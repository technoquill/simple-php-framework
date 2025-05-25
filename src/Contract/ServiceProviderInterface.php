<?php
declare(strict_types=1);

namespace Technoquill\Framework\Contract;

use Technoquill\Framework\Container\Container;


interface ServiceProviderInterface
{
    public function register(Container $container): void;

    public function boot(Container $container): void;

}