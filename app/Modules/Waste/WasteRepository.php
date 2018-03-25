<?php

namespace App\Modules\Waste;

use App\Modules\Common\CommonRepository;
use App\Modules\Role\Facades\Role;
use App\Modules\Setting\Facades\Setting;
use App\Modules\Waste\Exceptions\WasteException;
use App\Modules\Waste\Facades\Waste;

class WasteRepository extends CommonRepository
{
    private $_wasteMaterialModel;
    private $_wasteGasModel;
    private $_wasteGasTubeModel;

    public function __construct(
        EloquentWasteMaterialModel $wasteMaterialModel,
        EloquentWasteGasModel $wasteGasModel,
        EloquentWasteGasTubeModel $wasteGasTubeModel
    )
    {
        $this->_wasteMaterialModel = $wasteMaterialModel;
        $this->_wasteGasModel = $wasteGasModel;
        $this->_wasteGasTubeModel = $wasteGasTubeModel;
    }

    /**
     * 添加危废信息
     * @param $params
     * @return array
     * @throws WasteException
     */
    public function addWasteMaterial($params)
    {
        $userInfo = getUserInfo(['company_id']);
        $addData = [
            'company_id' => $userInfo['company_id'],
            'waste_category' => $params['waste_category'],
            'industry' => $params['industry'],
            'waste_code' => $params['waste_code'],
            'waste_name' => $params['waste_name'] ?? '',
            'commonly_called' => $params['commonly_called'] ?? '',
            'harmful_staff' => $params['harmful_staff'] ?? '',
            'waste_shape' => $params['waste_shape'] ?? 0,
            'waste_type' => $params['waste_type'] ?? 0,
            'waste_trait' => $params['waste_trait'] ?? 0,
            'annual_scale' => $params['annual_scale'] ?? 0,
            'handle_company' => $params['handle_company'] ?? '',
            'handle_way' => $params['handle_way'] ?? '',
            'transport_unit' => $params['transport_unit'] ?? '',
            'remark' => $params['remark'] ?? '',
        ];
        try {
            $result = $this->_wasteMaterialModel->add($addData);
            if (!$result) {
                throw new WasteException(60001);
            }
            return ['id' => $result];
        } catch (\Exception $e) {
            throw new WasteException(60001);
        }
    }

    /**
     * 获取危废信息列表
     * @param $params
     * @param $page
     * @param $pageSize
     * @param $orderBy
     * @param array $fileds
     * @return mixed
     */
    public function getWasteMaterialList($params, $page, $pageSize, $orderBy, $fileds = [])
    {
        $where = [];
        if (isset($params['waste_code']) && $params['waste_code']) {
            $where['waste_code'] = $params['waste_code'];
        }
        $result = $this->_wasteMaterialModel->getList($where, $fileds, $page, $pageSize, $orderBy);
        if (isset($result['rows']) && !empty($result['rows'])) {
            $wasteTypeIds = [];
            foreach ($result['rows'] as $item) {
                $wasteTypeIds[] = $item['waste_category'];
                $wasteTypeIds[] = $item['industry'];
                $wasteTypeIds[] = $item['waste_code'];
            }
            $wasteTypeIds = array_unique($wasteTypeIds);
            $wasteTypeInfo = Setting::searchWasteTypeForList($wasteTypeIds, ['name'], 'id');
            foreach ($result['rows'] as &$row) {
                $row['waste_category_name'] = isset($wasteTypeInfo[$row['waste_category']]) ? $wasteTypeInfo[$row['waste_category']]['name'] : '';
                $row['industry_name'] = isset($wasteTypeInfo[$row['industry']]) ? $wasteTypeInfo[$row['industry']]['name'] : '';
                $row['waste_code_name'] = isset($wasteTypeInfo[$row['waste_code']]) ? $wasteTypeInfo[$row['waste_code']]['name'] : '';
            }
        }
        return $result;
    }

    /**
     * 获取危废信息详情
     * @param $id
     * @param array $fields
     * @return mixed
     * @throws WasteException
     */
    public function getWasteMaterialDetail($id, $fields = [])
    {
        $where = [
            'id' => $id,
        ];
        $result = $this->_wasteMaterialModel->getOne($where);
        $this->_checkWastePermission($result['company_id']);
        $wasteTypeIds = [$result['waste_category'], $result['industry'], $result['waste_code']];
        $wasteTypeInfo = Setting::searchWasteTypeForList($wasteTypeIds, ['name'], 'id');
        $result['waste_category_name'] = isset($wasteTypeInfo[$result['waste_category']]) ? $wasteTypeInfo[$result['waste_category']]['name'] : '';
        $result['industry_name'] = isset($wasteTypeInfo[$result['industry']]) ? $wasteTypeInfo[$result['industry']]['name'] : '';
        $result['waste_code_name'] = isset($wasteTypeInfo[$result['waste_code']]) ? $wasteTypeInfo[$result['waste_code']]['name'] : '';
        return $result;
    }

    /**
     * 更新危废信息
     * @param $id
     * @param $params
     * @return array
     * @throws WasteException
     */
    public function updateWasteMaterial($id, $params)
    {
        $where = [
            'id' => $id,
        ];
        $model = $this->_wasteMaterialModel->where($where)->first();
        if (is_null($model)) {
            throw new WasteException(60003);
        }
        $this->_checkWastePermission($model->company_id);
        $updateData = [];
        $guardFillble = ['id'];
        foreach ($params as $fileld => $value) {
            if (in_array($fileld, $guardFillble)) {
                continue;
            }
            if (isset($model->$fileld)) {
                $updateData[$fileld] = $params[$fileld];
            }
        }
        $returnData = ['id' => $id];
        if (!empty($updateData)) {
            try {
                $result = $model->update($updateData);
                if ($result === false) {
                    throw new WasteException(60004);
                }
            } catch (\Exception $e) {
                throw new WasteException(60004);
            }
        }
        return $returnData;
    }

    /**
     * 删除危废信息
     * @param $id
     * @return bool
     * @throws WasteException
     */
    public function delWasteMaterial($id)
    {
        $where = [
            'id' => $id,
        ];
        $isExist = $this->_wasteMaterialModel->getOne($where, ['company_id']);
        if (is_null($isExist)) {
            throw new WasteException(60003);
        }
        $this->_checkWastePermission($isExist['company_id']);
        try {
            $result = $this->_wasteMaterialModel->deleteData($id);
            if ($result === false) {
                throw new WasteException(60005);
            }
            return true;
        } catch (\Exception $e) {
            throw new WasteException(60005);
        }
    }

    /**
     * 添加排放口
     * @param $params
     * @return array
     * @throws WasteException
     */
    public function addWasteGasTube($params)
    {
        $userInfo = getUserInfo();
        $addData = [
            'company_id' => $userInfo['company_id'],
            'item_no' => $params['item_no'],
            'height' => $params['height'] ?? 0,
            'pics' => isset($params['pics']) ? json_encode($params['pics'], JSON_UNESCAPED_UNICODE) : '[]',
            'check' => isset($params['check']) ? json_encode($params['check'], JSON_UNESCAPED_UNICODE) : '[]',
            'remark' => $params['remark'] ?? '',
        ];
        try {
            $result = $this->_wasteGasTubeModel->add($addData);
            if (!$result) {
                throw new WasteException(60007);
            }
            return ['id' => $result];
        } catch (\Exception $e) {
            throw new WasteException(60007);
        }
    }

    /**
     * 修改排放口
     * @param $id
     * @param $params
     * @return array
     * @throws WasteException
     */
    public function updateWasteGasTube($id, $params)
    {
        $where = [
            'id' => $id,
        ];
        $model = $this->_wasteGasTubeModel->where($where)->first();
        if (is_null($model)) {
            throw new WasteException(60008);
        }
        $this->_checkWastePermission($model->company_id);
        $updateData = [];
        $guardFillble = ['id'];
        foreach ($params as $field => $value) {
            if (in_array($field, $guardFillble)) {
                continue;
            }
            if (isset($model->$field)) {
                if ($field == 'pics' || $field == 'check') {
                    $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                }
                $updateData[$field] = $value;
            }
        }
        try {
            $returnData = ['id' => $id];
            if (!empty($updateData)) {
                $result = $model->update($updateData);
                if ($result === false) {
                    throw new WasteException(60009);
                }
            }
            return $returnData;
        } catch (\Exception $e) {
            throw new WasteException(60009);
        }
    }

    /**
     * 添加废气信息
     * @param $params
     * @return array
     * @throws WasteException
     */
    public function addWasteGas($params)
    {
        if (!isset(Waste::WASTE_GAS_TYPE_MAP[$params['type']])) {
            throw new WasteException(60006);
        }
        $userInfo = getUserInfo();
        $addData = [
            'company_id' => $userInfo['company_id'],
            'type' => $params['type'],
            'waste_name' => $params['waste_name'] ?? '',
            'fules_type' => $params['fules_type'] ?? '',
            'fules_element' => $params['fules_element'] ?? '',
            'sulfur_rate' => $params['sulfur_rate'] ?? 0,
            'gas_discharge' => $params['gas_discharge'] ?? 0,
            'discharge_level' => $params['discharge_level'] ?? 0,
            'tube_no' => $params['tube_no'] ?? '',
            'technique' => $params['technique'] ?? '[]',
            'installations' => $params['installations'] ?? '[]',
            'technique_pic' => $params['technique_pic'] ?? '[]',
            'remark' => $params['remark'] ?? '',
        ];
        try {
            $result = $this->_wasteGasModel->add($addData);
            if (!$result) {
                throw new WasteException(60001);
            }
            return ['id' => $result];
        } catch (\Exception $e) {
            throw new WasteException(60001);
        }
    }

    /**
     * 更新废气信息
     * @param $id
     * @param $params
     * @return array
     * @throws WasteException
     */
    public function updateWasteGas($id, $params)
    {
        $where = [
            'id' => $id,
        ];
        $model = $this->_wasteGasModel->where($where)->first();
        if (is_null($model)) {
            throw new WasteException(60003);
        }
        $isExist = $model->toArray();
        $this->_checkWastePermission($isExist['company_id']);
        $updateData = [];
        foreach ($isExist as $fileld => $value) {
            if (isset($params[$fileld])) {
                $updateData[$fileld] = $params[$fileld];
            }
        }
        $returnData = ['id' => $id];
        if (!empty($updateData)) {
            try {
                $result = $model->update($updateData);
                if ($result === false) {
                    throw new WasteException(60004);
                }
            } catch (\Exception $e) {
                throw new WasteException(60004);
            }
        }
        return $returnData;
    }

    /**
     * 删除危废信息
     * @param $id
     * @return bool
     * @throws WasteException
     */
    public function delWasteGas($id)
    {
        $where = [
            'id' => $id,
        ];
        $model = $this->_wasteGasModel->where($where)->first();
        if (is_null($model)) {
            throw new WasteException(60003);
        }
        $isExist = $model->toArray();
        $this->_checkWastePermission($isExist['company_id']);
        try {
            $result = $model->delete();
            if ($result === false) {
                throw new WasteException(60005);
            }
            return true;
        } catch (\Exception $e) {
            throw new WasteException(60005);
        }
    }

    /**
     * 检验权限
     * @param $companyId
     * @throws WasteException
     */
    private function _checkWastePermission($companyId)
    {
        $userInfo = getUserInfo();
        $roleInfo = Role::getRoleInfo($userInfo['role_id']);
        if ($roleInfo == Role::ROLE_COMMON_TYPE && $companyId != $userInfo['company_id']) {
            throw new WasteException(60002);
        };
    }
}
