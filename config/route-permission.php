<?php


return [
    /**
     * Allowable controller namespace will be count as permissible routes
     *
     * [nt: namespaces could be whole (with classname) or grouped namespaces]
     */
    'allowable-controller-namespace' => [
        'App\Http\Controllers', // allow all the controller classes which grouped by this namespace
    ],

    /**
     * Split route by defined character
     */
    'route-name-splitter' => '.',

    /**
     * Routes which are not count as permissible routes
     */
    'exclude-routes' => [
        // route.name
    ],

    /**
     * Cache the permissible routes
     */
    'cache-routes' => [
      'cacheable' => true,
      'cache-driver' => env('CACHE_DRIVER', 'file')
    ],

    /**
     * permission button used to checked / unchecked all routes
     *
     * nt: extra button can be added
     */
    'permission-buttons' => [
        '<button type="button" class="btn btn-success" onclick="checkAll()"><i class="fa fa-check-square"></i> Check All</button>',
        '<button type="button" class="btn btn-warning" onclick="uncheckAll()"><i class="fa fa-square"></i> Uncheck All </button>',
    ],

    /**
     * Permission card size
     *
     * nt: Permissible card only works on bootstrap
     */
    'card-size-class' => 'col-md-3 col-lg-3 col-sm-12',
];