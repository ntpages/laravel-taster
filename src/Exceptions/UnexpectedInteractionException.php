<?php

namespace Ntpages\LaravelTaster\Exceptions;

use Exception;

class UnexpectedInteractionException extends Exception
{
    public function __construct()
    {
        parent::__construct('The [@interact, interact()] is intended to be used within @variant or @feature');
    }
}
