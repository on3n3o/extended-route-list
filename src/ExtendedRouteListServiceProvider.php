<?php

namespace On3n3o\ExtendedRouteList;

use Illuminate\Support\ServiceProvider;
use On3n3o\ExtendedRouteList\Console\ExtendedRouteListCommand;

class ExtendedRouteListServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/extended-route-list.php' => config_path('extended-route-list.php'),
        ], 'config');

        

        // $this->commands([
        //     ExtendedRouteListCommand::class,
        // ]);

        $this->app->extend('command.route.list', function ($command, $app) {
            return new ExtendedRouteListCommand($app['router']);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/extended-route-list.php', 'extended-route-list'
        );
    }
}
