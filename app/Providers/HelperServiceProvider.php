<?php
declare(strict_types=1);

namespace App\Providers;

use ReflectionException;
use Technoquill\Framework\Config\Config;
use Technoquill\Framework\Container\Container;
use Technoquill\Framework\Provider\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * @throws ReflectionException
     */
    public function boot(Container $container): void
    {
        $config = $container->get(Config::class);
        $helpersPath = APP_BASE_PATH . '/helpers';

        foreach (glob($helpersPath . '/*.php') as $file) {
            require_once $file;
        }
    }

}