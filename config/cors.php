<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    // 'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'paths' => [
        'sanctum/csrf-cookie',
        'api/v1/login',
        'api/v1/forgot-password',
        'api/v1/reset-password',
        'api/v1/profile',
        'api/v1/logout',
        'api/v1/check-session',
        'api/v1/dht11sensor',
        'api/v1/npksensor',
        'api/v1/sgp30sensor',
        'api/v1/nodes',
        'api/v1/nodes/*',
        'api/v1/upload-data',
        'api/v1/refresh-data'
    ],

    'allowed_methods' => ['GET, POST, PUT, DELETE'],

    'allowed_origins' => [
        'http://localhost:3000',
        'https://emdieytea.com',
        'https://*.emdieytea.com'
    ],


    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Accept, withCredentials, Authorization, Content-Type, App-Auth-Key'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
