<?php

namespace App\Exceptions;

use Exception;

class PaymentsNotConfiguredException extends Exception
{
    public function __construct(string $message = 'Payment gateway is not configured for this boutique.')
    {
        parent::__construct($message);
    }
}
