<?php

namespace Ntpages\LaravelTaster\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use Ntpages\LaravelTaster\Models\Interaction;
use Ntpages\LaravelTaster\Models\Variant;

class Interact
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Interaction
     */
    public $interaction;

    /**
     * @var Variant
     */
    public $variant;

    /**
     * @var int
     */
    public $moment;

    /**
     * Rendered constructor.
     * @param Interaction $interaction
     * @param Variant $variant
     */
    public function __construct(Interaction $interaction, Variant $variant)
    {
        $this->interaction = $interaction;
        $this->variant = $variant;
        $this->moment = time();
    }
}
