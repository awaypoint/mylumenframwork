<?php

namespace App\Modules\Company\Facades;

use App\Modules\Company\CompanyRepository;

class CompanyFacades
{
    private $_companyRepository;

    public function __construct(CompanyRepository $repository)
    {
        $this->_companyRepository = $repository;
    }

    /**
     * 新建公司
     * @param $params
     * @return array
     */
    public function addCompany($params)
    {
        return $this->_companyRepository->addCompany($params);
    }
}
