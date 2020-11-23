<?php

// Global improvements & features
// todo$:
//      - Fill the documentation
//      - Use session instead of cookies
//      - Write some helpful commands
//      - Write unit tests

namespace Ntpages\LaravelTaster;

use Ntpages\LaravelTaster\Listeners\CaptureInteraction;
use Ntpages\LaravelTaster\Services\TasterService;
use Ntpages\LaravelTaster\Events\Interact;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Blade;

class Provider extends ServiceProvider
{
    public function boot()
    {
        $packageDir = dirname(__DIR__);

        $this->publishes([
            $packageDir . '/public/js/index.js' => public_path('vendor/tsr/index.js'),
            $packageDir . '/config/taster.php' => config_path('taster.php')
        ]);
        $this->loadMigrationsFrom("$packageDir/database/migrations");
        $this->loadRoutesFrom("$packageDir/routes/taster.php");

        // singleton helper
        $this->app->singleton('taster', function () {
            return new TasterService;
        });

        // registering listeners
        Event::listen(Interact::class, CaptureInteraction::class);

        // todo: figure out if helper can be required automatically
        // todo: configure auto detection for PhpStorm
        // blade directives
        require $packageDir . '/src/helpers.php';
        $this->defineConditionalDirectives();
    }

    /**
     * Directives that are used to take paths between variations and features.
     */
    private function defineConditionalDirectives()
    {
        /*
        |--------------------------------------------------------------------------
        | Experiment
        |--------------------------------------------------------------------------
        |   @experiment(string $experimentKey)
        |
        |       @variant(string $variantKey)
        |           <TEMPLATE>
        |       @endvariant
        |
        |       @variant(string $variantKey)
        |           <TEMPLATE>
        |       @endvariant
        |
        |   @endexperiment
        */

        // experiment
        Blade::directive('experiment', function ($experimentKey) {
            return "<?php switch (app('taster')->experiment($experimentKey)) {";
        });

        Blade::directive('endexperiment', function () {
            return "} app('taster')->reset(); ?>";
        });

        // variant
        Blade::directive('variant', function ($variantKey) {
            return " case app('taster')->variant($variantKey): ?>";
        });

        Blade::directive('endvariant', function () {
            return "<?php app('taster')->reset(); break;";
        });

        /*
        |--------------------------------------------------------------------------
        | Feature
        |--------------------------------------------------------------------------
        |   @feature(string $featureKey)
        |       <TEMPLATE>
        |   ?@fallback
        |       ?<TEMPLATE>
        |   @endfeature
        */

        Blade::directive('feature', function ($featureKey) {
            return "<?php if (app('taster')->feature($featureKey)) { ?>";
        });

        Blade::directive('fallback', function () {
            return "<?php app('taster')->reset(); } else { ?>";
        });

        Blade::directive('endfeature', function () {
            return "<?php app('taster')->reset(); } ?>";
        });
    }
}
