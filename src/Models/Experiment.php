<?php

namespace Ntpages\LaravelTaster\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property string $key
 * @property string $name
 *
 * @property Collection|Variant[] $variants
 */
class Experiment extends Model
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

    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class);
    }
}
