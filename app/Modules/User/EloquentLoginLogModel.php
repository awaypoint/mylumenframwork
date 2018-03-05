<?php

namespace App\Modules\User;

use App\Modules\Common\CommonEloquentModel;

class EloquentLoginLogModel extends CommonEloquentModel
{
    protected $table = 'login_log';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'uid', 'token'];
}
