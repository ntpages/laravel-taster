<?php

namespace Ntpages\LaravelTaster\Exceptions;

use Exception;

class UnexpectedVariantException extends Exception
{
    public function __construct(string $key)
    {
        parent::__construct("The variant '$key' should be used inside of an experiment");
    }
}
