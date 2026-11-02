<?php

namespace App\Exceptions;

use Exception;

class PaystackServerException extends Exception
{
    public function __construct(string $message = 'Paystack server returned an error (5xx).', int $code = 500, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
