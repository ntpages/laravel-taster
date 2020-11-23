<?php

use Ntpages\LaravelTaster\Exceptions\UnexpectedInteractionException;
use Ntpages\LaravelTaster\Exceptions\UnsupportedEventException;
use Ntpages\LaravelTaster\Exceptions\ElementNotFoundException;
use Ntpages\LaravelTaster\Services\TasterService;

if (!function_exists('tsrEvent')) {
    /**
     * If you use this helper you also should be including the js provided with the package
     * @param string $key interaction unique identifier
     * @param string|array $events supported javascript event
     * @param bool $once triggered once per visit
     * @return string HTMLElement attributes to print
     * @throws UnexpectedInteractionException
     * @throws UnsupportedEventException
     * @throws ElementNotFoundException
     */
    function tsrEvent(string $key, $events, bool $once = true)
    {
        /** @var TasterService $taster */
        $taster = app('taster');

        // processing events
        if (is_string($events)) {
            $events = [$events];
        }
        foreach ($events as $event) {
            if (!in_array($event, $taster::EVENT_TYPES)) {
                throw new UnsupportedEventException($event);
            }
        }

        // building HTML attributes
        $attrs = " data-tsr-event=" . join(',', $events)
            . " data-tsr-url="
            . route(config('taster.route.name'), [
                // loading interaction to make sure it exists
                'interaction' => $taster->getInteraction($key),
                'variant' => $taster->getCurrentVariant()
            ])
            . " ";

        if ($once) {
            $attrs .= " data-tsr-once ";
        }

        return $attrs;
    }
}
