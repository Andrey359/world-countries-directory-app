<?php

namespace App\Model\Exceptions;

use Exception;

class ОшибкаДублированныхСтран extends Exception
{
    public function __construct(string $поле, string $значение)
    {
        parent::__construct("Страна с {$поле} '{$значение}' уже существует", 409);
    }
}
