<?php

namespace App\Modules\User;

use App\Modules\Common\CommonEloquentModel;

class EloquentUsersPermissionsModel extends CommonEloquentModel
{
    protected $table = 'users_permissions';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'uid', 'province_code', 'province', 'city_code', 'city', 'area_code', 'area', 'industrial_park', 'industrial_park_code', 'combine'];
}
