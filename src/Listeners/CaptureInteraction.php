<?php

namespace Ntpages\LaravelTaster\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

use Ntpages\LaravelTaster\Events\Interact;

// todo:
//      - allow user to disable Queueable functionality
//      - resolve the queue name assign from config

class CaptureInteraction implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param Interact $event
     * @return void
     */
    public function handle(Interact $event)
    {
        $event->variant->interactions()->save($event->interaction, ['moment' => $event->moment]);
    }
}
