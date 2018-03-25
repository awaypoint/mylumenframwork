<?php

namespace App\Modules\Waste;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentWasteMaterialModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'waste_material';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'company_id', 'waste_category', 'industry', 'waste_code', 'waste_name', 'commonly_called', 'harmful_staff',
        'waste_shape', 'waste_type', 'waste_trait', 'annual_scale', 'handle_company', 'handle_way', 'transport_unit', 'remark'];
}
