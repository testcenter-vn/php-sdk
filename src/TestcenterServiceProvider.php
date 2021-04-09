<?php

namespace Testcenter\Testcenter;

use Illuminate\Support\ServiceProvider;

class TestcenterServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'testcenter');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'testcenter');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/testcenter.php', 'testcenter');

        // Register the service the package provides.
        $this->app->singleton('testcenter', function ($app) {
            return new Testcenter;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['testcenter'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/testcenter.php' => config_path('testcenter.php'),
        ], 'testcenter.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/testcenter'),
        ], 'testcenter.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/testcenter'),
        ], 'testcenter.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/testcenter'),
        ], 'testcenter.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
