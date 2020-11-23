<?php

namespace Ntpages\LaravelTaster\Exceptions;

class ElementNotFoundException extends AbstractTasterException
{
    public function __construct(string $type, string $key)
    {
        parent::__construct("$type not found for the key '$key'");
    }
}
