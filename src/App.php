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
use Technoquill\Framework\View\View;

/**
 * Represents the main application class.
 */
final class App
{

    public const VERSION = '0.4.1-dev';

    /** @var Container */
    private Container $container;


    protected array $middlewares = [
        ErrorHandlerMiddleware::class
    ];


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


        // Створюємо ланцюжок middleware навколо handler
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
            return null;
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
            $view = $this->container->get(View::class);
            $response = new Response(
                $view->render("errors/404", ['error' => 404]),
                404
            );
        }

        $response->send();
    }



//    public function run(): void
//    {
//        $resolver = new CallableResolver($this->container);
//
//        /** @var ErrorHandler $errorHandler */
//        $this->container->get(ErrorHandler::class)::register();
//
//        /** @var Request $request */
//        $request = $this->container->get(Request::class);
//
//
//        /** @var Router $router */
//        $router = $this->container->get(Router::class);
//
//        $method = $request->getMethod();
//        $uri = $request->getUri();
//
//        [$handler, $params] = $router->routeRegister()->dispatch($method, $uri, $request) ?? [null, []];
//
//
//        if (is_callable($handler)) {
//            $response = $resolver->call($handler, $params);
//
//        } elseif (is_array($handler) && is_string($handler[0]) && is_string($handler[1])) {
//            $instance = $this->container->get($handler[0]);
//            $method = $handler[1];
//
//            // if the controller has setParams — call (for web controllers)
//            if (method_exists($instance, 'setParams')) {
//                $instance->setParams($params);
//            }
//
//            // resolve callable Class method DI
//            $response = $resolver->call([$instance, $method], $params);
//        } else {
//            $view = $this->container->get(View::class);
//            $response = new Response($view->render("errors/404", [
//                'error' => 404
//            ]), 404);
//        }
//
//        if (!$response instanceof Response) {
//            $response = new Response((string)$response, 200);
//        }
//        $response->send();
//    }


}