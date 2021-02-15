<?php

namespace Ntpages\LaravelTaster\Exceptions;

class WrongPortioningException extends AbstractTasterException
{
    public function __construct(float $availablePortion)
    {
        parent::__construct(
            sprintf("Variant portion is exceeding the allowed amount, available: %d", $availablePortion)
        );
    }
}
