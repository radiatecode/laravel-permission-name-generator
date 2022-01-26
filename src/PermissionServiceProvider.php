<?php

namespace RadiateCode\LaravelRoutePermission;

use Illuminate\Support\ServiceProvider;
use RadiateCode\LaravelRoutePermission\Console\RolesTableCommand;

class PermissionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/route-permission.php',
            'route-permission');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                RolesTableCommand::class,
            ]);
        }


        $this->publishes([
            __DIR__
            .'/../config/route-permission.php' => config_path('route-permission.php'),
        ], 'route-permission-config');
    }
}