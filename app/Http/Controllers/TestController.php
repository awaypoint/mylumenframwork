<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;

class TestController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function testSession()
    {
        $a = Session::getId();
        $b = Session::all();
        echo $a.PHP_EOL;
        dd($b);
    }
}
