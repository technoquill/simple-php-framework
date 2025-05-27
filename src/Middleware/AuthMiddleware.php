<?php
declare(strict_types=1);

namespace Technoquill\Framework\Middleware;

use Technoquill\Framework\Contract\MiddlewareInterface;
use Technoquill\Framework\Http\Request;
use Technoquill\Framework\Http\Response;
use Technoquill\Framework\Support\Traits\HasHttpResponses;

class AuthMiddleware implements MiddlewareInterface
{


    use HasHttpResponses;

    /**
     * @param Request $request
     * @param callable $next
     * @return Response
     */
    public function handle(Request $request, callable $next): Response
    {
        if ($request->getUri() !== '/license') {
            return $this->redirect(route('home.index'));
        }
        return $next($request);
    }
}