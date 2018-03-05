<?php

namespace App\Modules\Example;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentExampleModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'example';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'device_name', 'device_id', 'product_key', 'created_uid', 'status'];
}
