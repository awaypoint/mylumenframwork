<?php

namespace App\Modules\Setting;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentIndustrialParkModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'industrial_park';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'name', 'province_code', 'city_code', 'area_code'];
}
