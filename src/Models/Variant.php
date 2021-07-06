<?php

namespace Ntpages\LaravelTaster\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property string $key
 * @property string $name
 * @property float $portion
 * @property float $availablePortion
 * @property Experiment $experiment
 * @property Collection|Variant[] $siblings
 * @method static findOrFail(int $id): ?Variant
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

    public function siblings(): HasMany
    {
        return $this->hasMany(self::class, 'experiment_id', 'experiment_id')->where('id', '!=', $this->id);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getAvailablePortionAttribute(): float
    {
        return 1 - ($this->siblings()->pluck('portion')->sum() ?: .1);
    }
}
