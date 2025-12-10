<?php

namespace App\Model\Exceptions;

use Exception;

class CountryNotFoundException extends Exception
{
    public function __construct(string $code)
    {
        parent::__construct("Country with code '{$code}' not found", 404);
    }
}