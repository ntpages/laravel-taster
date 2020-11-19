<?php

namespace Ntpages\LaravelTaster\Exceptions;

use Exception;

class ExperimentNotFoundException extends Exception
{
    public function __construct(string $key)
    {
        parent::__construct("Experiment '$key' wasn't found");
    }
}
