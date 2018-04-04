<?php 
namespace App\Modules\User\Facades;

use Illuminate\Support\Facades\Facade;

class User extends Facade
{
    const USER_COMMON_ROLE_TYPE = 1;//普通用户类型
    const USER_ADMIN_ROLE_TYPE = 2;//管理员用户类型
    const USER_SUPER_ROLE_TYPE = 3;//超级管理员用户类型

	protected static function getFacadeAccessor(){
		return 'user';
	}
}