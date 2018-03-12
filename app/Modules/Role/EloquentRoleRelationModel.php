<?php

namespace App\Modules\Role;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentRoleRelationModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'role_relation';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'role_id', 'permission', 'relation_permission'];
}
