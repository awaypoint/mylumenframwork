<?php 
namespace App\Modules\Role\Facades;

use Illuminate\Support\Facades\Facade;

class Role extends Facade
{
    const ROLE_COMMON_TYPE = 1;//普通用户
    const ROLE_ADMIN_TYPE = 2;//管理员用户
    const ROLE_SUPER_ADMIN_TYPE = 3;//超级管理员用户

	protected static function getFacadeAccessor(){
		return 'role';
	}
}