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
            return new class() {

                public string $name = '';
                public string $version = '';
                public string $url = '';
                public string $env = '';
                public string $charset = '';
                public string $lang = '';

                public function __construct()
                {
                    $this->name = config('app.app_name');
                    $this->version= config('app.app_version');
                    $this->url = config('app.app_url');
                    $this->env = config('app.app_env');
                    $this->charset = config('app.app_charset');
                    $this->lang = config('app.app_lang');
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
    function config(string $name): string
    {
        return container()->get(Config::class)->get($name);
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