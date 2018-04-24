<?php

namespace App\Modules\Website;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentWebsiteNewsModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'website_news';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'type', 'title', 'detail', 'updated_at'];
}
