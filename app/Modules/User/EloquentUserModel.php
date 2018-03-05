<?php

namespace App\Modules\User;

use App\Modules\Common\CommonEloquentModel;

class EloquentUserModel extends CommonEloquentModel
{
    protected $table = 'users';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'nickname', 'sex', 'open_id', 'avatar_url', 'subscribe', 'subscribe_time', 'city', 'province', 'country'];

    /**
     * 关联adminUser表
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function relation()
    {
        return $this->hasMany('App\Modules\User\EloquentUserRelationModel', 'uid');
    }
}
