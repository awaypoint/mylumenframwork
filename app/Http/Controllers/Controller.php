<?php

namespace App\Http\Controllers;

use App\Exceptions\ProtectException;
use Illuminate\Support\Facades\Session;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected $uid;

    public function __construct()
    {
        if (!Session::has('uid')){
            throw new ProtectException(401);
        }
        $this->uid = Session::get('uid');
    }
}