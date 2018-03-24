<?php

namespace App\Modules\Company;

use App\Modules\Common\CommonRepository;
use App\Modules\Company\Exceptions\CompanyException;
use App\Modules\User\Facades\User;
use Illuminate\Support\Facades\DB;

class CompanyRepository extends CommonRepository
{
    use CompanyTraits;

    private $_companyModel;
    private $_productModel;

    public function __construct(
        EloquentCompanyModel $eloquentCompanyModel,
        EloquentProductModel $eloquentProductModel
    )
    {
        $this->_companyModel = $eloquentCompanyModel;
        $this->_productModel = $eloquentProductModel;
    }

    /**
     * 添加公司
     * @param $params
     * @throws CompanyException
     */
    public function addCompany($params)
    {
        $userInfo = getUserInfo();
        if ($userInfo['company_id'] > 0) {
            throw new CompanyException(40001);
        }
        $this->_validate($params);
        $addData = [
            'name' => $params['name'],
            'used_name' => $params['used_name'],
            'credit_code' => $params['credit_code'],
            'company_status' => $params['company_status'],
            'type' => $params['type'],
            'owner' => $params['owner'],
            'iso' => json_encode($params['iso'], JSON_UNESCAPED_UNICODE),
            'business_lic' => json_encode($params['business_lic'], JSON_UNESCAPED_UNICODE),
            'industry_category' => $params['industry_category'],
            'production_time' => $params['production_time'],
            'annual_scale' => $params['annual_scale'],
            'province' => $params['province'],
            'city' => $params['city'],
            'area' => $params['area'],
            'address' => $params['address'],
            'contacts' => $params['contacts'] ?? '',
            'tel' => $params['tel'] ?? '',
            'mobile' => $params['mobile'] ?? 0,
            'email' => $params['email'] ?? '',
            'latitude' => $params['latitude'] ?? '',
            'longitude' => $params['longitude'] ?? '',
            'remark' => $params['remark'] ?? '',
        ];
        DB::beginTransaction();
        try {
            $result = $this->_companyModel->add($addData);
            if (!$result) {
                DB::rollBack();
                throw new CompanyException(40003);
            }
            User::updateCompanyId($result);
            DB::commit();
            return ['company_id' => $result];
        } catch (\Exception $e) {
            DB::rollBack();
            throw new CompanyException(40003);
        }
    }

    /**
     * 获取企业信息详情
     * @return mixed
     * @throws CompanyException
     */
    public function getCompanyDetail()
    {
        $userInfo = getUserInfo(['company_id']);
        $where = [
            'id' => $userInfo['company_id'],
        ];
        $result = $this->_companyModel->getOne($where);
        if (is_null($result)) {
            throw new CompanyException(40004);
        }
        return $result;
    }

    /**
     * 修改企业信息
     * @param $params
     * @return array
     * @throws CompanyException
     */
    public function updateCompany($params)
    {
        $userInfo = getUserInfo();
        if ($userInfo['company_id'] <= 0) {
            throw new CompanyException(40008);
        }
        $this->_validate($params, $userInfo['company_id']);
        $where = [
            'id' => $userInfo['company_id'],
        ];
        $companyInfo = $this->_companyModel->getOne($where);
        if (is_null($companyInfo)) {
            throw new CompanyException(40004);
        }
        $updateData = [];
        $returnData = ['company_id' => $companyInfo['id']];
        foreach ($companyInfo as $field => $value) {
            if (isset($params[$field])) {
                $updateData[$field] = $params[$field];
            }
        }
        if (!empty($updateData)) {
            $result = $this->_companyModel->updateData($updateData, $where);
            if (!$result) {
                throw new CompanyException(40009);
            }
        }
        return $returnData;
    }

    /**
     * 添加产品
     * @param $params
     * @return array
     * @throws CompanyException
     */
    public function addProduct($params)
    {
        $userInfo = getUserInfo(['company_id']);
        $this->_proValidate($params);
        $addData = [
            'company_id' => $userInfo['company_id'],
            'name' => $params['name'],
            'annual_output' => $params['annual_output'] ?? 0,
            'source_material' => $params['source_material'] ?? '',
            'unit' => $params['unit'] ?? 0,
            'consume' => $params['consume'] ?? 0,
            'process_flow' => $params['process_flow'] ?? '',
            'consume_unit' => $params['consume_unit'] ?? 0,
            'remark' => $params['remark'] ?? '',
        ];
        try {
            $result = $this->_productModel->add($addData);
            if (!$result) {
                throw new CompanyException(40010);
            }
            return ['id' => $result];
        } catch (\Exception $e) {
            throw new CompanyException(40010);
        }
    }

    /**
     * 获取产品列表
     * @param $params
     * @param int $page
     * @param int $pageSize
     * @param array $orderBy
     * @param array $fields
     * @return mixed
     */
    public function getProductList($params, $page = 1, $pageSize = 10, $orderBy = [], $fields = [])
    {
        $userInfo = getUserInfo(['company_id']);
        $where = [
            'company_id' => $userInfo['company_id'],
        ];
        if (isset($params['name']) && $params['name']) {
            $where[] = ['name', 'LIKE', '%' . $params['name'] . '%'];
        }
        $result = $this->_productModel->getList($where, $fields, $page, $pageSize, $orderBy);
        return $result;
    }

    /**
     * 获取产品详情
     * @param $companyId
     * @param $id
     * @param array $fields
     * @return mixed
     * @throws CompanyException
     */
    public function getProductDetail($companyId, $id, $fields = [])
    {
        $where = [
            'company_id' => $companyId,
            'id' => $id,
        ];
        $result = $this->_productModel->getOne($where, $fields);
        if (is_null($result)) {
            throw new CompanyException(40011);
        }
        return $result;
    }

    /**
     * 修改产品
     * @param $companyId
     * @param $id
     * @param $params
     * @return array
     * @throws CompanyException
     */
    public function updateProduct($companyId, $id, $params)
    {
        $where = [
            'company_id' => $companyId,
            'id' => $id,
        ];
        $isExist = $this->_productModel->getOne($where);
        if (is_null($isExist)) {
            throw new CompanyException(40011);
        }
        $updateData = [];
        foreach ($isExist as $field => $value) {
            if (isset($params[$field])) {
                $updateData[$field] = $params[$field];
            }
        }
        if (!empty($updateData)) {
            $result = $this->_productModel->updateData($updateData, $where);
            if (!$result) {
                throw new CompanyException(40012);
            }
        }
        return ['id' => $isExist['id']];
    }

    /**
     * 删除产品
     * @param $companyId
     * @param $id
     * @return mixed
     * @throws CompanyException
     */
    public function delProduct($companyId, $id)
    {
        $where = [
            'company_id' => $companyId,
            'id' => $id,
        ];
        $isExist = $this->_productModel->getOne($where, ['id']);
        if (is_null($isExist)) {
            throw new CompanyException(40011);
        }
        $result = $this->_productModel->deleteByFields($where);
        if (!$result) {
            throw new CompanyException(40013);
        }
        return $result;
    }

    /**
     * 添加企业信息参数验证
     * @param $params
     * @throws CompanyException
     */
    private function _validate($params, $id = 0)
    {
        $isExistWhere = [
            'name' => $params['name'],
        ];
        if ($id > 0) {
            $isExistWhere[] = ['id', '<>', $id];
        }
        $isExist = $this->_companyModel->getOne($isExistWhere, ['id']);
        if (!is_null($isExist)) {
            throw new CompanyException(40002, ['companyName' => $params['name']]);
        }

        if (isset($params['mobile']) && !isMobile($params['mobile'])) {
            throw new CompanyException(40005);
        }
        if (isset($params['email']) && !isEmail($params['email'])) {
            throw new CompanyException(40006);
        }
        if (isset($params['company_status']) && !in_array($params['company_status'], array_keys($this->getConst('company_status')))) {
            throw new CompanyException(40007, ['fileName' => 'company_status']);
        }
        $legalBoolFields = ['is_env_statistics', 'is_pass_iso',];
        foreach ($legalBoolFields as $checkBool) {
            if (isset($params[$checkBool]) && !in_array($params[$checkBool], array_keys(self::COMMON_LEGAL_BOOL))) {
                throw new CompanyException(40007, ['fileName' => $checkBool]);
            }
        }
        if (isset($params['pollution_type']) && $params['pollution_type'] && !in_array($params['pollution_type'], array_keys($this->getConst('pollution_type')))) {
            throw new CompanyException(40007, ['fileName' => 'pollution_type']);
        }
    }

    private function _proValidate($params, $id = 0)
    {

    }
}
