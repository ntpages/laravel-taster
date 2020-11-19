<?php

namespace Ntpages\LaravelTaster\Exceptions;

use Exception;

class WrongPortioningException extends Exception
{
    public function __construct()
    {
        parent::__construct("You're trying to pick value between uneven portions");
    }
}
