<?php

namespace App\Modules\Waste\Facades;

use Illuminate\Support\Facades\Facade;

class Waste extends Facade
{
    const WASTE_GAS_TYPE_MAP = ['1' => '燃烧废气', '2' => '工业废气'];
    const WASTE_TUBE_TYPE_MAP = ['1' => '废气排放口', '2' => '废水排放口'];
    const WASTE_GAS_TUBE_TYPE = 1;
    const WASTE_WATER_TUBE_TYPE = 2;

    const WASTE_WATER_TYPE_MAP = ['1' => '生活废水', '2' => '工业废水'];

    protected static function getFacadeAccessor()
    {
        return 'waste';
    }
}