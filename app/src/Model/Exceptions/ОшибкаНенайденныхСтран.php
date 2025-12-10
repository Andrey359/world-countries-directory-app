<?php

namespace App\Model\Exceptions;

use Exception;

class ОшибкаНенайденныхСтран extends Exception
{
    public function __construct(string $код)
    {
        parent::__construct("Страна с кодом '{$код}' не найдена", 404);
    }
}
