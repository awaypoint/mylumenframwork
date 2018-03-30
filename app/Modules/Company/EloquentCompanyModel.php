<?php

namespace App\Modules\Company;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentCompanyModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'company';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'name', 'company_status', 'credit_code', 'used_name', 'owner', 'type', 'contacts', 'tel', 'mobile',
        'email', 'latitude', 'longitude', 'address', 'industry_category', 'production_time', 'province', 'city', 'area',
        'annual_scale', 'business_lic', 'iso', 'remark', 'province_code', 'city_code', 'area_code'];

    /**
     * 关联factory表
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function factory()
    {
        return $this->hasMany('App\Modules\Company\EloquentCompanyFactoryModel', 'company_id');
    }
}
