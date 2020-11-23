<?php

use Ntpages\LaravelTaster\Http\Controllers\DefaultController;

Route::post(
    sprintf('%s/{interaction}/{variant}', rtrim(config('taster.route.prefix'), '/')),
    [
        DefaultController::class,
        'interact'
    ]
)->name(config('taster.route.name'));
