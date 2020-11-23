<?php

namespace Ntpages\LaravelTaster\Exceptions;

class UnexpectedVariantException extends AbstractTasterException
{
    public function __construct(string $key)
    {
        parent::__construct("The variant '$key' should be used inside of an experiment");
    }
}
