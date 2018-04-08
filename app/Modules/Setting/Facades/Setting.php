<?php 
namespace App\Modules\Setting\Facades;

use Illuminate\Support\Facades\Facade;

class Setting extends Facade
{
    const SETTING_WASTE_TYPE_MAP = ['1' => '废气', '2' => '废水'];
    const SETTING_WASTE_WATER_TYPE = 2;
    const SETTING_WASTE_GAS_TYPE = 1;

	protected static function getFacadeAccessor(){
		return 'setting';
	}
}