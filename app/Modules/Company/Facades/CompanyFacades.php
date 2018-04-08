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

    /**
     * 匹配公司
     * @param $ids
     * @param array $fields
     * @param string $indexKey
     * @param array $replaceWhere
     * @return array
     */
    public function searchCompanyForList($ids, $fields = ['name'], $indexKey = 'id', $replaceWhere = [])
    {
        return $this->_companyRepository->searchForList($ids, $fields, $indexKey, $replaceWhere);
    }

    /**
     * 获取公司信息
     * @param $id
     * @param array $fields
     * @return mixed
     */
    public function getCopmanyInfo($id, $fields = [])
    {
        return $this->_companyRepository->getCompanyInfo($id, $fields);
    }

    /**
     * 获取公司总数
     * @return int
     */
    public function getCompanyCount()
    {
        return $this->_companyRepository->getCompanyCount();
    }
}
