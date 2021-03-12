<?php

namespace Ntpages\LaravelTaster\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use Ntpages\LaravelTaster\Models\Variant;

class Impression
{
    use Dispatchable, SerializesModels;

    public Variant $variant;
    public int $moment;
    public string $uuid;
    public string $url;

    public function __construct(Variant $variant, string $uuid, string $url = '')
    {
        $this->variant = $variant;
        $this->moment = time();
        $this->uuid = $uuid;
        $this->url = $url;
    }
}
