<?php

use Ntpages\LaravelTaster\Http\Controllers\DefaultController;
use Illuminate\Support\Facades\Route;

$routeUrl = rtrim(config('taster.route.prefix'), '/');
$routeName = config('taster.route.name');

Route::post($routeUrl, [DefaultController::class, 'interact'])->name($routeName);
