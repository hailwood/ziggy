<?php

namespace Tightenco\Ziggy;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class ZiggyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Route::macro('blacklist', function ($group = null) {
            return Macro::blacklist($this, $group);
        });

        Route::macro('whitelist', function ($group = null) {
            return Macro::whitelist($this, $group);
        });

        $this->app['blade.compiler']->directive('routes', function ($group) {
            return "<?php echo app('" . BladeRouteGenerator::class . "')->generate({$group}); ?>";
        });

        $this->publishes([
            __DIR__.'/js' => resource_path('assets/js/ziggy'),
        ], 'resources');

        if (true || $this->app->runningInConsole()) {
            $this->commands([
                CommandTypescriptGenerator::class,
                CommandRouteGenerator::class,
            ]);
        }
    }
}
