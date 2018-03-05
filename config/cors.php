<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Laravel CORS
     |--------------------------------------------------------------------------
     |
     | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
     | to accept any value.
     |
     */
    'supportsCredentials' => true,
    'allowedOrigins' => env('APP_DEBUG')?['*']:['http://mobile.xiaoweixitong.com'],
    'allowedHeaders' => ['*'],
    'allowedMethods' => ['POST','GET','PUT','DELETE','OPTIONS'],
    'exposedHeaders' => [],
    'maxAge' => 0,
];

