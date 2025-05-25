<?php
declare(strict_types=1);

namespace Technoquill\Framework\Contract;

interface ContainerInterface
{
    public function get(string $id);

    public function set(string $id, callable $concrete): void;

}