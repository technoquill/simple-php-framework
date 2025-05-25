<?php
declare(strict_types=1);

namespace Technoquill\Framework\Router;

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
     * Adds a route with the specified HTTP method, path, and handler function to the routing table.
     *
     * @param string $method The HTTP method (e.g., GET, POST, etc.) associated with the route.
     * @param string $path The URI path for the route.
     * @param callable|array|string $handler The callback function to handle requests to the specified path and method.
     * @return RouteDefinition
     */
    public function add(string $method, string $path, callable|array|string $handler): RouteDefinition
    {
        $path = '/' . ltrim(trim($path), '/');
        $method = strtoupper($method);

        if (is_string($handler) && str_contains($handler, '@')) {
            [$class, $methodName] = explode('@', $handler, 2);

            if (!str_contains($class, '\\')) {
                $class = 'App\\Http\\Controllers\\' . $class;
            }
            $handler = [$class, $methodName];
        }

        $routeDefinition = new RouteDefinition($method, $path, $handler);
        $this->routes[] = $routeDefinition;

        return $routeDefinition;
    }


    public function routeRegister(): self
    {
        foreach ($this->routes as $routeDefinition) {
            /** @var RouteDefinition $routeDefinition */
            $definition = $routeDefinition->getDefinition();

            // name, pattern, middleware, theme
            $pattern = $this->convertToRegex($definition['path']);

            $route = [
                'pattern' => $pattern,
                'handler' => $definition['handler'],
                'path' => $definition['path'],
                'name' => $definition['name'],
                'theme' => $definition['theme'],
                'layout' => $definition['layout'],
                'middleware' => $definition['middleware'],
            ];

            $this->routes[$definition['method']][] = $route;

            // Cache named
            if ($definition['name']) {
                if (isset(self::$cachedNamedRoutes[$definition['name']])) {
                    throw new InvalidArgumentException("Route name [{$definition['name']}] already exists.");
                }
                self::$cachedNamedRoutes[$definition['name']] = $route;
            }
        }
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

    private function convertToRegex(string $path): string
    {
        return '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $path) . '$#';
    }


}