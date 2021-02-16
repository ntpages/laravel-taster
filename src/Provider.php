<?php

namespace Ntpages\LaravelTaster;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Blade;

use Ntpages\LaravelTaster\Listeners\CaptureInteraction;
use Ntpages\LaravelTaster\Observers\VariantObserver;
use Ntpages\LaravelTaster\Services\TasterService;
use Ntpages\LaravelTaster\Events\Interact;
use Ntpages\LaravelTaster\Models\Variant;

class Provider extends ServiceProvider
{
    public function boot()
    {
        $packageDir = dirname(__DIR__);

        $this->publishes([
            "$packageDir/public/js/taster.js" => public_path('vendor/tsr/taster.js'),
            "$packageDir/config/taster.php" => config_path('taster.php')
        ]);

        $this->loadMigrationsFrom("$packageDir/database/migrations");
        $this->loadRoutesFrom("$packageDir/routes/taster.php");

        // registering listeners
        Event::listen(Interact::class, CaptureInteraction::class);

        // entities
        Variant::observe(VariantObserver::class);

        // singleton helper
        $this->app->singleton('taster', fn() => new TasterService);

        // blade directives
        require $packageDir . '/src/helpers.php';

        $this->defineDirectives();
    }

    /**
     * Directives that are used to take paths between variations.
     */
    private function defineDirectives()
    {
        // experiment
        Blade::directive('experiment', function ($experimentKey) {
            return "<?php switch (app('taster')->experiment($experimentKey)) {";
        });

        Blade::directive('endexperiment', function () {
            return "} ?>";
        });

        // variant
        Blade::directive('variant', function ($variantKey) {
            return " case app('taster')->variant($variantKey): ?>";
        });

        Blade::directive('endvariant', function () {
            return "<?php break;";
        });
    }
}
