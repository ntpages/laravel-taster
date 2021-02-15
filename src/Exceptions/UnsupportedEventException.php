<?php

namespace Ntpages\LaravelTaster\Exceptions;

use Ntpages\LaravelTaster\Services\TasterService;

class UnsupportedEventException extends AbstractTasterException
{
    public function __construct(string $key)
    {
        parent::__construct(sprintf("The frontend event '%s' is not supported. Available events: %s.", $key,
            join(', ', TasterService::JS_EVENTS)));
    }
}
