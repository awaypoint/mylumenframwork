<?php 
namespace App\Modules\Files\Facades;

use Illuminate\Support\Facades\Facade;

class Files extends Facade
{	
	protected static function getFacadeAccessor(){
		return 'files';
	}
}