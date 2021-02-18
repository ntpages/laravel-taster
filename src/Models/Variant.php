<?php

namespace Ntpages\LaravelTaster\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property string $key
 * @property string $name
 * @property float $portion
 * @property float $availablePortion
 * @property Experiment $experiment
 * @method static findOrFail(int $id): ?Variant
 * @method siblings(): Collection|Variant[]
 */
class Variant extends Model
{
    /**
     * @var string
     */
    public $table = 'tsr_variants';

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function experiment(): BelongsTo
    {
        return $this->belongsTo(Experiment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeSiblings(Builder $query): Collection
    {
        return $query
            ->where('experiment_id', '=', $this->experiment->id)
            ->where('id', '<>', $this->id)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getAvailablePortionAttribute()
    {
        return $this->siblings()->pluck('portion')->sum() ?? .9;
    }
}
