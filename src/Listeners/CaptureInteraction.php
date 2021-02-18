<?php

namespace Ntpages\LaravelTaster\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;

use Ntpages\LaravelTaster\Events\Interact;
use Ntpages\LaravelTaster\Models\Record;

class CaptureInteraction implements ShouldQueue
{
    /**
     * @param Interact $event
     * @return void
     */
    public function handle(Interact $event)
    {
        $record = new Record;
        $record->interaction()->associate($event->interaction);
        $record->variant()->associate($event->variant);
        $record->moment = $event->moment;
        $record->url = $event->url;
        $record->save();
    }
}
