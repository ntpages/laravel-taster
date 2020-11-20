<?php

namespace Ntpages\LaravelTaster\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property Collection|Interaction[] $interactions
 * @property Experiment|null $experiment
 * @property double $available_portion
 * @property double $portion
 * @method static features()
 */
class Variant extends AbstractModel
{
    /**
     * @var string
     */
    public $table = 'tsr_variants';

    /**
     * @var array
     */
    public $fillable = [
        'portion'
    ];

    /*
    |--------------------------------------------------------------------------
    | Lifecycle
    |--------------------------------------------------------------------------
    */

    public static function boot()
    {
        parent::boot();

        self::saving(function (Variant $variant) {
            // only applicable for grouped variants
            if ($variant->isFeature()) {
                return true;
            }

            // avoiding unnecessary check
            return $variant->portion != $variant->getOriginal('portion')
                ? $variant->portion <= $variant->available_portion
                : true;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function interactions()
    {
        return $this->belongsToMany(Interaction::class, 'tsr_interaction_variant')->withPivot('moment');
    }

    public function experiment()
    {
        return $this->belongsTo(Experiment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeFeatures(Builder $query)
    {
        return $query->whereNull('experiment_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessor
    |--------------------------------------------------------------------------
    */

    public function getAvailablePortionAttribute()
    {
        if ($this->isFeature()) {
            return 1;
        }

        return (double)static::selectRaw('IFNULL(1 - SUM(portion), 1) AS available')
            ->where('experiment_id', $this->attributes['experiment_id'])
            ->where('id', '<>', $this->id)
            ->value('available');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isFeature()
    {
        return (bool)$this->attributes['experiment_id'] ?? null;
    }
}
