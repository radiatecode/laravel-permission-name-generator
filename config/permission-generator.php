<?php


return [
    /**
     * Permission middlewares
     *
     * [NT: Which middleware routes need to be count as permission]
     */
    'permission-middlewares' => [
        // permission middleware
    ],

    /**
     * Split route name by defined character
     */
    'route-name-splitter' => '.',

    /**
     * Exclude routes by route name
     */
    'exclude-routes' => [
        // route.name
    ],

    /**
     * Exclude routes by controller
     *
     * [NT: We can exclude routes by controllers. All the routes associated with controller will be excluded]
     */
    'exclude-controllers' => [
        /*
         * exclude every route which associate with WelcomeController
         */
        // WelcomeController::class
    ],

    /**
     * Cache the permissible routes
     */
    'cache-routes' => [
      'cacheable' => true,
      'cache-driver' => env('CACHE_DRIVER', 'file')
    ],

    /**
     * Permission card size
     *
     * [NT: Permissible card only works on bootstrap]
     */
    'card-size-class' => 'col-md-3 col-lg-3 col-sm-12',
];