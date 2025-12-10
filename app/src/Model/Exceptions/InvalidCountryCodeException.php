<?php

namespace App\Model\Exceptions;

use Exception;

class InvalidCountryCodeException extends Exception
{
    public function __construct(string $code)
    {
        parent::__construct("Invalid country code: '{$code}'", 400);
    }
}