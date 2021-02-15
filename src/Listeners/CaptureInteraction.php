<?php

namespace Ntpages\LaravelTaster\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Ntpages\LaravelTaster\Events\Interact;

class CaptureInteraction implements ShouldQueue
{
    /**
     * @param Interact $event
     * @return void
     */
    public function handle(Interact $event)
    {
        $event->variant->interactions()->save($event->interaction, ['moment' => $event->moment]);
    }
}
