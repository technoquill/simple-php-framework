<?php
declare(strict_types=1);


use Technoquill\Framework\Asset\Asset;
use Technoquill\Framework\Config\Config;
use Technoquill\Framework\Container\Container;
use Technoquill\Framework\Exceptions\NotFoundException;
use Technoquill\Framework\Router\Router;
use Technoquill\Framework\View\View;


if (!function_exists('container')) {
    function container(): Container
    {
        return Container::getInstance();
    }
}

if (!function_exists('app')) {
    /**
     * @return object
     * @todo must return App::class (Kernel instead of App, App to src/App)
     */
    function app(): object
    {
        try {
            return new class(config()->get('app')) {
                public string $name = '';
                public string $version = '';
                public string $url = '';
                public string $env = '';
                public string $charset = '';
                public string $lang = '';
                public function __construct(array $appConfig)
                {
                    $this->name = $appConfig['app_name'];
                    $this->version= $appConfig['app_version'];
                    $this->url = $appConfig['app_url'];
                    $this->env = $appConfig['app_env'];
                    $this->charset = $appConfig['app_charset'];
                    $this->lang = $appConfig['app_lang'];
                }
            };

        } catch (ReflectionException $e) {
        }
    }
}

if (!function_exists('route')) {
    /**
     * @param string $name
     * @param array $params
     * @return string
     */
    function route(string $name, array $params = []): string
    {
        try {
            /** @var Router $router */
            $router = container()->get(Router::class);
            return $router->to($name, $params);
        } catch (NotFoundException|ReflectionException $exception) {
        }

    }
}


if (!function_exists('config')) {
    /**
     * @throws ReflectionException
     */
    function config(): Config
    {
        return container()->get(Config::class);
    }
}


if (!function_exists('view')) {
    /**
     * @throws ReflectionException
     */
    function view(): View
    {
        return container()->get(View::class);
    }
}


if (!function_exists('asset')) {
    /**
     * @return Asset
     */
    function asset(): Asset
    {
        try {
            return container()->get(Asset::class);
        } catch (ReflectionException $e) {
        }
    }
}