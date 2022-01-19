<?php

namespace RadiateCode\LaravelRoutePermission;

use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-route-permission.php.php', 'laravel-route-permission');

    }

    public function boot(){
        $this->publishes([
            __DIR__.'/../config/laravel-route-permission.php' => config_path('laravel-route-permission.php'),
        ],'laravel-route-permission-config');
    }
}