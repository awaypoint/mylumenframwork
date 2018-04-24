<?php 
namespace App\Modules\Website\Facades;

use Illuminate\Support\Facades\Facade;

class Website extends Facade
{	
	protected static function getFacadeAccessor(){
		return 'website';
	}
}