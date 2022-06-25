<?php

namespace RadiateCode\PermissionNameGenerator;

use Illuminate\Support\ServiceProvider;
use RadiateCode\PermissionNameGenerator\Html\Builder;
use RadiateCode\PermissionNameGenerator\Console\PermissionCacheClearCommand;

class PermissionNameServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/permission-generator.php',
            'permission-generator'
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
        if ($this->app->runningInConsole()) {
            $this->commands(
                [
                    PermissionCacheClearCommand::class
                ]
            );
        }

        $this->loadViewsFrom(
            __DIR__.'/../resources/views',
            'permission-generator'
        );

        $this->publishes(
            [
                __DIR__.'/../resources/views' => resource_path(
                    'views/vendor/permission-generator'
                ),
            ]
        );

        $this->publishes(
            [
                __DIR__
                .'/../config/permission-generator.php' => config_path(
                    'permission-generator.php'
                ),
            ],
            'permission-generator-config'
        );
    }
}