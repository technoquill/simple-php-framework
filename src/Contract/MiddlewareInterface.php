<?php
declare(strict_types=1);

namespace Technoquill\Framework\Contract;

use Technoquill\Framework\Http\Request;
use Technoquill\Framework\Http\Response;

interface MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response;

}