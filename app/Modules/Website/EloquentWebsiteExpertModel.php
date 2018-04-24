<?php

namespace App\Modules\Website;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentWebsiteExpertModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'website_expert';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'category_id', 'expert', 'img', 'desc'];
}
