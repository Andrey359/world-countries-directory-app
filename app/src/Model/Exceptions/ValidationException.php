<?php

namespace App\Model\Exceptions;

use Exception;

class ValidationException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message, 400);
    }
}
