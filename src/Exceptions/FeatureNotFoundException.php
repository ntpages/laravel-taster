<?php

namespace Ntpages\LaravelTaster\Exceptions;

use Exception;

class FeatureNotFoundException extends Exception
{
    public function __construct(string $key)
    {
        parent::__construct("Feature '$key' wasn't found");
    }
}
