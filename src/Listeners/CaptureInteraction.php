<?php

namespace Ntpages\LaravelTaster\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Ntpages\LaravelTaster\Events\Interact;

// todo:
//      - allow user to disable Queueable functionality
//      - resolve the queue name assign from config
//      - fix the bug with the `moment` not saving

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
