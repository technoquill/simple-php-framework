<?php
declare(strict_types=1);

namespace Technoquill\Framework\Middleware;

use Technoquill\Framework\Contract\MiddlewareInterface;
use Technoquill\Framework\Http\Request;
use Technoquill\Framework\Http\Response;
use Technoquill\Framework\View\View;
use Throwable;

/**
 * Middleware responsible for handling exceptions during the processing
 * of a request and returning a consistent error response.
 *
 * This class implements the MiddlewareInterface and ensures that any
 * unexpected exceptions that occur during the execution of the middleware
 * pipeline are caught and handled gracefully by returning an appropriate
 * error response.
 */
class ErrorHandlerMiddleware implements MiddlewareInterface
{
    public function __construct(private ?View $view = null) {}

    public function handle(Request $request, callable $next): Response
    {
        try {
            return $next($request);
        } catch (Throwable $e) {
            $status = $e->getCode();
            if ($status < 400 || $status > 599) {
                $status = 500;
            }
            $message = (getenv('APP_DEBUG') === 'true') ? $e->getMessage() : 'Internal Server Error';
            $content = $this->view
                ? $this->view->render('errors/' . $status, ['error' => $status, 'message' => $message])
                : $message;
            return new Response($content, $status);
        }
    }
}