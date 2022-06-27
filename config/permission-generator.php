<?php


return [
    /**
     * Split route name by defined needle
     */
    'route-name-splitter-needle'    => '.',

    /**
     * Custom permissions
     */
    'custom-permissions'            => [
        //
    ],

    /**
     * Define controller namespace
     *
     * [NT: permissions will be generated from those controller which contains the prefix]
     */
    'controller-namespace-prefixes' => [
        'App\Http\Controllers',
    ],

    /**
     * Exclude routes by route name
     */
    'exclude-routes'                => [
        // route.name
    ],

    /**
     * Exclude routes by controller or controller namespace-prefix
     *
     * [NT: We can exclude routes by defining controller name or namespace-prefix. All the routes associated with controller will be excluded]
     */
    'exclude-controllers'           => [
        /*
         * exclude every route which associate with the prefix namespace
         */
        'App\Http\Controllers\Auth',
    ],

    /**
     * Cache the permissible routes
     */
    'cache-permissions'             => [
        'cacheable'    => true,
        'cache-driver' => env('CACHE_DRIVER', 'file'),
    ],

    /**
     * Permission card size
     *
     * [NT: Permissible card only works on bootstrap]
     */
    'card-size-class'               => 'col-md-3 col-lg-3 col-sm-12',
];