<?php
declare(strict_types=1);

namespace Technoquill\Framework\Contract;

interface EventInterface
{
    public function getName(): string;
}