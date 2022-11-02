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
     * By Default permissions will be generated from all controller's routes
     * 
     * [Note: permissions will be generated from those controller which contains the defined whole or prefix of controller namespace]
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
     * Exclude routes by controller whole namespace or sub/prefix of controller namespace
     *
     * By default all auth controller's routes will be excluded from being generated as permission names
     * 
     * [Note: We can exclude routes by defining controller name or namespace-prefix. All the routes associated with controller will be excluded]
     */
    'exclude-controllers'           => [
        // exclude every route which associate with the prefix of controller namespace 
        'App\Http\Controllers\Auth',
    ],

    /**
     * Cache the rendered permission names
     */
    'cache-permissions'             => [
        'cacheable'    => true,
        'cache-driver' => env('CACHE_DRIVER', 'file'),
    ],

    /**
     * Permission card size
     *
     * [NT: Predefined permission cards works on bootstrap]
     */
    'card-size-class'               => 'col-md-3 col-lg-3 col-sm-12',
];
