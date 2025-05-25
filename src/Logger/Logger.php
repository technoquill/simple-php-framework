<?php
declare(strict_types=1);

namespace Technoquill\Framework\Logger;

use Technoquill\Framework\Contract\LoggerInterface;

class Logger implements LoggerInterface
{

    public function log(string $level, string $message, array $context = []): void
    {
        // TODO: Implement log() method.
    }
}