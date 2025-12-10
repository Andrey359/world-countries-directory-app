<?php

namespace App\Model\Exceptions;

use Exception;

class InvalidCountryCodeException extends Exception
{
    public function __construct(string $code = '')
    {
        $message = empty($code) ? 'Invalid country code format' : "Invalid country code: '{$code}'";
        parent::__construct($message, 400);
    }
}
