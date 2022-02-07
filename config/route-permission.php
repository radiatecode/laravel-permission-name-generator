<?php


return [
    /**
     * Permission middlewares
     *
     * [NT: Define the middlewares by which we applied permissions]
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
     * permission button used to checked / unchecked all routes
     *
     * [NT: Extra button can be added]
     */
    'permission-buttons' => [
        '<button type="button" class="btn btn-primary" onclick="checkAll()"><i class="fa fa-check-square"></i> Check All</button>',
        '<button type="button" class="btn btn-warning" onclick="uncheckAll()"><i class="fa fa-square"></i> Uncheck All </button>',
        '<button type="button" class="btn btn-success save-btn" onclick="saveRolePermissions()" title="save role permission"><i class="save-loader fa fa-save"></i> Save </button>',
    ],

    /**
     * Permission card size
     *
     * [NT: Permissible card only works on bootstrap]
     */
    'card-size-class' => 'col-md-3 col-lg-3 col-sm-12',
];