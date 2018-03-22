<?php

namespace App\Modules\Role;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentRolePermissionsModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'role_permissions';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'name', 'permission', 'relation_permission', 'module', 'parents_id', 'leaf', 'listorder'];
}
