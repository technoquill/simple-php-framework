<?php
declare(strict_types=1);

namespace Technoquill\Framework\Http\Middleware;

use Technoquill\Framework\Contract\MiddlewareInterface;
use Technoquill\Framework\Http\Request;
use Technoquill\Framework\Http\Response;

class Middleware implements MiddlewareInterface
{

    public function handle(Request $request, callable $next): Response
    {
        // TODO: Implement handle() method.
    }
}