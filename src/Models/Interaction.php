<?php

namespace Ntpages\LaravelTaster\Models;

use DateTimeInterface;

/**
 * @property DateTimeInterface $moment
 */
class Interaction extends AbstractModel
{
    /**
     * @var string
     */
    public $table = 'tsr_interactions';
}
