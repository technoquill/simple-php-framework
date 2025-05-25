<?php
declare(strict_types=1);

namespace Technoquill\Framework\Router;

use Closure;

class RouteDefinition
{

    /** @var string  */
    protected string $method;

    /** @var string  */
    protected string $path;

    /** @var Closure|array|string  */
    protected Closure|array|string $handler;

    /** @var string|null  */
    protected ?string $name = null;

    /** @var string|null  */
    protected ?string $theme = null;

    /** @var string|null  */
    protected ?string $layout = null;

    /** @var array  */
    protected array $middleware = [];

    /**
     * Constructor for initializing the object with HTTP method, path, and handler.
     *
     * @param string $method The HTTP method (e.g., GET, POST).
     * @param string $path The request path or route.
     * @param mixed $handler The handler function or callable for the request.
     *
     * @return void
     */
    public function __construct(string $method, string $path, Closure|array|string $handler)
    {
        $this->method = $method;
        $this->path = $path;
        $this->handler = $handler;
    }

    /**
     * Sets the name property and returns the current instance.
     *
     * @param string $name The name to set.
     *
     * @return self The current instance for method chaining.
     */
    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Sets the theme for the object and returns the current instance.
     *
     * @param string $theme The theme name or identifier to be assigned.
     *
     * @return self Returns the current instance after setting the theme.
     */
    public function theme(string $theme): self
    {
        $this->theme = $theme;
        return $this;
    }

    /**
     * @param string $layout
     * @return $this
     */
    public function layout(string $layout): self
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * Sets middleware for the current instance.
     *
     * @param mixed $middleware A single middleware or an array of middleware to be applied.
     *
     * @return self Returns the current instance for method chaining.
     */
    public function middleware(mixed $middleware): self
    {
        $this->middleware = is_array($middleware) ? $middleware : [$middleware];
        return $this;
    }



    /**
     * Retrieves the definition of the current object as an associative array.
     *
     * @return array An array containing the method, path, handler, name, theme, and middleware properties.
     */
    public function getDefinition(): array
    {
        return [
            'method' => $this->method,
            'path' => $this->path,
            'handler' => $this->handler,
            'name' => $this->name,
            'theme' => $this->theme,
            'layout' => $this->layout,
            'middleware' => $this->middleware,
        ];
    }

}