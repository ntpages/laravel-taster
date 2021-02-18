<?php

namespace Ntpages\LaravelTaster\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use Ntpages\LaravelTaster\Models\Interaction;
use Ntpages\LaravelTaster\Models\Variant;

class Interact
{
    use Dispatchable, SerializesModels;

    public Interaction $interaction;
    public Variant $variant;
    public int $moment;
    public string $uuid;
    public string $url;

    public function __construct(Interaction $interaction, Variant $variant, string $uuid, string $url = '')
    {
        $this->interaction = $interaction;
        $this->variant = $variant;
        $this->moment = time();
        $this->uuid = $uuid;
        $this->url = $url;
    }
}
