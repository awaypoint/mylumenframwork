<?php

namespace App\Modules\Setting;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentWasteTypeModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'waste_type';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'parents_id', 'level', 'name', 'code'];
}
