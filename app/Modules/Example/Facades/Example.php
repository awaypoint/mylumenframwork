<?php 
namespace App\Modules\_bigname_\Facades;

use Illuminate\Support\Facades\Facade;

class _bigname_ extends Facade
{	
	protected static function getFacadeAccessor(){
		return '_smallname_';
	}
}