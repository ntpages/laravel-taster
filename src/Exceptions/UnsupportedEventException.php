<?php

namespace Ntpages\LaravelTaster\Exceptions;

class UnsupportedEventException extends AbstractTasterException
{
    public function __construct(string $key)
    {
        parent::__construct("The frontend event '$key' is not supported");
    }
}
