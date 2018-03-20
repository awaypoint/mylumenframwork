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
    public $fillable = ['id', 'name', 'company_status', 'credit_code', 'used_name', 'owner', 'type', 'is_env_statistics', 'is_pass_iso', 'contacts', 'tel', 'mobile',
        'email', 'latitude', 'longitude', 'address', 'zip_code', 'area', 'industry_category', 'production_time', 'annual_output', 'investment', 'env_investment',
        'annual_scale', 'eia_code', 'eia_unit', 'env_approve_code', 'env_approve_unit', 'pollution_lic_code', 'pollution_lic_date', 'pollution_type', 'pollution_names',
        'pollution_outlet_no', 'radiant_lic_code', 'business_lic', 'remark'];
}
