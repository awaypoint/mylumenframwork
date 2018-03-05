<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected $uid;

    public function __construct()
    {
        $token = app('request')->header('Authorization');
        if (Redis::exists(TUJIA_TOKEN_PREFIX . $token)) {
            $this->uid = Redis::get(TUJIA_TOKEN_PREFIX . $token);
        }
    }
}