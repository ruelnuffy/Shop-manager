<?php

namespace App\Exceptions;

use RuntimeException;

class TenantNotResolvedException extends RuntimeException
{
    public function __construct(string $message = 'No active tenant is set for this tenant-scoped operation in an HTTP request.')
    {
        parent::__construct($message);
    }
}
