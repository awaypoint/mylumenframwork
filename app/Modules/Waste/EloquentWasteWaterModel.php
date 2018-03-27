<?php

namespace App\Modules\Waste;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentWasteWaterModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'waste_water';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'company_id', 'tube_id', 'type', 'waste_name', 'water_discharge', 'discharge_level', 'water_direction', 'waste_plants', 'technique', 'daily_process', 'remark'];
}
