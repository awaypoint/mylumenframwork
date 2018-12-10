<?php
return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', 'localhost'),
            'database' => env('DB_DATABASE', ''),
            'username' => env('DB_USERNAME', ''),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => env('DB_PREFIX', ''),
            'strict' => false,
        ],
        'mongodb' => array(
            'driver' => 'mongodb',
            'host' => env('MONGODB_HOST', 'localhost'),
            'port' => env('MONGODB_PORT', 27017),
            'username' => env('MONGODB_USERNAME', ''),
            'password' => env('MONGODB_PASSWORD', ''),
            'database' => env('MONGODB_DATABASE', 'mylumentemplate'),
            'options' => array(
                'db' => env('MONGODB_AUTHDATABASE', '') //Sets the auth DB
            )
        )
    ],
    'redis' => [
        'client' => 'predis',
        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],
    ],
];
