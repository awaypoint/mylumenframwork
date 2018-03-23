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
    public $fillable = ['id', 'company_id', 'type', 'waste_name', 'fules_type', 'fules_element', 'sulfur_rate', 'gas_discharge',
        'discharge_level', 'tube_no', 'technique', 'installations', 'technique_pic', 'remark'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        EloquentWasteGasModel::saving(function (){
            dd('dfdfdf');
        });
    }
}
