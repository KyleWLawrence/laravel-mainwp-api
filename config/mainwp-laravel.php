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
    'consumer_key' => env('MAINWP_CONSUMER_KEY', ''),
    'consumer_secret' => env('MAINWP_CONSUMER_SECRET', ''),

];
