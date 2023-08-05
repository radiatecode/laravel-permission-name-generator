<?php
/**
 * In an application we may have permissions like 'create-post', 'edit-post', and these permission are hardcoded. Using the package
 * We can dynamically generate these permissions from route names or defining resource name
 * 
 * Note: permissions could be generate for multiple panel, for example one application may have User panel and Admin panel, both may need different permissions
 * So we can achieve that by defining multiple panel, By default it is 'user' panel, assuming that all the permissions for user panel. 
 * If you want different permissions for different panel you can generate that by defining another panel.
 */
return [
    /**
     * Split route name by defined needle
     */
    'route-name-splitter-needle'    => '.',

    /**
     * Custom permissions
     * 
     * Here you can add custom permissions for individual panel, you can add multiple panel ex: admin panel
     */
    'custom-permissions'            => [
        'panels' => [
            'user' => [
                // custom-permissions
            ],
            // 'admin' => [
            //    // custom-permission
            //],
        ]
    ],

    /**
     * Permission generate controller's
     *
     * By Default permissions will be generated from all controller's routes for user panel
     */
    'permission-generate-controllers' => [
        'panels' => [
            'user' => [
                'App\Http\Controllers',
            ],
            // 'admin' => [
            //    'App\Http\Controllers\Admin',
            //],
        ]
    ],

    /**
     * Exclude routes by controller's
     *
     * By default all auth controller's routes will be excluded from being generated as permission names for user panel
     * 
     * [Note: routes can be excluded  by defining App\Http\Controller\SomeController::class or namespace-prefix 'App\Http\Controllers\Auth']
     */
    'exclude-controllers'           => [
        'panels' => [
            'user' => [
                'App\Http\Controllers\Auth',
            ],
            // 'admin' => [
            //    'App\Http\Controllers\Admin\Auth',
            //],
        ]
    ],

    /**
     * Exclude routes by route name
     * 
     * Panel wise you can exclude routes
     */
    'exclude-routes'                => [
        'panels' => [
            'user' => [
                // route.name,
            ],
            // 'admin' => [
            //  // route.name,
            //],
        ]
    ],

    /**
     * Cache the rendered permission names
     */
    'cache-permissions'             => true,

    /**
     * ---------------------------------------------------------------------------------------------------------
     * This config only used if you want to generate permission names from resources instead of routes
     * ---------------------------------------------------------------------------------------------------------
     * 
     * These actions used to generate permissions on given resources
     * 
     * [Ex: If resource is posts, then permission will be (create-posts,'edit-posts','view-posts') etc]
     */
    'resource-actions' => [
        'create',
        'edit',
        'show',
        'delete',
        'view',
    ],

    /**
     * Parmissions can be grouped by section (ex: adminland, settings, employee managment etc)
     * 
     * sample format: key as section name, value as generated permissions-title
     * [
     *   'adminland' => [
     *       'employee-permissions',
     *       'bonus-permissions'
     *   ],
     *   'settings' => [
     *       'office-permissions',
     *       'designation-permissions',
     *       'email-settings-permissions',
     *       'rules-permissions'
     *   ],
     *  ]
     */
    'permissions-section' => [
        'panels' => [
            'user' => [
                
            ],
            // 'admin' => [
            // 
            //],
        ]
    ]
];
