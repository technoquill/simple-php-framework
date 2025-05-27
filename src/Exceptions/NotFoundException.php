<?php
declare(strict_types=1);

namespace Technoquill\Framework\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Represents an exception thrown when a requested resource is not found.
 *
 * This exception typically indicates a 404 error, commonly used in web applications
 * to signal that the server cannot find the requested resource.
 *
 * The default message is "404 Not Found", and the default error code is 404.
 *
 * @param string $message Custom error message for the exception.
 * @param int $code Custom error code for the exception.
 * @param Throwable|null $previous Optional previous exception for chaining.
 */
class NotFoundException extends RuntimeException
{

    public function __construct(string $message = '404 Not Found', int $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}