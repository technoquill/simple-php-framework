<?php
declare(strict_types=1);

namespace Technoquill\Framework\Provider;

use Technoquill\Framework\Contract\ServiceProviderInterface;
use Technoquill\Framework\Container\Container;

/**
 * Provides service registration and bootstrapping functionality for dependency injection containers.
 * Implements methods required by ServiceProviderInterface to register services and initialize resources.
 */
class ServiceProvider implements ServiceProviderInterface
{

    /**
     * Registers services or definitions in the provided container.
     *
     * @param Container $container The container instance where services or definitions are registered.
     * @return void
     */
    public function register(Container $container): void
    {
        // $container->set(Class::class, fn() => new Class(...));
        // etc.
    }

    /**
     * Executes additional initialization processes such as configuring events, aliases, or other setup.
     *
     * @param Container $container The container instance used for performing initialization tasks.
     * @return void
     */
    public function boot(Container $container): void
    {
        // Additional initialization if needed (events, aliases...)
    }
}