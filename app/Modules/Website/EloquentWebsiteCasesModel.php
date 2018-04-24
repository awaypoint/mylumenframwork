<?php

namespace App\Modules\Website;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentWebsiteCasesModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'website_cases';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'category_id', 'title', 'img', 'desc', 'detail'];
}
