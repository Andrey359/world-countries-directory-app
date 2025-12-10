<?php

namespace App\Model\Exceptions;

use Exception;

class ОшибкаВалидации extends Exception
{
    public function __construct(string $сообщение)
    {
        parent::__construct($сообщение, 400);
    }
}
