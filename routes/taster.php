<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

use Ntpages\LaravelTaster\Models\Interaction;
use Ntpages\LaravelTaster\Events\Interact;
use Ntpages\LaravelTaster\Models\Variant;

Route::post(
    sprintf('%s/{variantKey}/{interactionKey}', rtrim(config('taster.route.prefix'), '/')),

    /**
     * @param string $variantKey
     * @param string $interactionKey
     * @return Response
     */
    function (string $variantKey, string $interactionKey) {
        try {
            event(
                new Interact(
                    Interaction::where('key', $interactionKey)->firstOrFail(),
                    Variant::where('key', $variantKey)->firstOrFail()
                )
            );
        } catch (ModelNotFoundException $exception) {
            abort(Response::HTTP_NOT_FOUND, $exception->getMessage());
        }
        return response()->noContent();
    }
)->name(config('taster.route.name'));
