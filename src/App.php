<?php
declare(strict_types=1);

namespace Technoquill\Framework;

use ReflectionException;
use Technoquill\Framework\Container\Container;
use Technoquill\Framework\Error\ErrorHandler;
use Technoquill\Framework\Http\Request;
use Technoquill\Framework\Http\Response;
use Technoquill\Framework\Middleware\ErrorHandlerMiddleware;
use Technoquill\Framework\Router\Router;
use Technoquill\Framework\Support\Resolver\CallableResolver;
use Technoquill\Framework\Support\Traits\HasHttpResponses;

/**
 * Represents the main application class.
 */
final class App
{

    /** @var string  */
    public const VERSION = '0.4.2-dev';

    /** @var Container */
    private Container $container;


    /** @var array|class-string[]  */
    protected array $middlewares = [
        ErrorHandlerMiddleware::class
    ];

    use HasHttpResponses;


    /**
     * App constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Runs the application.
     * @throws ReflectionException
     */

    public function run(): void
    {
        $resolver = new CallableResolver($this->container);

        $this->container->get(ErrorHandler::class)::register();

        /** @var Request $request */
        $request = $this->container->get(Request::class);

        /** @var Router $router */
        $router = $this->container->get(Router::class);

        $method = $request->getMethod();
        $uri = $request->getUri();

        [$handler, $middlewares, $params] = $router->routeRegister()->dispatch($method, $uri) ?? [null, [], []];

        $middlewares = array_merge($this->middlewares, $middlewares);


        // Create a middleware chain around the handler
        $coreHandler = function() use ($handler, $params, $resolver) {
            if (is_callable($handler)) {
                return $resolver->call($handler, $params);
            }
            if (is_array($handler) && is_string($handler[0]) && is_string($handler[1])) {
                $instance = $this->container->get($handler[0]);
                $method = $handler[1];
                if (method_exists($instance, 'setParams')) {
                    $instance->setParams($params);
                }
                return $resolver->call([$instance, $method], $params);
            }
            // if dispatch result [null, [], []]
            return $this->notFound();
        };

        // Wrap in middleware
        foreach (array_reverse($middlewares) as $middlewareClass) {
            $prevHandler = $coreHandler;
            $coreHandler = function($request) use ($middlewareClass, $prevHandler) {
                // Add Middleware to the container
                $middleware = class_exists($middlewareClass)
                    ? $this->container->get($middlewareClass)
                    : new $middlewareClass();
                return $middleware->handle($request, $prevHandler);
            };
        }

        // Run the chain depends on the handler
        $response = $coreHandler($request);

        if (!$response instanceof Response) {
            $response = $this->notFound();
        }

        $response->send();
    }


}