<?php

namespace App\Exceptions;

use Exception;

class PaystackTimeoutException extends Exception
{
    public function __construct(string $message = 'Connection to Paystack timed out.', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
