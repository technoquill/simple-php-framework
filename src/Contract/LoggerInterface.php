<?php
declare(strict_types=1);

namespace Technoquill\Framework\Contract;

interface LoggerInterface
{
    public function log(string $level, string $message, array $context = []): void;
}