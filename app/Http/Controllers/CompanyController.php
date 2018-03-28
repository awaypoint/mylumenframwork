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
            'used_name' => 'required',
            'credit_code' => 'required',
            'company_status' => 'required',
            'type' => 'required|numeric',
            'owner' => 'required',
            'iso' => 'required|array',
            'business_lic' => 'required|array',
            'industry_category' => 'required|numeric',
            'production_time' => 'required',
            'annual_scale' => 'required',
            'province' => 'required',
            'city' => 'required',
            'area' => 'required',
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
        $result = $this->_companyRepository->getCompanyDetail(getUserInfo()['company_id']);
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
        return responseTo($result, '企业信息修改成功');
    }

    /**
     * 添加产品
     * @param Request $request
     * @return array
     */
    public function addProduct(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $result = $this->_companyRepository->addProduct($request->all());
        return responseTo($result);
    }

    /**
     * 获取产品列表
     * @param Request $request
     * @return array
     */
    public function getProductList(Request $request)
    {
        $page = $request->get('page') ?? 0;
        $pageSize = $request->get('page_size') ?? 0;
        $orderBy = $request->get('order_by') ?? 'id';
        $sortBy = $request->get('sort_by') ?? 'DESC';
        $orderArr = [$orderBy, $sortBy];
        $result = $this->_companyRepository->getProductList($request->all(), $page, $pageSize, $orderArr);
        return responseTo($result);
    }

    /**
     * 获取产品详情
     * @param Request $request
     * @return array
     */
    public function getProductDetail(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $userInfo = getUserInfo(['company_id']);
        $result = $this->_companyRepository->getProductDetail($userInfo['company_id'], $request->get('id'));
        return responseTo($result);
    }

    /**
     * 修改产品
     * @param Request $request
     * @return array
     */
    public function updateProduct(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $userInfo = getUserInfo(['company_id']);
        $result = $this->_companyRepository->updateProduct($userInfo['company_id'], $request->get('id'), $request->all());
        return responseTo($result, '产品修改成功');
    }

    /**
     * 删除产品
     * @param Request $request
     * @return array
     */
    public function delProduct(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $userInfo = getUserInfo(['company_id']);
        $result = $this->_companyRepository->delProduct($userInfo['company_id'], $request->get('id'));
        return responseTo($result, '产品删除成功');
    }
}
