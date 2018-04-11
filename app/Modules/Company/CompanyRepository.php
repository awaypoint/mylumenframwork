<?php

namespace App\Modules\Company;

use App\Modules\Common\CommonRepository;
use App\Modules\Company\Exceptions\CompanyException;
use App\Modules\Files\Facades\Files;
use App\Modules\Role\Facades\Role;
use App\Modules\User\Facades\User;
use Illuminate\Support\Facades\DB;

class CompanyRepository extends CommonRepository
{
    use CompanyTraits;

    private $_companyModel;
    private $_productModel;
    private $_factoryModel;

    public function __construct(
        EloquentCompanyModel $eloquentCompanyModel,
        EloquentProductModel $eloquentProductModel,
        EloquentCompanyFactoryModel $factoryModel
    )
    {
        $this->_companyModel = $eloquentCompanyModel;
        $this->_productModel = $eloquentProductModel;
        $this->_factoryModel = $factoryModel;
    }

    /**
     * 添加公司
     * @param $params
     * @throws CompanyException
     */
    public function addCompany($params)
    {
        $this->_validate($params);
        $addData = [
            'name' => $params['name'],
            'used_name' => $params['used_name'] ?? '',
            'credit_code' => $params['credit_code'] ?? '',
            'company_status' => $params['company_status'] ?? 0,
            'type' => $params['type'] ?? 0,
            'owner' => $params['owner'] ?? '',
            'iso' => isset($params['iso']) ? json_encode($params['iso'], JSON_UNESCAPED_UNICODE) : '[]',
            'business_lic' => isset($params['business_lic']) ? json_encode($params['business_lic'], JSON_UNESCAPED_UNICODE) : '[]',
            'industry_category' => $params['industry_category'] ?? 0,
            'production_time' => $params['production_time'] ?? 0,
            'annual_scale' => $params['annual_scale'] ?? '',
            'province_code' => $params['province_code'] ?? 350000,
            'province' => $params['province'] ?? '福建省',
            'city_code' => $params['city_code'] ?? 350200,
            'city' => $params['city'] ?? '厦门市',
            'area_code' => $params['area_code'] ?? 350211,
            'area' => $params['area'] ?? '集美区',
            'industrial_park_code' => $params['industrial_park_code'] ?? 0,
            'industrial_park' => $params['industrial_park'] ?? '',
            'address' => $params['address'] ?? '',
            'contacts' => $params['contacts'] ?? '',
            'tel' => $params['tel'] ?? '',
            'mobile' => $params['mobile'] ?? 0,
            'email' => $params['email'] ?? '',
            'latitude' => $params['latitude'] ?? '',
            'longitude' => $params['longitude'] ?? '',
            'remark' => $params['remark'] ?? '',
        ];
        try {
            $result = $this->_companyModel->add($addData);
            if (!$result) {
                throw new CompanyException(40003);
            }
            return ['id' => $result];
        } catch (\Exception $e) {
            throw new CompanyException(40003);
        }
    }

    /**
     * 获取企业信息详情
     * @return mixed
     * @throws CompanyException
     */
    public function getCompanyDetail($params)
    {
        $userInfo = getUserInfo();
        $companyId = isset($params['company_id']) && $params['company_id'] ? $params['company_id'] : $userInfo['company_id'];
        checkCompanyPermission($companyId);
        $where = [
            'id' => $companyId,
            'built_in' => [
                'with' => 'factory'
            ]
        ];
        $factoryFields = ['id', 'address'];
        $result = $this->_companyModel->getOne($where);
        if (is_null($result)) {
            throw new CompanyException(40004);
        }
        $result['iso'] = json_decode($result['iso'], true);
        $result['business_lic'] = json_decode($result['business_lic'], true);
        $result['iso_files'] = $result['business_lic_files'] = [];
        $fileIds = array_merge($result['iso'], $result['business_lic']);
        if (!empty($fileIds)) {
            $fileInfo = Files::searchFilesForList($fileIds);
            $result = array_merge($result, $fileInfo);
        }
        $result['production_time'] = intval($result['production_time']);
        if (!empty($result['factory'])) {
            foreach ($result['factory'] as &$factory) {
                foreach ($factory as $field => $value) {
                    if (!in_array($field, $factoryFields)) {
                        unset($factory[$field]);
                    }
                }
            }
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
        $companyId = isset($params['company_id']) ? $params['company_id'] : $userInfo['company_id'];
        checkCompanyPermission($companyId);
        $this->_validate($params, $companyId);
        $where = [
            'id' => $companyId,
        ];
        $model = $this->_companyModel->where($where)->first();
        if (is_null($model)) {
            throw new CompanyException(40004);
        }
        $combine = $params['province_code'] | $params['city_code'] | $params['area_code'];
        if (isset($params['industrial_park_code']) && $params['industrial_park_code']) {
            $combine .= $params['industrial_park_code'];
        }
        $updateData = [];
        $returnData = ['company_id' => $model->id];
        //不可编辑名单
        $guardFillble = ['id', 'name'];
        foreach ($params as $field => $value) {
            if (in_array($field, $guardFillble)) {
                continue;
            }
            if (isset($model->$field)) {
                if ($field == 'iso' || $field == 'business_lic') {
                    $params[$field] = json_encode($params[$field], JSON_UNESCAPED_UNICODE);
                }
                $updateData[$field] = $params[$field];
            }
        }
        $updateData['combine'] = $combine;
        DB::beginTransaction();
        try {
            if (!empty($updateData)) {
                $result = $model->update($updateData);
                if (!$result) {
                    DB::rollBack();
                    throw new CompanyException(40009);
                }
            }
            $delFactory = $this->delCompanyFactory($model->id);
            if ($delFactory === false) {
                DB::rollBack();
                throw new CompanyException(40015);
            }
            if (isset($params['factory']) && !empty($params['factory'])) {
                $factoryResult = $this->addCompanyFactory($params['factory']);
                if (!$factoryResult) {
                    DB::rollBack();
                    throw new CompanyException(40014);
                }
            }
            DB::commit();
            return $returnData;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new CompanyException(40009);
        }
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
            'process_flow' => isset($params['process_flow']) ? json_encode($params['process_flow'], JSON_UNESCAPED_UNICODE) : '[]',
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
        $userInfo = getUserInfo();
        $where = [];
        if ($userInfo['role_type'] == Role::ROLE_ADMIN_TYPE) {
            $where['built_in'] = ['whereIn' => ['id', $userInfo['companies']]];
        }
        if ($userInfo['role_type'] == User::USER_COMMON_ROLE_TYPE) {
            $where['company_id'] = $userInfo['company_id'];
        } elseif (isset($params['company_id']) && $params['company_id']) {
            $where['company_id'] = $params['company_id'];
        }
        if (isset($params['name']) && $params['name']) {
            $where[] = ['name', 'LIKE', '%' . $params['name'] . '%'];
        }
        $result = $this->_productModel->getList($where, $fields, $page, $pageSize, $orderBy);
        if (isset($result['rows']) && !empty($result['rows'])) {
            $fileInfos = $companyInfos = $fileIds = $companyIds = [];
            foreach ($result['rows'] as $row) {
                if (!is_array($row['process_flow'])) {
                    $row['process_flow'] = json_decode($row['process_flow'], true);
                }
                $fileIds = array_merge($fileIds, $row['process_flow']);
                $companyIds[] = $row['company_id'];
            }
            $fileIds = array_unique($fileIds);
            $companyIds = array_unique($companyIds);
            if (!empty($fileIds)) {
                $fileInfos = Files::searchFilesForList($fileIds, 2);
            }
            if (!empty($companyIds)) {
                $companyInfos = $this->searchForList($companyIds, ['name'], 'id');
            }
            foreach ($result['rows'] as &$newRow) {
                if (!is_array($newRow['process_flow'])) {
                    $newRow['process_flow'] = json_decode($newRow['process_flow'], true);
                }
                $newRow['process_flow_files'] = [];
                if (!empty($newRow['process_flow'])) {
                    foreach ($newRow['process_flow'] as $flow) {
                        if (isset($fileInfos[$flow])) {
                            $newRow['process_flow_files'][] = $fileInfos[$flow];
                        }
                    }
                }
                $newRow['company_name'] = '';
                if (isset($companyInfos[$newRow['company_id']])) {
                    $newRow['company_name'] = $companyInfos[$newRow['company_id']]['name'];
                }
            }
        }
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
    public function getProductDetail($params, $id, $fields = [])
    {
        $where = [
            'id' => $id,
        ];
        $result = $this->_productModel->getOne($where, $fields);
        if (is_null($result)) {
            throw new CompanyException(40011);
        }
        checkCompanyPermission($result['company_id']);
        $result['process_flow'] = json_decode($result['process_flow'], true);
        $result['process_flow_files'] = [];
        if ($result['process_flow'] && $result['process_flow'] != '[]') {
            if (!is_array($result['process_flow'])) {
                $result['process_flow'] = json_decode($result['process_flow'], true);
            }
            $fileInfo = Files::searchFilesForList($result['process_flow']);
            $result = array_merge($result, $fileInfo);
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
    public function updateProduct($id, $params)
    {
        $this->_proValidate($params);
        $fileFields = ['process_flow'];
        dealFileFields($fileFields, $params);
        return $this->_productModel->up($id, $params);
    }

    /**
     * 删除产品
     * @param $companyId
     * @param $id
     * @return mixed
     * @throws CompanyException
     */
    public function delProduct($id)
    {
        return $this->_productModel->del($id);
    }

    /**
     * 添加分厂信息
     * @param $factorys
     * @return mixed
     */
    public function addCompanyFactory($factorys)
    {
        $addData = [];
        $nowTime = time();
        $userInfo = getUserInfo();
        foreach ($factorys as $factory) {
            if (!isset($factory['address'])) {
                throw new CompanyException(40016);
            }
            $tmp = [
                'company_id' => $userInfo['company_id'],
                'name' => '',
                'address' => $factory['address'],
                'created_at' => $nowTime,
                'updated_at' => $nowTime,
                'updated_by' => $userInfo['id'],
            ];
            $addData[] = $tmp;
        }
        return $this->_factoryModel->addBatch($addData);
    }

    /**
     * 删除分厂
     * @param $companyId
     * @return mixed
     */
    public function delCompanyFactory($companyId)
    {
        $where = [
            'company_id' => $companyId
        ];
        return $this->_factoryModel->deleteByFields($where, true);
    }

    /**
     * 获取公司列表
     * @param $params
     * @param int $page
     * @param int $pageSize
     * @param array $orderBy
     * @param array $fields
     * @return mixed
     * @throws CompanyException
     */
    public function getCompanyList($params, $page = 1, $pageSize = 10, $orderBy = [], $fields = [])
    {
        $userInfo = getUserInfo();
        $where = [];
        if ($userInfo['role_type'] == User::USER_COMMON_ROLE_TYPE) {
            throw new CompanyException(40017);
        }
        if ($userInfo['role_type'] == Role::ROLE_ADMIN_TYPE) {
            $where['built_in'] = ['whereIn' => ['id', $userInfo['companies']]];
        }
        if (isset($params['province_code']) && $params['province_code']) {
            $where['province_code'] = $params['province_code'];
        }
        if (isset($params['city_code']) && $params['city_code']) {
            $where['city_code'] = $params['city_code'];
        }
        if (isset($params['area_code']) && $params['area_code']) {
            $where['area_code'] = $params['area_code'];
        }
        if (isset($params['industrial_park_code']) && $params['industrial_park_code']) {
            $where['industrial_park_code'] = $params['industrial_park_code'];
        }
        if (isset($params['company_status']) && $params['company_status']) {
            $where['company_status'] = $params['company_status'];
        }
        if (isset($params['industry_category']) && $params['industry_category']) {
            $where['industry_category'] = $params['industry_category'];
        }
        if (isset($params['name']) && $params['name']) {
            $where[] = ['name', 'LIKE', '%' . $params['name'] . '%'];
        }
        $result = $this->_companyModel->getList($where, $fields, $page, $pageSize, $orderBy);
        return $result;
    }

    /**
     * 匹配
     * @param $ids
     * @param array $fields
     * @param string $indexKey
     * @param array $replaceWhere
     * @return array
     */
    public function searchForList($ids, $fields = [], $indexKey = '', $replaceWhere = [])
    {
        return $this->_companyModel->searchForList($ids, $fields, $indexKey, $replaceWhere);
    }

    /**
     * 获取企业下拉框
     * @return mixed
     */
    public function getCompanyCombo()
    {
        $userInfo = getUserInfo();
        $where = [];
        $fields = ['id', 'name'];
        if ($userInfo['role_type'] == Role::ROLE_ADMIN_TYPE) {
            $where['built_in'] = ['whereIn' => ['id', $userInfo['companies']]];
        }
        $result = $this->_companyModel->searchData($where, $fields);
        return $result;
    }

    /**
     * 获取行业统计数据
     * @return array
     */
    public function getIndustryReport()
    {
        $userInfo = getUserInfo();
        $where = [];
        $fieldStr = 'COUNT(DISTINCT id) AS company_num,industry_category';
        $result = $this->_companyModel->select(DB::raw($fieldStr))
            ->where($where);
        if ($userInfo['role_type'] == Role::ROLE_ADMIN_TYPE) {
            $result->whereIn('id', $userInfo['companies']);
        }
        $result = $result->groupBy('industry_category')->get()->toArray();
        return $result;
    }

    /**
     * 获取公司信息
     * @param $id
     * @param array $fields
     * @return mixed
     */
    public function getCompanyInfo($id, $fields = [])
    {
        $where = [
            'id' => $id,
        ];
        return $this->_companyModel->getOne($where, $fields);
    }

    /**
     * 获取公司总数
     * @return int
     */
    public function getCompanyCount()
    {
        $userInfo = getUserInfo();
        $model = $this->_companyModel;
        if ($userInfo['role_type'] == Role::ROLE_ADMIN_TYPE) {
            $model->whereIn('id', $userInfo['companies']);
        }
        return $model->count('id');
    }

    /**
     * 删除公司
     * @param $id
     * @return array
     * @throws CompanyException
     */
    public function delCompany($id)
    {
        if (getUserInfo()['role_type'] != Role::ROLE_SUPER_ADMIN_TYPE) {
            throw new CompanyException(40018);
        }
        DB::beginTransaction();
        try {
            $companyDelRes = $this->_companyModel->del($id);
            if (!$companyDelRes) {
                DB::rollBack();
                throw new CompanyException(40019);
            }
            $userDelRes = User::delUserByCompany($id);
            if ($userDelRes === false) {
                DB::rollBack();
                throw new CompanyException(40019);
            }
            DB::commit();
            return ['id' => $id];
        } catch (\Exception $e) {
            DB::rollBack();
            throw new CompanyException(40019);
        }
    }

    /**
     * 获取用户有权限的公司ids
     * @param $uid
     * @return array
     */
    public function getPermissionCompanies($uid)
    {
        $cityPermissions = User::getUserCityPermissions($uid, ['combine']);
        if (empty($cityPermissions)) {
            return [];
        }
        $model = $this->_companyModel->select(['id']);
        foreach ($cityPermissions as $combines) {
            $model->orWhere('combine', 'LIKE', $combines['combine'] . '%');
        }
        $result = $model->whereNull('deleted_at')->get()->toArray();
        if (!empty($result)) {
            $result = array_column($result, 'id');
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

        if (isset($params['mobile']) && $params['mobile'] && !isMobile($params['mobile'])) {
            throw new CompanyException(40005);
        }
        if (isset($params['email']) && $params['email'] && !isEmail($params['email'])) {
            throw new CompanyException(40006);
        }
        if (isset($params['iso'])) {
            if (!is_array($params['iso'])) {
                throw new CompanyException(40007, ['fileName' => 'iso认证']);
            }
            if (!empty($params['iso'])) {
                foreach ($params['iso'] as $param) {
                    if (!is_numeric($param)) {
                        throw new CompanyException(40007, ['fileName' => 'iso认证']);
                    }
                }
            }
        }
        if (isset($params['business_lic'])) {
            if (!is_array($params['business_lic'])) {
                throw new CompanyException(40007, ['fileName' => '营业执照']);
            }
            if (!empty($params['business_lic'])) {
                foreach ($params['business_lic'] as $param) {
                    if (!is_numeric($param)) {
                        throw new CompanyException(40007, ['fileName' => '营业执照']);
                    }
                }
            }
        }
    }

    /**
     * 添加产品验证参数
     * @param $params
     * @param int $id
     * @throws CompanyException
     */
    private function _proValidate($params, $id = 0)
    {
        if (isset($params['process_flow']) && !is_array($params['process_flow'])) {
            throw new CompanyException(40007, ['fileName' => 'process_flow']);
        }
    }
}
