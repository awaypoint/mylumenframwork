<?php 
namespace App\Modules\Waste\Facades;

use Illuminate\Support\Facades\Facade;

class Waste extends Facade
{	
	protected static function getFacadeAccessor(){
		return 'waste';
	}
}