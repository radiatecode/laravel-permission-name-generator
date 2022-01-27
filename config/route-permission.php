<?php


return [
    /**
     * Allowable controller namespace will be count as permissible routes
     */
    'allowable-controller-namespace' => [
        'App\Http\Controllers',
    ],

    /**
     * Split route by defined character
     */
    'route-name-splitter' => '.',

    /**
     * Routes which are not count as permissible routes
     */
    'exclude-routes' => [
        //
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