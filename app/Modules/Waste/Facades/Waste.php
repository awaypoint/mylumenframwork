<?php

namespace App\Modules\Waste\Facades;

use Illuminate\Support\Facades\Facade;

class Waste extends Facade
{
    const WASTE_GAS_TYPE_MAP = ['1' => '燃料废气', '2' => '工业废气'];

    protected static function getFacadeAccessor()
    {
        return 'waste';
    }
}