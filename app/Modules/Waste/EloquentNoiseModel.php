<?php

namespace App\Modules\Waste;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentNoiseModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'waste_noise';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'company_id', 'equipment', 'num', 'range', 'stanard', 'is_done', 'technique', 'remark'];
}
