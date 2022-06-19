<?php

namespace RadiateCode\PermissionNameGenerator;

use Illuminate\Support\ServiceProvider;
use RadiateCode\PermissionNameGenerator\Html\Builder;

class PermissionNameServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/permission-generator.php',
            'route-permission'
        );

        $this->app->singleton(
            'permission.view.builder',
            function ($app) {
                return $this->app->make(Builder::class);
            }
        );
    }

    public function boot()
    {
        $this->loadViewsFrom(
            __DIR__.'/../resources/views',
            'permissions-generator'
        );

        $this->publishes(
            [
                __DIR__.'/../resources/views' => resource_path(
                    'views/vendor/permissions-generator'
                ),
            ]
        );

        $this->publishes(
            [
                __DIR__
                .'/../config/permission-generator.php' => config_path(
                    'permissions-generator.php'
                ),
            ],
            'permissions-generator-config'
        );
    }
}