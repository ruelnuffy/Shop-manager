<?php

namespace App\Exceptions;

use Exception;

class PaystackRejectionException extends Exception
{
    public function __construct(string $message = 'Transfer rejected by Paystack (4xx).', int $code = 400, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
