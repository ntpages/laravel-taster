<?php

use Ntpages\LaravelTaster\Exceptions\UnexpectedInteractionException;
use Ntpages\LaravelTaster\Exceptions\InteractionNotFoundException;
use Ntpages\LaravelTaster\Exceptions\UnsupportedEventException;
use Ntpages\LaravelTaster\Services\TasterService;

if (!function_exists('interact')) {
    /**
     * If you use this helper you also should be including the js provided with the package
     * @param string $key interaction unique identifier
     * @param string $event supported javascript event
     * @param bool $once triggered once per visit
     * @return string HTMLElement attributes to print
     * @throws UnexpectedInteractionException
     * @throws InteractionNotFoundException
     * @throws UnsupportedEventException
     */
    function interact(string $key, string $event, bool $once = true)
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

        $attrs = " data-tsr-event=$event data-tsr-url=$url ";

        if ($once) {
            $attrs .= "data-tsr-once ";
        }

        return $attrs;
    }
}
