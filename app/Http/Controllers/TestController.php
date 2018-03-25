<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Laravel\Lumen\Routing\Controller as BaseController;

class TestController extends BaseController
{
    public function testSession()
    {
        $a = Session::getId();
        $b = Session::all();
        echo $a.PHP_EOL;
        dd($b);
    }
}
