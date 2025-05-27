<?php
declare(strict_types=1);

namespace Technoquill\Framework\Support\Traits;


use Technoquill\Framework\Http\Response;

trait HasHttpResponses
{


    /**
     * @param string $message
     * @param int $status
     * @return Response
     */
    protected function errorResponse(string $message = 'Something went wrong.', int $status = 500): Response
    {
        return new Response(
            view()->render("errors/errors", [
                'error' => $status,
                'message' => $message
            ]),
            $status
        );
    }

    /**
     * @param string $message
     * @return Response
     */
    protected function notFound(string $message = 'Page not found'): Response
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * @param string $message
     * @return Response
     */
    protected function forbidden(string $message = 'Forbidden'): Response
    {
        return $this->errorResponse($message, 403);
    }


    /**
     * @param string $url
     * @param int $status
     * @return Response
     */
    protected function redirect(string $url, int $status = 302): Response
    {
        return (new Response(''))->setHeader('Location', $url)->setStatus($status);
    }
}