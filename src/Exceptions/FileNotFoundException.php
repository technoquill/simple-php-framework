<?php
declare(strict_types=1);

namespace Technoquill\Framework\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Represents an exception thrown when a specified file is not found.
 *
 * This exception can be used to indicate that an operation requiring a file
 * has failed due to the absence of the specified file. It provides details
 * about the missing file, as well as optional exception code and chaining.
 */
class FileNotFoundException extends RuntimeException
{

    /**
     * Constructor method to initialize the exception.
     *
     * @param string $file The name of the file that was not found.
     * @param int $code An optional exception code.
     * @param Throwable|null $previous An optional previous exception for exception chaining.
     *
     * @return void
     */
    public function __construct(string $file, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("File not found: [$file]", $code, $previous);
    }
}