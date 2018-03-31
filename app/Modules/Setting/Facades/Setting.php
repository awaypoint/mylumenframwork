<?php 
namespace App\Modules\Setting\Facades;

use Illuminate\Support\Facades\Facade;

class Setting extends Facade
{
    const SETTING_WASTE_TYPE_MAP = ['1' => '废水', '2' => '废气'];
    const SETTING_WASTE_WATER_TYPE = 1;
    const SETTING_WASTE_GAS_TYPE = 2;

	protected static function getFacadeAccessor(){
		return 'setting';
	}
}