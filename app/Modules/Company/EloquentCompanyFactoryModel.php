<?php

namespace App\Modules\Company;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentCompanyFactoryModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'company_factory';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'company_id', 'name', 'address'];
}
