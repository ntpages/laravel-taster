<?php

namespace Ntpages\LaravelTaster\Exceptions;

use Exception;

class VariantNotFoundException extends Exception
{
    public function __construct(string $experimentKey, string $variantKey)
    {
        parent::__construct("Variant '$variantKey' wasn't found in experiment '$experimentKey'");
    }
}
