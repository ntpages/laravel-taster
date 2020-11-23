<?php

namespace Ntpages\LaravelTaster\Exceptions;

class WrongPortioningException extends AbstractTasterException
{
    public function __construct()
    {
        parent::__construct("You're trying to pick value between uneven portions");
    }
}
