<?php

namespace App\Modules\Role;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentRoleModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'role';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'name', 'company_id'];

    /**
     * 关联role_relation表
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function relation()
    {
        return $this->hasMany('App\Modules\Role\EloquentRoleRelationModel', 'role_id');
    }
}
