<?php

namespace RadiateCode\LaravelRoutePermission;

use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/route-permission.php',
            'route-permission');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__
            .'/../config/route-permission.php' => config_path('route-permission.php'),
        ], 'route-permission-config');
    }
}