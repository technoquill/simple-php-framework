<?php
declare(strict_types=1);

namespace Technoquill\Framework\Router;

use DeepCopy\Exception\PropertyException;
use RuntimeException;
use InvalidArgumentException;
use Technoquill\Framework\Support\Traits\Macroable;

/**
 * Handles the registration and dispatching of routes in an application.
 *
 * @method RouteDefinition get(string $path, callable $handler)
 * @method RouteDefinition post(string $path, callable $handler)
 * @method RouteDefinition put(string $path, callable $handler)
 * @method RouteDefinition patch(string $path, callable $handler)
 * @method RouteDefinition delete(string $path, callable $handler)
 * @method RouteDefinition options(string $path, callable $handler)
 * @method RouteDefinition head(string $path, callable $handler)
 */
final class Router
{
    /** @var array */
    protected array $routes = [];

    /** @var array */
    protected static array $cachedNamedRoutes = [];

    /** @var array  */
    protected array $groupStack = [];

    /** @var array  */
    protected array $routeDefinition = [];

    /** @var string[] */
    protected const HTTP_REQUEST_METHODS = [
        'get' => 'GET',
        'post' => 'POST',
        'put' => 'PUT',
        'patch' => 'PATCH',
        'delete' => 'DELETE',
        'options' => 'OPTIONS',
        'head' => 'HEAD',
    ];

    use Macroable;


    /**
     * @return void
     */
    public function __construct()
    {
        $this->bindRouteMethods();
    }

    /**
     * Binds the HTTP request methods to the add() method.
     *
     * @return void
     */
    private function bindRouteMethods(): void
    {
        foreach (self::HTTP_REQUEST_METHODS as $key => $method) {
            self::macro($key, function ($path, $handler) use ($method) {
                return $this->add($method, $path, $handler);
            });
        }
    }

    /**
     * @param string $name
     * @param array $params
     * @return string
     */
    public function to(string $name, array $params = []): string
    {
        if (!isset(self::$cachedNamedRoutes[$name])) {
            throw new RuntimeException("Route $name not found");
        }
        $pattern = self::$cachedNamedRoutes[$name]['path'];
        foreach ($params as $key => $value) {
            $pattern = str_replace('{' . $key . '}', $value, $pattern);
        }
        return $pattern;
    }


    /**
     * @param array $options
     * @param callable $callback
     * @throws PropertyException
     */
    public function group(array $options, callable $callback): void
    {
        if(!array_key_exists('prefix', $options)) {
            throw new PropertyException('You need to init $options[prefix] key value');
        }
        $this->groupStack[] = $options;
        $callback($this);
        array_pop($this->groupStack);

    }


    /**
     * @param string $path
     * @return string
     */
    protected function buildFullPath(string $path): string
    {
        $prefix = '';
        foreach ($this->groupStack as $group) {
            if (!empty($group['prefix'])) {
                $prefix .= '/' . trim($group['prefix'], '/');
            }
        }
        $fullPath = $prefix . '/' . ltrim($path, '/');
        return '/' . ltrim($fullPath, '/'); // Захист від зайвих слешів
    }

    /**
     * @return array
     */
    protected function getGroupOptions(): array
    {
        $merged = [
            'middleware' => [],
        ];

        foreach ($this->groupStack as $group) {
            // Merge middleware as an array
            if (isset($group['middleware'])) {
                $merged['middleware'] = array_merge($merged['middleware'], (array)$group['middleware']);
            }
            // Other keys (override)
            foreach ($group as $key => $value) {
                if ($key === 'middleware') {
                    continue;
                }
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * Adds a route with the specified HTTP method, path, and handler function to the routing table.
     *
     * @param string $method The HTTP method (e.g., GET, POST, etc.) associated with the route.
     * @param string $path The URI path for the route.
     * @param callable|array|string $handler The callback function to handle requests to the specified path and method.
     * @return RouteDefinition
     */
    public function add(string $method, string $path, callable|array|string $handler): RouteDefinition
    {
        $groupOptions = $this->getGroupOptions();
        $path = $this->buildFullPath($path); //(isset($options['prefix']) ? rtrim($options['prefix'], '/') : '') . '/' . ltrim($path, '/');

        $method = strtoupper($method);

        if (is_string($handler) && str_contains($handler, '@')) {
            [$class, $methodName] = explode('@', $handler, 2);
            if (!str_contains($class, '\\')) {
                $class = 'App\\Http\\Controllers\\' . $class;
            }
            $handler = [$class, $methodName];
        }

        $routeDefinition = new RouteDefinition($method, $path, $handler);

        if (!empty($groupOptions['middleware'])) {
            $routeDefinition->middleware($groupOptions['middleware']);
        }
//        if (!empty($options['name'])) {
//            $routeDefinition->name($options['name']);
//        }

        $this->routeDefinition[] = $routeDefinition;

        return $routeDefinition;
    }


    /**
     * Registers and organizes the application's route definitions into a structured format
     * for efficient matching and handling of requests. It converts route paths to regular
     * expressions, caches named routes, and ensures no duplicate route names exist.
     *
     * @return self Returns the current instance for method chaining.
     */
    public function routeRegister(): self
    {
        foreach ($this->routeDefinition as $routeDefinition) {
            /** @var RouteDefinition $routeDefinition */
            $route = [
                'pattern' => $this->convertToRegex($routeDefinition->get('path')),
                'method' => $routeDefinition->get('method'),
                'path' => $routeDefinition->get('path'),
                'handler' => $routeDefinition->get('handler'),
                'name' => $routeDefinition->get('name'),
                'middleware' => $routeDefinition->get('middleware')
            ];
            $this->routes[$routeDefinition->get('method')][] = $route;
            // Cached by name
            if ($routeDefinition->get('name')) {
                if (isset(self::$cachedNamedRoutes[$routeDefinition->get('name')])) {
                    throw new InvalidArgumentException("Route name [{$routeDefinition->get('name')}] already exists.");
                }
                self::$cachedNamedRoutes[$routeDefinition->get('name')] = $route;
            }
        }

//        dump($this->routes);
//        dd(self::$cachedNamedRoutes);

        return $this;
    }


    /**
     * Dispatches a route based on the provided HTTP method and path.
     *
     * @param string $method The HTTP method to match, such as GET, POST, etc.
     * @param string $uri The path to match against the registered routes.
     * @return array|null The route handler if a matching route is found, or null if no match exists.
     */
    public function dispatch(string $method, string $uri): ?array
    {
        $method = strtoupper($method);
        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $route) {
            if (preg_match($route['pattern'], $uri, $matches)) {
                // Discard numeric keys, leave only named ones
                $params = array_filter(
                    $matches,
                    static fn($k) => !is_int($k),
                    ARRAY_FILTER_USE_KEY
                );
                return [$route['handler'], $params];
            }
        }
        return null;
    }

    /**
     * Converts a given path string into a regular expression pattern, where placeholders
     * enclosed in curly braces are transformed into named capturing groups.
     *
     * @param string $path The input path containing placeholders to be converted.
     * @return string The resulting regular expression pattern.
     */
    private function convertToRegex(string $path): string
    {
        return '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $path) . '$#';
    }


}