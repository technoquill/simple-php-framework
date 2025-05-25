<?php
declare(strict_types=1);

namespace Technoquill\Framework\Contract;

interface ConfigInterface
{
    public function get(string $key, mixed $default = null): mixed;
}