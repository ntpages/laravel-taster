<?php

use Ntpages\LaravelTaster\Http\Controllers\DefaultController;

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;

Route::post(sprintf('%s/{interaction}/{variant}', rtrim(config('taster.route.prefix'), '/')),
    DefaultController::class . '@interact')
    ->middleware(SubstituteBindings::class)
    ->name(config('taster.route.name'));
