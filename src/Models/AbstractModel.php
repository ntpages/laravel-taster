<?php

namespace Ntpages\LaravelTaster\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property string $key
 */
abstract class AbstractModel extends Model
{
    /**
     * @return array
     */
    abstract public function getValidationRules();
}
