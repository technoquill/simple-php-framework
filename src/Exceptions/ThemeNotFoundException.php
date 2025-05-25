<?php
declare(strict_types=1);

namespace Technoquill\Framework\Exceptions;


use RuntimeException;
use Throwable;


/**
 * Exception thrown when a specified theme cannot be found.
 *
 * This exception is typically used to indicate that an attempt to access
 * or load a theme by its identifier has failed due to the theme being
 * nonexistent or unavailable.
 *
 * @extends RuntimeException
 *
 * @param string $theme The name or identifier of the theme that was not found.
 * @param int $code An optional exception code.
 * @param Throwable|null $previous An optional previous exception for chaining.
 */
class ThemeNotFoundException extends RuntimeException
{
    public function __construct(string $message, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}