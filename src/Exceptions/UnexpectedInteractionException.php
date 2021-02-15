<?php

namespace Ntpages\LaravelTaster\Exceptions;

class UnexpectedInteractionException extends AbstractTasterException
{
    public function __construct()
    {
        parent::__construct('The interaction should be used inside of a variant statement');
    }
}
