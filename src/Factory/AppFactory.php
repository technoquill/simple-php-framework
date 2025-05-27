<?php
declare(strict_types=1);

namespace Technoquill\Framework\Factory;


use ReflectionException;
use Technoquill\Framework\App;
use Technoquill\Framework\Asset\Asset;
use Technoquill\Framework\Config\Config;
use Technoquill\Framework\Container\Container;
use Technoquill\Framework\Error\ErrorHandler;
use Technoquill\Framework\Event\EventDispatcher;
use Technoquill\Framework\Http\Request;
use Technoquill\Framework\Http\Response;
use Technoquill\Framework\Logger\Logger;
use Technoquill\Framework\Router\Router;
use Technoquill\Framework\Support\Resolver\TemplateResolver;
use Technoquill\Framework\View\View;
use Technoquill\Framework\View\ViewContext;

/**
 * Factory class responsible for creating and configuring an instance of the application.
 */
final class AppFactory
{

    /**
     * Creates and returns an instance of the App class with a pre-configured container,
     * registering core services such as Router, Request, Response, ErrorHandler, Logger,
     * EventDispatcher, and Config.
     *
     * @return App Instance of the App class with the configured service container.
     * @throws ReflectionException
     */
    public static function create(): App
    {
        $container = new Container();

        $container->set(Config::class, fn() => new Config(
            require base_path() . '/config/config.php')
        );
        /** @var Config $config */
        $config = $container->get(Config::class);

        // add base services to container
        $container->set(Request::class, fn() => Request::createFromGlobals());
        $container->set(Response::class, fn() => new Response());
        $container->set(Router::class, fn() => new Router());
        $container->set(ErrorHandler::class, fn() => new ErrorHandler());
        $container->set(Logger::class, fn() => new Logger());
        $container->set(EventDispatcher::class, fn() => new EventDispatcher());
        $container->set(TemplateResolver::class, fn() => new TemplateResolver(
            config: $config, request: $container->get(Request::class)
        ));
        $container->set(View::class, fn() => new View(
            $container->get(TemplateResolver::class)
        ));
        $container->set(Asset::class, fn() => new Asset(view: $container->get(View::class)));

        /** @var Router  $router */
        $router = $container->get(Router::class);
        require base_path() . '/routes/web.php';

        $services = $config->get('services', []);
        foreach ($services as $service) {
            $serviceProvider = new($service);
            $serviceProvider->boot($container);
            $serviceProvider->register($container);
        }

        Container::setInstance($container);

        if(file_exists(base_path() . '/src/Support/App/functions.php')) {
            require base_path() . '/src/Support/App/functions.php';
        }

        // Return App and pass to its constructor the container
        return new App($container);
    }

}