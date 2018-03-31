<?php 
namespace App\Modules\Setting\Facades;

use Illuminate\Support\Facades\Facade;

class Setting extends Facade
{
    const SETTING_WASTE_TYPE_MAP = ['1' => '废水', '2' => '废气'];

	protected static function getFacadeAccessor(){
		return 'setting';
	}
}