<?php

namespace App\Modules\Company;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentProductModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'product';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'company_id', 'name', 'annual_output', 'source_material', 'unit', 'consume', 'process_flow', 'consume_unit', 'remark'];
}
