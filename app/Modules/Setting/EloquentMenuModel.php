<?php

namespace App\Modules\Setting;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentMenuModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'menu';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'name', 'parents_id', 'level', 'leaf', 'listorder', 'itemorder', 'status', 'url', 'icon'];
}
