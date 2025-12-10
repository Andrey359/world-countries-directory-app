<?php

namespace App\Model\Exceptions;

use Exception;

class ОшибкаНевернымКодаМ extends Exception
{
    public function __construct(string $код)
    {
        parent::__construct("Неверный код страны: '{$код}'", 400);
    }
}
