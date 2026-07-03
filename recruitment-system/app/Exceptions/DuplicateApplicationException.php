<?php

namespace App\Exceptions;

use Exception;

class DuplicateApplicationException extends Exception
{
    public function __construct(string $message = 'You have already applied for this position.', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
