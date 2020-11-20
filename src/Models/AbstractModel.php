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
    public function getValidationRules()
    {
        return [
            'key' => "required|alpha_dash|max:50|unique:{$this->table},key,{$this->id}"
        ];
    }

    /**
     * Makes sure that common columns are in the fallible attribute
     * @return array
     */
    public function getFillable()
    {
        $this->fillable[] = 'key';

        return $this->fillable;
    }
}
