<?php

use Ntpages\LaravelTaster\Exceptions\UnexpectedInteractionException;
use Ntpages\LaravelTaster\Exceptions\InteractionNotFoundException;
use Ntpages\LaravelTaster\Exceptions\UnsupportedEventException;
use Ntpages\LaravelTaster\Services\TasterService;

if (!function_exists('interact')) {
    /**
     * @param string $key interaction unique identifier
     * @param string $event supported javascript event
     * @return string HTMLElement attributes to print
     * @throws UnexpectedInteractionException
     * @throws InteractionNotFoundException
     * @throws UnsupportedEventException
     */
    function interact(string $key, string $event)
    {
        // javascript events
        if (!in_array($event, TasterService::EVENT_TYPES)) {
            throw new UnsupportedEventException($event);
        }

        $url = route(config('taster.route.name'), [
            // loading interaction to make sure it exists
            'interactionKey' => app('taster')->getInteraction($key)->key,
            'variantKey' => app('taster')->getCurrentVariant()->key
        ]);

        // handled by javascript provided by the package
        // should be included where this is used
        return " data-tsr-event=\"$event\" data-tsr-url=\"$url\" ";
    }
}
