<?php


return [
    /**
     * Generate permissible routes for the allowable controller namespace
     *
     * [nt: namespaces could be whole controller classname or namespace prefix]
     */
    'allowable-controller-namespace' => [
        'App\Http\Controllers',
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
     * [nt: Within the allowable controller we can exclude routes by specific controllers]
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
     * permission button used to checked / unchecked all routes
     *
     * nt: extra button can be added
     */
    'permission-buttons' => [
        '<button type="button" class="btn btn-primary" onclick="checkAll()"><i class="fa fa-check-square"></i> Check All</button>',
        '<button type="button" class="btn btn-warning" onclick="uncheckAll()"><i class="fa fa-square"></i> Uncheck All </button>',
        '<button type="button" class="btn btn-success save-btn" onclick="saveRolePermissions()" title="save role permission"><i class="save-loader fa fa-save"></i> Save </button>',
    ],

    /**
     * Permission card size
     *
     * [nt: permissible card only works on bootstrap]
     */
    'card-size-class' => 'col-md-3 col-lg-3 col-sm-12',
];