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

    public function __construct(
        EloquentCompanyModel $eloquentCompanyModel
    )
    {
        $this->_companyModel = $eloquentCompanyModel;
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
            'company_status' => $params['company_status'],
            'credit_code' => $params['credit_code'],
            'used_name' => $params['used_name'] ?? '',
            'owner' => $params['owner'],
            'type' => $params['type'],
            'is_env_statistics' => $params['is_env_statistics'] ?? 1,
            'is_pass_iso' => $params['is_pass_iso'] ?? 1,
            'contacts' => $params['contacts'] ?? '',
            'tel' => $params['tel'] ?? '',
            'mobile' => $params['mobile'] ?? 0,
            'email' => $params['email'] ?? '',
            'latitude' => $params['latitude'] ?? '',
            'longitude' => $params['longitude'] ?? '',
            'address' => $params['address'] ?? '',
            'zip_code' => $params['zip_code'] ?? 0,
            'area' => $params['area'] ?? '',
            'industry_category' => $params['industry_category'] ?? 1,
            'production_time' => $params['production_time'] ?? 0,
            'annual_output' => $params['annual_output'] ?? 0,
            'investment' => $params['investment'] ?? 0,
            'env_investment' => $params['env_investment'] ?? 0,
            'annual_scale' => $params['annual_scale'] ?? 0,
            'eia_code' => $params['eia_code'] ?? '',
            'eia_unit' => $params['eia_unit'] ?? '',
            'env_approve_code' => $params['env_approve_code'] ?? '',
            'env_approve_unit' => $params['env_approve_unit'] ?? '',
            'pollution_lic_code' => $params['pollution_lic_code'] ?? '',
            'pollution_lic_date' => $params['pollution_lic_date'] ?? '',
            'pollution_type' => $params['pollution_type'] ?? 0,
            'pollution_names' => $params['pollution_names'] ?? '',
            'pollution_outlet_no' => $params['pollution_outlet_no'] ?? '',
            'radiant_lic_code' => $params['radiant_lic_code'] ?? '',
            'business_lic' => $params['business_lic'] ?? '',
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
        } catch (Exception $e) {
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
            $isExistWhere[] = ['id','<>',$id];
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
}
