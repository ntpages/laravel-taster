<?php

namespace Ntpages\LaravelTaster\Models;

use Illuminate\Database\Eloquent\Collection;

/**
 * @property Collection|Variant[] $variants
 */
class Experiment extends AbstractModel
{
    /**
     * @var string
     */
    public $table = 'tsr_experiments';

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function variants()
    {
        return $this->hasMany(Variant::class);
    }
}
