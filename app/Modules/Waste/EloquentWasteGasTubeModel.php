<?php

namespace App\Modules\Waste;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentWasteGasTubeModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'gas_tube';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'company_id', 'item_no', 'height', 'pics', 'check', 'remark'];
}
