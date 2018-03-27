<?php

namespace App\Modules\Waste;

use App\Modules\Common\CommonRepository;
use App\Modules\Files\Facades\Files;
use App\Modules\Role\Facades\Role;
use App\Modules\Setting\Facades\Setting;
use App\Modules\Waste\Exceptions\WasteException;
use App\Modules\Waste\Facades\Waste;

class WasteRepository extends CommonRepository
{
    private $_wasteMaterialModel;
    private $_wasteGasModel;
    private $_wasteTubeModel;
    private $_wasteWaterModel;
    private $_noiseModel;

    public function __construct(
        EloquentWasteMaterialModel $wasteMaterialModel,
        EloquentWasteGasModel $wasteGasModel,
        EloquentWasteTubeModel $wasteTubeModel,
        EloquentWasteWaterModel $wasteWaterModel,
        EloquentNoiseModel $noiseModel
    )
    {
        $this->_wasteMaterialModel = $wasteMaterialModel;
        $this->_wasteGasModel = $wasteGasModel;
        $this->_wasteTubeModel = $wasteTubeModel;
        $this->_wasteWaterModel = $wasteWaterModel;
        $this->_noiseModel = $noiseModel;
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
        if (!isset(Waste::WASTE_TUBE_TYPE_MAP[$params['type']])) {
            throw new WasteException(60012);
        }
        $addData = [
            'company_id' => $userInfo['company_id'],
            'type' => $params['type'],
            'item_no' => $params['item_no'],
            'height' => $params['height'] ?? 0,
            'pics' => isset($params['pics']) ? json_encode($params['pics'], JSON_UNESCAPED_UNICODE) : '[]',
            'check' => isset($params['check']) ? json_encode($params['check'], JSON_UNESCAPED_UNICODE) : '[]',
            'remark' => $params['remark'] ?? '',
        ];
        try {
            $result = $this->_wasteTubeModel->add($addData);
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
        $model = $this->_wasteTubeModel->where($where)->first();
        if (is_null($model)) {
            throw new WasteException(60008);
        }
        $this->_checkWastePermission($model->company_id);
        $updateData = [];
        $guardFillble = ['id', 'type'];
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
     * 获取排放口详情
     * @param $id
     * @return array|mixed
     * @throws WasteException
     */
    public function getWasteGasTubeDetail($id)
    {
        $where = [
            'id' => $id,
        ];
        $result = $this->_wasteTubeModel->getOne($where);
        if (is_null($result)) {
            throw new WasteException(60008);
        }
        $this->_checkWastePermission($result['company_id']);
        $result['pics'] = json_decode($result['pics'], true);
        $result['check'] = json_decode($result['check'], true);
        $allFileIds = array_merge($result['pics'], $result['check']);
        $result['pics_files'] = $result['check_files'] = [];
        if (!empty($allFileIds)) {
            $filesInfo = Files::searchFilesForList($allFileIds);
            $result = array_merge($result, $filesInfo);
        }
        return $result;
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
            'tube_id' => $params['tube_id'],
            'type' => $params['type'],
            'waste_name' => $params['waste_name'] ?? '',
            'gas_discharge' => $params['gas_discharge'] ?? 0,
            'equipment' => $params['equipment'] ?? '',
            'discharge_level' => $params['discharge_level'] ?? 0,
            'technique' => $params['technique'] ?? '',
            'installations' => $params['installations'] ?? 0,
            'fules_type' => $params['fules_type'] ?? '',
            'fules_element' => $params['fules_element'] ?? '',
            'sulfur_rate' => $params['sulfur_rate'] ?? 0,
            'technique_pic' => isset($params['technique_pic']) ? json_encode($params['technique_pic']) : '[]',
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
        $this->_checkWastePermission($model->company_id);
        $updateData = [];
        $guardFillble = ['id'];
        foreach ($params as $fileld => $value) {
            if (in_array($fileld, $guardFillble)) {
                continue;
            }
            if ($fileld == 'technique_pic') {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }
            if (isset($model->$fileld)) {
                $updateData[$fileld] = $value;
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
     * 获取排气口下拉框
     * @return mixed
     */
    public function getWasteGasTubeCombo($params)
    {
        $where = [
            'company_id' => getUserInfo()['company_id'],
            'type' => $params['type'],
        ];
        $fields = ['id', 'item_no'];
        $result = $this->_wasteTubeModel->searchData($where, $fields);
        return $result;
    }

    /**
     * 删除排放口
     * @param $id
     * @return bool
     * @throws WasteException
     */
    public function delWasteGasTube($id)
    {
        $where = [
            'id' => $id,
        ];
        $model = $this->_wasteTubeModel->where($where)->first();
        if (is_null($model)) {
            throw new WasteException(60008);
        }
        $this->_checkWastePermission($model->company_id);
        try {
            $result = $model->delete();
            if ($result === false) {
                throw new WasteException(60010);
            }
            return true;
        } catch (\Exception $e) {
            throw new WasteException(60010);
        }
    }

    /**
     * 获取废气信息详情
     * @param $id
     * @return mixed
     * @throws WasteException
     */
    public function getWasteGasDetail($companyId, $id)
    {
        $where = [
            'id' => $id,
            'company_id' => $companyId,
        ];
        $result = $this->_wasteGasModel->getOne($where);
        if (is_null($result)) {
            throw new WasteException(60011);
        }
        $this->_checkWastePermission($result['company_id']);
        $tubeWhere = [
            'id' => $result['tube_id'],
        ];
        $result['type_name'] = Waste::WASTE_GAS_TYPE_MAP[$result['type']] ?? '';
        $gasTubeInfo = $this->_wasteTubeModel->getOne($tubeWhere, ['item_no']);
        if (is_null($gasTubeInfo)) {
            throw new WasteException(60008);
        }
        $result['item_no'] = is_null($gasTubeInfo) ? '' : $gasTubeInfo['item_no'];
        $result['technique_pic'] = json_decode($result['technique_pic'], true);
        $result['technique_pic_files'] = [];
        if (!empty($result['technique_pic'])) {
            $picInfo = Files::searchFilesForList($result['technique_pic'], 2);
            $result['technique_pic_files'] = array_values($picInfo);
        }
        return $result;
    }

    /**
     * 获取废气列表
     * @param $params
     * @return mixed
     */
    public function getWasteGasList($params)
    {
        $where = [
            'type' => Waste::WASTE_GAS_TUBE_TYPE,
            'built_in' => [
                'with' => 'gases',
            ]
        ];
        $tubeFields = ['id', 'item_no', 'height', 'pics', 'check'];
        $gasFields = ['id', 'type', 'waste_name', 'gas_discharge', 'discharge_level', 'equipment', 'technique', 'installations', 'remark'];
        $result = $this->_wasteTubeModel->getList($where, $tubeFields);
        if (isset($result['rows']) && !empty($result['rows'])) {
            $allFileIds = [];
            foreach ($result['rows'] as &$row) {
                $row['pics_files'] = [];
                $row['check_files'] = [];
                $row['pics'] = json_decode($row['pics'], true);
                $row['check'] = json_decode($row['check'], true);
                $allFileIds = array_merge($allFileIds, $row['pics'], $row['check']);
                if (!empty($row['gases'])) {
                    $newGas = [];
                    foreach ($row['gases'] as $gas) {
                        $tmp = [
                            'type_name' => Waste::WASTE_GAS_TYPE_MAP[$gas['type']] ?? '',
                        ];
                        foreach ($gasFields as $gasField) {
                            if (isset($gas[$gasField])) {
                                $tmp[$gasField] = $gas[$gasField];
                            }
                        }
                        $newGas[] = $tmp;
                    }
                    $row['gases'] = $newGas;
                }
            }
            if (!empty($allFileIds)) {
                $filesInfo = Files::searchFilesForList($allFileIds, 2);
                foreach ($result['rows'] as &$item) {
                    if (!empty($item['pics'])) {
                        foreach ($item['pics'] as $pic) {
                            if (isset($filesInfo[$pic])) {
                                $item['pics_files'][] = $filesInfo[$pic];
                            }
                        }
                    }
                    if (!empty($item['check'])) {
                        foreach ($item['check'] as $check) {
                            if (isset($filesInfo[$check])) {
                                $item['check_files'][] = $filesInfo[$check];
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 添加废水信息
     * @param $params
     * @return array
     * @throws WasteException
     */
    public function addWasterWater($params)
    {
        if (!isset(Waste::WASTE_WATER_TYPE_MAP[$params['type']])) {
            throw new WasteException(60013);
        }
        $userInfo = getUserInfo();
        $addData = [
            'company_id' => $userInfo['company_id'],
            'tube_id' => $params['tube_id'],
            'type' => $params['type'],
            'waste_name' => $params['waste_name'] ?? '',
            'water_discharge' => $params['water_discharge'] ?? 0,
            'discharge_level' => $params['discharge_level'] ?? 0,
            'technique' => $params['technique'] ?? '',
            'water_direction' => $params['water_direction'] ?? '',
            'waste_plants' => $params['waste_plants'] ?? '',
            'daily_process' => $params['daily_process'] ?? 0,
            'remark' => $params['remark'] ?? '',
        ];
        try {
            $result = $this->_wasteWaterModel->add($addData);
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
    public function updateWasteWater($id, $params)
    {
        $where = [
            'id' => $id,
        ];
        if (isset($params['type']) && !isset(Waste::WASTE_WATER_TYPE_MAP[$params['type']])) {
            throw new WasteException(60013);
        }
        $model = $this->_wasteWaterModel->where($where)->first();
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
                $updateData[$fileld] = $value;
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
     * 获取废水信息详情
     * @param $id
     * @return mixed
     * @throws WasteException
     */
    public function getWasteWaterDetail($companyId, $id)
    {
        $where = [
            'id' => $id,
            'company_id' => $companyId,
        ];
        $result = $this->_wasteWaterModel->getOne($where);
        if (is_null($result)) {
            throw new WasteException(60014);
        }
        $this->_checkWastePermission($result['company_id']);
        $tubeWhere = [
            'id' => $result['tube_id'],
        ];
        $result['type_name'] = Waste::WASTE_WATER_TYPE_MAP[$result['type']] ?? '';
        $gasTubeInfo = $this->_wasteTubeModel->getOne($tubeWhere, ['item_no']);
        if (is_null($gasTubeInfo)) {
            throw new WasteException(60008);
        }
        $result['item_no'] = is_null($gasTubeInfo) ? '' : $gasTubeInfo['item_no'];
        return $result;
    }

    /**
     * 获取废水信息列表
     * @param $params
     */
    public function getWasteWaterList($params)
    {
        $where = [
            'type' => Waste::WASTE_WATER_TUBE_TYPE,
            'built_in' => [
                'with' => 'water',
            ]
        ];
        $tubeFields = ['id', 'item_no', 'height', 'pics', 'check'];
        $waterFields = ['id', 'type', 'waste_name', 'water_discharge', 'discharge_level', 'water_direction', 'technique', 'waste_plants', 'daily_process', 'remark'];
        $result = $this->_wasteTubeModel->getList($where, $tubeFields);
        if (isset($result['rows']) && !empty($result['rows'])) {
            $allFileIds = [];
            foreach ($result['rows'] as &$row) {
                $row['pics_files'] = [];
                $row['check_files'] = [];
                $row['pics'] = json_decode($row['pics'], true);
                $row['check'] = json_decode($row['check'], true);
                $allFileIds = array_merge($allFileIds, $row['pics'], $row['check']);
                if (!empty($row['water'])) {
                    $newWater = [];
                    foreach ($row['water'] as $water) {
                        $tmp = [
                            'type_name' => Waste::WASTE_WATER_TYPE_MAP[$water['type']] ?? '',
                        ];
                        foreach ($waterFields as $waterField) {
                            if (isset($water[$waterField])) {
                                $tmp[$waterField] = $water[$waterField];
                            }
                        }
                        $newWater[] = $tmp;
                    }
                    $row['water'] = $newWater;
                }
            }
            if (!empty($allFileIds)) {
                $filesInfo = Files::searchFilesForList($allFileIds, 2);
                foreach ($result['rows'] as &$item) {
                    if (!empty($item['pics'])) {
                        foreach ($item['pics'] as $pic) {
                            if (isset($filesInfo[$pic])) {
                                $item['pics_files'][] = $filesInfo[$pic];
                            }
                        }
                    }
                    if (!empty($item['check'])) {
                        foreach ($item['check'] as $check) {
                            if (isset($filesInfo[$check])) {
                                $item['check_files'][] = $filesInfo[$check];
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 删除废水信息
     * @param $id
     * @return bool
     * @throws WasteException
     */
    public function delWasteWater($id)
    {
        $where = [
            'id' => $id,
        ];
        $model = $this->_wasteWaterModel->where($where)->first();
        if (is_null($model)) {
            throw new WasteException(60003);
        }
        $this->_checkWastePermission($model->company_id);
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
     * 添加噪音
     * @param $params
     * @return array
     * @throws WasteException
     */
    public function addNoise($params)
    {
        $userInfo = getUserInfo();
        $addData = [
            'company_id' => $userInfo['company_id'],
            'equipment' => $params['equipment'],
            'num' => $params['num'] ?? 0,
            'range' => $params['range'] ?? '',
            'stanard' => $params['stanard'] ?? 0,
            'is_done' => $params['is_done'] ?? 0,
            'technique' => $params['technique'] ?? '',
            'remark' => $params['remark'] ?? '',
        ];
        try {
            $result = $this->_noiseModel->add($addData);
            if (!$result) {
                throw new WasteException(60001);
            }
            return ['id' => $result];
        } catch (\Exception $e) {
            throw new WasteException(60001);
        }
    }

    /**
     * 更新噪音信息
     * @param $id
     * @param $params
     * @return array
     * @throws WasteException
     */
    public function updateNoise($id, $params)
    {
        $where = [
            'id' => $id,
        ];
        $model = $this->_noiseModel->where($where)->first();
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
                $updateData[$fileld] = $value;
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
     * 获取噪音详情
     * @param $companyId
     * @param $id
     * @return mixed
     * @throws WasteException
     */
    public function getNoiseDetail($companyId, $id)
    {
        $where = [
            'id' => $id,
            'company_id' => $companyId,
        ];
        $result = $this->_noiseModel->getOne($where);
        if (is_null($result)) {
            throw new WasteException(60015);
        }
        $this->_checkWastePermission($result['company_id']);
        return $result;
    }

    public function getNoiseList($params)
    {
        $where = [];
        if (isset($params['equipment']) && $params['equipment']){
            $where['built_in'] = [
                'LIKE'=>['equipment',$params['equipment']]
            ];
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
