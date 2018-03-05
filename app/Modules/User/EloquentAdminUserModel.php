<?php

namespace App\Modules\User;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentAdminUserModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'admin_user';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'company_id', 'role_type', 'uid', 'user_name', 'mobile', 'is_superuser'];

    /**
     * 关联role_relation表
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function relation()
    {
        return $this->hasMany('App\Modules\Role\EloquentRoleRelationModel', 'uid');
    }
}
