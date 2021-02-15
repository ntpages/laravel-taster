<?php

namespace Ntpages\LaravelTaster\Observers;

use Ntpages\LaravelTaster\Exceptions\WrongPortioningException;
use Ntpages\LaravelTaster\Models\Variant;

class VariantObserver
{
    /**
     * @param Variant $variant
     * @throws WrongPortioningException
     */
    public function creating(Variant $variant)
    {
        $this->checkPortion($variant);
    }

    /**
     * @param Variant $variant
     * @throws WrongPortioningException
     */
    public function updating(Variant $variant)
    {
        $this->checkPortion($variant);
    }

    /**
     * @param Variant $variant
     * @throws WrongPortioningException
     */
    private function checkPortion(Variant $variant)
    {
        if ($variant->availablePortion > $variant->portion) {
            throw new WrongPortioningException($variant->availablePortion);
        }
    }
}
