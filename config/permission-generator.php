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
        
    ],

    /**
     * Permission generate controller's namespace
     *
     * By Default permissions will be generated from all controller's routes
     */
    'permission-generate-controllers' => [
        'App\Http\Controllers',
    ],

    /**
     * Exclude routes by controller's namespace
     *
     * By default all auth controller's routes will be excluded from being generated as permission names
     * 
     * [Note: Exclude routes by defining App\Http\Controller\SomeController::class or namespace-prefix]
     */
    'exclude-controllers'           => [
        'App\Http\Controllers\Auth',
    ],

    /**
     * Exclude routes by route name
     */
    'exclude-routes'                => [
        // route.name
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
     * Parmissions can be organised by section (ex: adminland, settings, employee managment etc)
     * 
     * format: key as section name, value as generated permissions-title
     * [
     *   'adminland' => [
     *       'employee-permissions',
     *       'bonus-permissions'
     *   ],
     *   'settings' => [
     *       'office-permissions',
     *       'designation-permissions',
     *       'email-settings-permissions,
     *       'rules-permissions
     *   ],
     *  ]
     */
    'permissions-section' => [
        
    ]
];
