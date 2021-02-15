<?php

use Ntpages\LaravelTaster\Exceptions\UnexpectedInteractionException;
use Ntpages\LaravelTaster\Exceptions\UnsupportedEventException;
use Ntpages\LaravelTaster\Exceptions\ElementNotFoundException;
use Ntpages\LaravelTaster\Services\TasterService;

if (!function_exists('tsr')) {
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
    function tsr(string $key, $events, bool $once = true): string
    {
        // processing events
        if (is_string($events)) {
            $events = [$events];
        }

        foreach ($events as $event) {
            if (!in_array($event, TasterService::JS_EVENTS)) {
                throw new UnsupportedEventException($event);
            }
        }

        /** @var TasterService $tsr */
        $tsr = app('taster');

        // building HTML attributes
        $attrs = " data-tsr-url=" . $tsr->getInteractionUrl($key)
            . " data-tsr-event=" . join(',', $events);

        if ($once) {
            $attrs .= " data-tsr-once ";
        }

        return $attrs;
    }
}
