<?php

namespace App\Modules\Company;

trait CompanyTraits
{
    //企业状态
    private $company_status = [
        '1' => '国控企业（1000吨以上）',
        '2' => '省控企业（100吨以上）',
        '3' => '市控企业（10吨以上）',
        '4' => '区控企业',
    ];
    //排污类型
    private $pollution_type = [
        '1' => '废水企业',
        '2' => '废气企业',
        '3' => '危废企业',
        '4' => '一般固废企业',
    ];

    public function getConst($field)
    {
        return $this->$field;
    }
}
