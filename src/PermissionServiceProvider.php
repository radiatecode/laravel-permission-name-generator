<?php

namespace RadiateCode\LaravelRoutePermission;

use Illuminate\Support\ServiceProvider;
use RadiateCode\LaravelRoutePermission\Html\Builder;

class PermissionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/route-permission.php',
            'route-permission');

        $this->app->singleton('permission.view.builder',function ($app){
            return $this->app->make(Builder::class);
        });
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'route-permission');

        $this->publishes([
            __DIR__
            .'/../config/route-permission.php' => config_path('route-permission.php'),
        ], 'route-permission-config');
    }
}