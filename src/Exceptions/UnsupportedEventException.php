<?php

namespace Ntpages\LaravelTaster\Exceptions;

use Exception;

class UnsupportedEventException extends Exception
{
    public function __construct(string $key)
    {
        parent::__construct("The frontend event '$key' is not supported");
    }
}
