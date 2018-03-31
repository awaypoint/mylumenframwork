<?php

namespace App\Modules\Setting;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentWasteModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'waste';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'name', 'code'];
}
