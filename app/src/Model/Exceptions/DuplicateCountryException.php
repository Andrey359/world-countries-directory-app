<?php

namespace App\Model\Exceptions;

use Exception;

class DuplicateCountryException extends Exception
{
    public function __construct(string $field, string $value)
    {
        parent::__construct("Country with {$field} '{$value}' already exists", 409);
    }
}
