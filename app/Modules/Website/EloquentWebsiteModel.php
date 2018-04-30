<?php

namespace App\Modules\Website;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentWebsiteModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'website';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'logo', 'banners', 'category_num', 'longitude', 'latitude'];
}
