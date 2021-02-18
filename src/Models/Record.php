<?php

namespace Ntpages\LaravelTaster\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use DateTime;

/**
 * @property Interaction $interaction
 * @property Variant $variant
 * @property DateTime $moment
 * @property string $uuid
 * @property string $url
 */
class Record extends Pivot
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    public $table = 'tsr_records';

    /**
     * @var string[]
     */
    public $fillable = [
        'moment',
        'uuid',
        'url'
    ];

    /**
     * @var string[]
     */
    public $dates = [
        'moment'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function interaction(): BelongsTo
    {
        return $this->belongsTo(Interaction::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }
}
