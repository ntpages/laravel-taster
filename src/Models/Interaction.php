<?php

namespace Ntpages\LaravelTaster\Models;

use DateTimeInterface;

/**
 * @property DateTimeInterface $moment
 */
class Interaction extends AbstractModel
{
    /**
     * @var string
     */
    public $table = 'tsr_interactions';

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getValidationRules()
    {
        return [
            'key' => "required|alpha_dash|max:50|unique:{$this->table},key,{$this->id}"
        ];
    }
}
