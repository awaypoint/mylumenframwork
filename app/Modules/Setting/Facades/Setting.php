<?php 
namespace App\Modules\Setting\Facades;

use Illuminate\Support\Facades\Facade;

class Setting extends Facade
{	
	protected static function getFacadeAccessor(){
		return 'setting';
	}
}