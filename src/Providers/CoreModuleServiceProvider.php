<?php

namespace Redoy\CoreModule\Providers;

use Illuminate\Support\ServiceProvider;
use Redoy\CoreModule\Traits\ResponseHelperTrait;
use MarcinOrlowski\ResponseBuilder\ExceptionHandlerHelper;

class CoreModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Merge core config
        $this->mergeConfigFrom(__DIR__ . '/../../config/core.php', 'core');

        // Inject your CoreModule response_builder config into ResponseBuilder
        config(['response_builder' => config('core.response_builder')]);
        

        // Register singleton for core-response helper
        $this->app->singleton('core-response', function () {
            return new class {
                use ResponseHelperTrait;
            };
        });
    }

    public function boot()
    {
        // Publish configs
        $this->publishes([
            __DIR__ . '/../../config/core.php' => config_path('core.php'),
        ], 'core-config');

        // Publish translations
        $this->publishes([
            __DIR__ . '/../../resources/lang' => resource_path('lang/'),
        ], 'core-lang');

        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        // Load translations from package
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'core');

        // Load helpers if exists
        if (file_exists(__DIR__ . '/Helpers/helpers.php')) {
            require __DIR__ . '/Helpers/helpers.php';
        }

        // Register global exception handler for the module
        $this->app->make('Illuminate\Contracts\Debug\ExceptionHandler')
            ->renderable(function (\Throwable $ex, $request) {
                return ExceptionHandlerHelper::render($request, $ex);
            });
    }
}
