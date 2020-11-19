<?php

namespace Ntpages\LaravelTaster\Exceptions;

use Exception;

class InteractionNotFoundException extends Exception
{
    public function __construct(string $key)
    {
        parent::__construct("Interaction '$key' wasn't found");
    }
}
