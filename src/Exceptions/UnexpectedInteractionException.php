<?php

namespace Ntpages\LaravelTaster\Exceptions;

class UnexpectedInteractionException extends AbstractTasterException
{
    public function __construct()
    {
        parent::__construct('The [@interact, interact()] is intended to be used within @variant or @feature');
    }
}
