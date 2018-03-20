<?php

namespace App\Http\Controllers;

use App\Modules\Company\CompanyRepository;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    private $_companyRepository;

    public function __construct(
        CompanyRepository $companyRepository
    )
    {
        parent::__construct();
        $this->_companyRepository = $companyRepository;
    }

    /**
     * 添加公司
     * @param Request $request
     * @return array
     */
    public function addCompany(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'company_status' => 'required',
            'credit_code' => 'required',
            'owner' => 'required',
            'contacts' => 'required',
            'mobile' => 'required',
            'address' => 'required',
        ]);
        $result = $this->_companyRepository->addCompany($request->all());
        return responseTo($result, '企业信息添加成功');
    }

    /**
     * 获取公司信息详情
     * @return array
     */
    public function getCompanyDetail()
    {
        $result = $this->_companyRepository->getCompanyDetail();
        return responseTo($result);
    }

    /**
     * 修改企业信息
     * @param Request $request
     * @return array
     */
    public function updateCompany(Request $request)
    {
        $result = $this->_companyRepository->updateCompany($request->all());
        return responseTo($result,'企业信息修改成功');
    }
}
