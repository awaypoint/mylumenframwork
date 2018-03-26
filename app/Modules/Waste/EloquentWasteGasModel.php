<?php

namespace App\Modules\Waste;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentWasteGasModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'waste_gas';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'company_id', 'tube_id', 'type', 'waste_name', 'fules_type', 'fules_element', 'sulfur_rate', 'gas_discharge',
        'discharge_level', 'equipment', 'technique', 'installations', 'technique_pic', 'remark'];
}
