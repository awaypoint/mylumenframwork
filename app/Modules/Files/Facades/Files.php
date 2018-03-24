<?php 
namespace App\Modules\Files\Facades;

use Illuminate\Support\Facades\Facade;

class Files extends Facade
{
    const FILES_COMPANY_MODULE_TYPE = 1;

	protected static function getFacadeAccessor(){
		return 'myfiles';
	}
}