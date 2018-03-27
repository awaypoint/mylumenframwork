<?php

namespace App\Modules\Waste;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentNucleusModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'waste_nucleus';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'company_id', 'equipment', 'num', 'equipment_type', 'radial_type', 'spec', 'activity', 'code', 'no', 'maintenance_staff', 'staff_mobile', 'management_agency', 'remark'];
}
