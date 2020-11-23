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
     * Shortcut for ease searching
     * @param string $key
     * @return $this|null
     */
    public static function findByKey(string $key)
    {
        return self::where('key', $key)->first();
    }

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
        if (!in_array('key', $this->fillable)) {
            $this->fillable[] = 'key';
        }

        return $this->fillable;
    }

    /**
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'key';
    }
}
