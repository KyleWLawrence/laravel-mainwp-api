<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Auth
    |--------------------------------------------------------------------------
    |
    | This is the Auth vars.
    |
    */

    'driver' => env('MAINWP_DRIVER', 'api'),
    'domain' => env('MAINWP_DOMAIN', ''),
    'bearer_token' => env('MAINWP_BEARER_TOKEN', ''),
    'version' => env('MAINWP_VERSION', \KyleWLawrence\MainWP\Http\HttpClient::DEFAULT_VERSION),
    'consumer_key' => env('MAINWP_CONSUMER_KEY', ''),
    'consumer_secret' => env('MAINWP_CONSUMER_SECRET', ''),

];
