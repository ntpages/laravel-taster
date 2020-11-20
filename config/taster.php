<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Here we store the default values which are not recommended to update unless
    | the package overrides your app definitions or consumes to much.
    | Assign 0 to 'cache.ttl' to disable the cache (default one day).
    |
    */

    'cache' => [
        'key' => 'taster.definition_config',
        'ttl' => 86400
    ],

    /*
    |--------------------------------------------------------------------------
    | Cookies
    |--------------------------------------------------------------------------
    |
    | Used to store the information about the assigned variants & features.
    | By default every decision is assigned to visitor for five years (forever).
    |
    */

    'cookie' => [
        'ttl' => 2628000,
        'key' => 'tsr'
    ],

    /*
    |--------------------------------------------------------------------------
    | Router
    |--------------------------------------------------------------------------
    |
    | If we're overriding one of your urls or package route is not reachable
    | please update the configuration in this section.
    |
    */

    'route' => [
        'name' => 'taster.xhr-interact',
        'prefix' => '/tsr/interact'
    ],

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    |
    | In case your application have multiple queues you can pick the one is used
    | by the package here
    |
    */

    'queue' => 'default'
];
