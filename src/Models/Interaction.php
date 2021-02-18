<?php

namespace Ntpages\LaravelTaster\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property string $key
 * @property string $name
 * @method static findOrFail(int $id): ?Interaction
 */
class Interaction extends Model
{
    /**
     * @var string
     */
    public $table = 'tsr_interactions';
}
