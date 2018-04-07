<?php

namespace App\Modules\Waste;

use App\Modules\Common\CommonRepository;
use App\Modules\Company\Facades\Company;
use App\Modules\Files\Facades\Files;
use App\Modules\Role\Facades\Role;
use App\Modules\Setting\Facades\Setting;
use App\Modules\Waste\Exceptions\WasteException;
use App\Modules\Waste\Facades\Waste;
use Illuminate\Support\Facades\DB;

class WasteRepository extends CommonRepository
{
    private $_wasteMaterialModel;
    private $_wasteGasModel;
    private $_wasteTubeModel;
    private $_wasteWaterModel;
    private $_noiseModel;
    private $_nucleusModel;

    public function __construct(
        EloquentWasteMaterialModel $wasteMaterialModel,
        EloquentWasteGasModel $wasteGasModel,
        EloquentWasteTubeModel $wasteTubeModel,
        EloquentWasteWaterModel $wasteWaterModel,
        EloquentNoiseModel $noiseModel,
        EloquentNucleusModel $nucleusModel
    )
    {
        $this->_wasteMaterialModel = $wasteMaterialModel;
        $this->_wasteGasModel = $wasteGasModel;
        $this->_wasteTubeModel = $wasteTubeModel;
        $this->_wasteWaterModel = $wasteWaterModel;
        $this->_noiseModel = $noiseModel;
        $this->_nucleusModel = $nucleusModel;
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
        $userInfo = getUserInfo();
        $where = [];
        if ($userInfo['role_type'] == Role::ROLE_COMMON_TYPE) {
            $where['company_id'] = $userInfo['company_id'];
        } elseif (isset($params['company_id']) && $params['company_id']) {
            $where['company_id'] = $params['company_id'];
        }
        if (isset($params['name']) && $params['name']) {
            $where['built_in'] = [
                'whereRaw' => "(commonly_called LIKE '%" . $params['name'] . "%' OR waste_name LIKE '%" . $params['name'] . "%')",
            ];
        }
        $result = $this->_wasteMaterialModel->getList($where, $fileds, $page, $pageSize, $orderBy);
        if (isset($result['rows']) && !empty($result['rows'])) {
            $companyIds = $wasteTypeIds = [];
            foreach ($result['rows'] as $item) {
                $wasteTypeIds[] = $item['waste_category'];
                $wasteTypeIds[] = $item['industry'];
                $wasteTypeIds[] = $item['waste_code'];
                $companyIds[] = $item['company_id'];
            }
            $wasteTypeIds = array_unique($wasteTypeIds);
            $wasteTypeInfo = Setting::searchWasteTypeForList($wasteTypeIds, ['name', 'code'], 'id');
            $companyInfos = Company::searchCompanyForList(array_unique($companyIds), ['name'], 'id');
            foreach ($result['rows'] as &$row) {
                $row['waste_category_name'] = isset($wasteTypeInfo[$row['waste_category']]) ? $wasteTypeInfo[$row['waste_category']]['name'] : '';
                $row['industry_name'] = isset($wasteTypeInfo[$row['industry']]) ? $wasteTypeInfo[$row['industry']]['name'] : '';
                $row['waste_code_name'] = isset($wasteTypeInfo[$row['waste_code']]) ? $wasteTypeInfo[$row['waste_code']]['code'] : '';
                $row['company_name'] = isset($companyInfos[$row['company_id']]) ? $companyInfos[$row['company_id']]['name'] : '';
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
        checkCompanyPermission($result['company_id']);
        $wasteTypeIds = [$result['waste_category'], $result['industry'], $result['waste_code']];
        $wasteTypeInfo = Setting::searchWasteTypeForList($wasteTypeIds, ['name', 'code'], 'id');
        $result['waste_category_name'] = isset($wasteTypeInfo[$result['waste_category']]) ? $wasteTypeInfo[$result['waste_category']]['name'] : '';
        $result['industry_name'] = isset($wasteTypeInfo[$result['industry']]) ? $wasteTypeInfo[$result['industry']]['name'] : '';
        $result['waste_code_name'] = isset($wasteTypeInfo[$result['waste_code']]) ? $wasteTypeInfo[$result['waste_code']]['code'] : '';
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
        return $this->_wasteMaterialModel->up($id, $params);
    }

    /**
     * 删除危废信息
     * @param $id
     * @return bool
     * @throws WasteException
     */
    public function delWasteMaterial($id)
    {
        return $this->_wasteMaterialModel->del($id);
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
        $fileFields = ['pics', 'check'];
        dealFileFields($fileFields, $params);
        return $this->_wasteTubeModel->up($id, $params);
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
        checkCompanyPermission($result['company_id']);
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
        Setting::checkWasteExist($params['waste_name'], Setting::SETTING_WASTE_GAS_TYPE, ['id']);
        $userInfo = getUserInfo();
        $addData = [
            'company_id' => $userInfo['company_id'],
            'tube_id' => $params['tube_id'],
            'type' => $params['type'],
            'waste_name' => $params['waste_name'],
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
        if (isset($params['waste_name']) && $params['waste_name']) {
            Setting::checkWasteExist($params['waste_name'], Setting::SETTING_WASTE_GAS_TYPE, ['id']);
        }
        $fileFields = ['technique_pic'];
        dealFileFields($fileFields, $params);
        return $this->_wasteGasModel->up($id, $params);
    }

    /**
     * 删除危废信息
     * @param $id
     * @return bool
     * @throws WasteException
     */
    public function delWasteGas($id)
    {
        return $this->_wasteGasModel->del($id);
    }

    /**
     * 获取排气口下拉框
     * @return mixed
     */
    public function getWasteGasTubeCombo($params)
    {
        $companyId = isset($params['company_id']) && $params['company_id'] ? $params['company_id'] : getUserInfo()['company_id'];
        checkCompanyPermission($companyId);
        $where = [
            'company_id' => $companyId,
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
        checkCompanyPermission($model->company_id);
        $dateWhere = [
            'company_id' => $model->company_id,
            'tube_id' => $id,
        ];
        if ($model->type == Waste::WASTE_GAS_TUBE_TYPE) {
            $existGas = $this->_wasteGasModel->getOne($dateWhere, ['id']);
            if (!is_null($existGas)) {
                throw new WasteException(60018);
            }
        } elseif ($model->type == Waste::WASTE_WATER_TUBE_TYPE) {
            $existWater = $this->_wasteWaterModel->getOne($dateWhere, ['id']);
            if (!is_null($existWater)) {
                throw new WasteException(60019);
            }
        }
        return $this->_wasteTubeModel->del($id, false, $model);
    }

    /**
     * 获取废气信息详情
     * @param $id
     * @return mixed
     * @throws WasteException
     */
    public function getWasteGasDetail($id)
    {
        $where = [
            'id' => $id,
        ];
        $result = $this->_wasteGasModel->getOne($where);
        if (is_null($result)) {
            throw new WasteException(60011);
        }
        checkCompanyPermission($result['company_id']);
        $tubeWhere = [
            'id' => $result['tube_id'],
        ];
        //污染物信息
        $wasteInfo = Setting::checkWasteExist($result['waste_name'], Setting::SETTING_WASTE_GAS_TYPE, ['name']);
        $result['waste'] = $wasteInfo['name'];
        $result['type_name'] = Waste::WASTE_GAS_TYPE_MAP[$result['type']] ?? '';
        $gasTubeInfo = $this->_wasteTubeModel->select(['item_no'])->withTrashed()->where($tubeWhere)->first();
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
    public function getWasteGasList($params, $orderBy = ['id', 'DESC'])
    {
        $where = [
            'type' => Waste::WASTE_GAS_TUBE_TYPE,
            'built_in' => [
                'with' => 'gases',
            ]
        ];
        $userInfo = getUserInfo();
        if ($userInfo['role_type'] == Role::ROLE_COMMON_TYPE) {
            $where['company_id'] = $userInfo['company_id'];
        } elseif (isset($params['company_id']) && $params['company_id']) {
            $where['company_id'] = $params['company_id'];
        }
        $tubeFields = ['id', 'item_no', 'height', 'pics', 'check'];
        $gasFields = ['id', 'type', 'waste_name', 'gas_discharge', 'discharge_level', 'equipment', 'technique', 'installations', 'remark'];
        $result = $this->_wasteTubeModel->getList($where, $tubeFields, 0, 0, $orderBy);
        if (isset($result['rows']) && !empty($result['rows'])) {
            $wasteInfo = $wasteIds = $filesInfo = $allFileIds = [];
            foreach ($result['rows'] as &$row) {
                $row['pics_files'] = [];
                $row['check_files'] = [];
                $row['pics'] = json_decode($row['pics'], true);
                $row['check'] = json_decode($row['check'], true);
                $allFileIds = array_merge($allFileIds, $row['pics'], $row['check']);
                if (!empty($row['gases'])) {
                    $wasteIds = array_column($row['gases'], 'waste_name');
                }
            }
            if (!empty($allFileIds)) {
                $filesInfo = Files::searchFilesForList($allFileIds, 2);
            }
            if (!empty($wasteIds)) {
                $wasteInfo = Setting::searchWasteForList($wasteIds, ['name'], 'id');
            }
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
                if (!empty($item['gases'])) {
                    $newGas = [];
                    foreach ($item['gases'] as $gas) {
                        $newGasWasteName = '';
                        if (isset($wasteInfo[$gas['waste_name']])) {
                            $newGasWasteName = $wasteInfo[$gas['waste_name']]['name'];
                        }
                        if (isset($params['waste_name']) && $params['waste_name'] && strpos($newGasWasteName, $params['waste_name']) === false) {
                            continue;
                        }
                        $tmp = [
                            'type_name' => Waste::WASTE_GAS_TYPE_MAP[$gas['type']] ?? '',
                            'waste' => $newGasWasteName,
                        ];
                        foreach ($gasFields as $gasField) {
                            if (isset($gas[$gasField])) {
                                $tmp[$gasField] = $gas[$gasField];
                            }
                        }
                        $newGas[] = $tmp;
                    }
                    $item['gases'] = $newGas;
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
        Setting::checkWasteExist($params['waste_name'], Setting::SETTING_WASTE_WATER_TYPE, ['id']);
        $userInfo = getUserInfo();
        $addData = [
            'company_id' => $userInfo['company_id'],
            'tube_id' => $params['tube_id'],
            'type' => $params['type'],
            'waste_name' => $params['waste_name'],
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
        if (isset($params['type']) && !isset(Waste::WASTE_WATER_TYPE_MAP[$params['type']])) {
            throw new WasteException(60013);
        }
        if (isset($params['waste_name']) && $params['waste_name']) {
            Setting::checkWasteExist($params['waste_name'], Setting::SETTING_WASTE_WATER_TYPE, ['id']);
        }
        return $this->_wasteWaterModel->up($id, $params);
    }

    /**
     * 获取废水信息详情
     * @param $id
     * @return mixed
     * @throws WasteException
     */
    public function getWasteWaterDetail($id)
    {
        $where = [
            'id' => $id,
        ];
        $result = $this->_wasteWaterModel->getOne($where);
        if (is_null($result)) {
            throw new WasteException(60014);
        }
        checkCompanyPermission($result['company_id']);
        $tubeWhere = [
            'id' => $result['tube_id'],
        ];
        $wasteInfo = Setting::checkWasteExist($result['waste_name'], Setting::SETTING_WASTE_WATER_TYPE, ['name']);
        $result['waste'] = $wasteInfo['name'];
        $result['type_name'] = Waste::WASTE_WATER_TYPE_MAP[$result['type']] ?? '';
        $gasTubeInfo = $this->_wasteTubeModel->select(['item_no'])->withTrashed()->where($tubeWhere)->first();
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
    public function getWasteWaterList($params, $page = 0, $pageSize = 0, $orderBy = [])
    {
        $where = [
            'type' => Waste::WASTE_WATER_TUBE_TYPE,
            'built_in' => [
                'with' => 'water',
            ]
        ];
        $userInfo = getUserInfo();
        if ($userInfo['role_type'] == Role::ROLE_COMMON_TYPE) {
            $where['company_id'] = $userInfo['company_id'];
        } elseif (isset($params['company_id']) && $params['company_id']) {
            $where['company_id'] = $params['company_id'];
        }
        $tubeFields = ['id', 'item_no', 'height', 'pics', 'check'];
        $waterFields = ['id', 'type', 'waste_name', 'water_discharge', 'discharge_level', 'water_direction', 'technique', 'waste_plants', 'daily_process', 'remark'];
        $result = $this->_wasteTubeModel->getList($where, $tubeFields, $page, $pageSize, $orderBy);
        if (isset($result['rows']) && !empty($result['rows'])) {
            $wasteInfo = $filesInfo = $wasteIds = $allFileIds = [];
            foreach ($result['rows'] as &$row) {
                $row['pics_files'] = [];
                $row['check_files'] = [];
                $row['pics'] = json_decode($row['pics'], true);
                $row['check'] = json_decode($row['check'], true);
                $allFileIds = array_merge($allFileIds, $row['pics'], $row['check']);
                if (!empty($row['water'])) {
                    $wasteIds = array_column($row['water'], 'waste_name');
                }
            }
            if (!empty($allFileIds)) {
                $filesInfo = Files::searchFilesForList($allFileIds, 2);
            }
            if (!empty($wasteIds)) {
                $wasteInfo = Setting::searchWasteForList($wasteIds, ['name'], 'id');
            }
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
                if (!empty($item['water'])) {
                    $newWater = [];
                    foreach ($item['water'] as $w) {
                        $newWaterName = '';
                        if (isset($wasteInfo[$w['waste_name']])) {
                            $newWaterName = $wasteInfo[$w['waste_name']]['name'];
                        }
                        if (isset($params['waste_name']) && $params['waste_name'] && strpos($newWaterName, $params['waste_name']) === false) {
                            continue;
                        }
                        $tmp = [
                            'type_name' => Waste::WASTE_WATER_TYPE_MAP[$w['type']] ?? '',
                            'waste' => $newWaterName,
                        ];
                        foreach ($waterFields as $waterField) {
                            if (isset($w[$waterField])) {
                                $tmp[$waterField] = $w[$waterField];
                            }
                        }
                        $newWater[] = $tmp;
                    }
                    $item['water'] = $newWater;
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
        return $this->_wasteWaterModel->del($id);
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
        return $this->_noiseModel->up($id, $params);
    }

    /**
     * 获取噪音详情
     * @param $companyId
     * @param $id
     * @return mixed
     * @throws WasteException
     */
    public function getNoiseDetail($id)
    {
        $where = [
            'id' => $id,
        ];
        $result = $this->_noiseModel->getOne($where);
        if (is_null($result)) {
            throw new WasteException(60015);
        }
        checkCompanyPermission($result['company_id']);
        return $result;
    }

    /**
     * 获取噪音列表
     * @param $params
     * @return mixed
     */
    public function getNoiseList($params, $page = 1, $pageSize = 10, $orderBy = [])
    {
        $userInfo = getUserInfo();
        $where = [];
        if ($userInfo['role_type'] == Role::ROLE_COMMON_TYPE) {
            $where['company_id'] = $userInfo['company_id'];
        } elseif (isset($params['company_id']) && $params['company_id']) {
            $where['company_id'] = $params['company_id'];
        }
        if (isset($params['equipment']) && $params['equipment']) {
            $where[] = ['equipment', 'LIKE', '%' . $params['equipment'] . '%'];
        }
        $result = $this->_noiseModel->getList($where, [], $page, $pageSize, $orderBy);
        if (isset($result['rows']) && !empty($result['rows'])) {
            $companyIds = array_unique(array_column($result['rows'], 'company_id'));
            $companyInfo = Company::searchCompanyForList($companyIds);
            $checkFiles = [];
            foreach ($companyIds as $cmpId) {
                $fileParams = [
                    'company_id' => $cmpId,
                    'relation_field' => 'noise',
                ];
                $checkFiles[$cmpId] = Files::getFileByRelationField($fileParams);
            }
            foreach ($result['rows'] as &$row) {
                $row['noise_files'] = [];
                if (isset($checkFiles[$row['company_id']])) {
                    $row['noise_files'][] = $checkFiles[$row['company_id']];
                }
                $row['company_name'] = isset($companyInfo[$row['company_id']]) ? $companyInfo[$row['company_id']]['name'] : '';
            }
        }
        return $result;
    }

    /**
     * 删除噪音
     * @param $id
     * @return bool
     * @throws WasteException
     */
    public function delNoise($id)
    {
        return $this->_noiseModel->del($id);
    }

    /**
     * 添加辐射信息
     * @param $params
     * @return array
     * @throws WasteException
     */
    public function addNucleus($params)
    {
        $userInfo = getUserInfo();
        if (isset($params['staff_mobile']) && !isMobile($params['staff_mobile'])) {
            throw new WasteException(60016);
        }
        $addData = [
            'company_id' => $userInfo['company_id'],
            'equipment' => $params['equipment'],
            'num' => $params['num'] ?? 0,
            'equipment_type' => $params['equipment_type'] ?? 0,
            'radial_type' => $params['radial_type'] ?? 0,
            'spec' => $params['spec'] ?? '',
            'activity' => $params['activity'] ?? '',
            'code' => $params['code'] ?? '',
            'no' => $params['no'] ?? '',
            'maintenance_staff' => $params['maintenance_staff'] ?? '',
            'staff_mobile' => $params['staff_mobile'] ?? 0,
            'management_agency' => $params['management_agency'] ?? '',
            'remark' => $params['remark'] ?? '',
        ];
        try {
            $result = $this->_nucleusModel->add($addData);
            if (!$result) {
                throw new WasteException(60001);
            }
            return ['id' => $result];
        } catch (\Exception $e) {
            throw new WasteException(60001);
        }
    }

    /**
     * 更新辐射信息
     * @param $id
     * @param $params
     * @return array
     * @throws WasteException
     */
    public function updateNucleus($id, $params)
    {
        return $this->_nucleusModel->up($id, $params);
    }

    /**
     * 获取辐射详情
     * @param $companyId
     * @param $id
     * @return mixed
     * @throws WasteException
     */
    public function getNucleusDetail($id)
    {
        $where = [
            'id' => $id,
        ];
        $result = $this->_nucleusModel->getOne($where);
        if (is_null($result)) {
            throw new WasteException(60017);
        }
        checkCompanyPermission($result['company_id']);
        return $result;
    }

    /**
     * 获取辐射列表
     * @param $params
     * @return mixed
     */
    public function getNucleusList($params, $page = 1, $pageSize = 10, $orderBy = [])
    {
        $userInfo = getUserInfo();
        $where = [];
        if ($userInfo['role_type'] == Role::ROLE_COMMON_TYPE) {
            $where['company_id'] = $userInfo['company_id'];
        } elseif (isset($params['company_id']) && $params['company_id']) {
            $where['company_id'] = $params['company_id'];
        }
        if (isset($params['equipment']) && $params['equipment']) {
            $where[] = ['equipment', 'LIKE', '%' . $params['equipment'] . '%'];
        }
        $result = $this->_nucleusModel->getList($where, [], $page, $pageSize, $orderBy);
        if (isset($result['rows']) && !empty($result['rows'])) {
            $checkFiles = [];
            $companyIds = array_unique(array_column($result['rows'], 'company_id'));
            $companyInfo = Company::searchCompanyForList($companyIds);
            foreach ($companyIds as $cmpId) {
                $fileParams = [
                    'company_id' => $cmpId,
                    'relation_field' => 'radiation',
                ];
                $checkFiles[$cmpId] = Files::getFileByRelationField($fileParams);
            }
            foreach ($result['rows'] as &$row) {
                $row['radiation_files'] = [];
                if (isset($checkFiles[$row['company_id']])) {
                    $row['radiation_files'][] = $checkFiles[$row['company_id']];
                }
                $row['company_name'] = isset($companyInfo[$row['company_id']]) ? $companyInfo[$row['company_id']]['name'] : '';
            }
        }
        return $result;
    }

    /**
     * 删除辐射信息
     * @param $id
     * @return bool
     * @throws WasteException
     */
    public function delNucleus($id)
    {
        return $this->_nucleusModel->del($id);
    }

    /**
     * 获取废气报表
     * @param $params
     * @return array
     */
    public function getWasteGasReport($params)
    {
        $where = [];
        if (isset($params['start_time']) && $params['start_time'] > 0) {
            $where[] = ['created_at', '>=', $params['start_time']];
        }
        if (isset($params['end_time']) && $params['end_time'] > 0) {
            $where[] = ['created_at', '<=', $params['end_time']];
        }
        $fieldStr = 'SUM(installations) as installations, COUNT(DISTINCT company_id) AS company_num,waste_name';
        $result = $this->_wasteGasModel->select(DB::raw($fieldStr))
            ->where($where)
            ->groupBy('waste_name')
            ->get()->toArray();
        if (!empty($result)) {
            $wasteId = array_column($result, 'waste_name');
            $wasteInfo = Setting::searchWasteForList($wasteId, ['name'], 'id');
            foreach ($result as &$item) {
                if (isset($wasteInfo[$item['waste_name']])) {
                    $item['waste_name'] = $wasteInfo[$item['waste_name']]['name'];
                }
            }
        }
        return $result;
    }

    /**
     * 获取行业废气报表
     * @param $params
     * @return array
     */
    public function getWasteGasReportByIndustry($params)
    {
        $where = [];
        if (isset($params['start_time']) && $params['start_time'] > 0) {
            $where[] = ['protect_waste_gas.created_at', '>=', $params['start_time']];
        }
        if (isset($params['end_time']) && $params['end_time'] > 0) {
            $where[] = ['protect_waste_gas.created_at', '<=', $params['end_time']];
        }
        $fieldStr = 'SUM(protect_waste_gas.installations) as installations,protect_company.industry_category';
        $result = DB::table('waste_gas')
            ->select(DB::raw($fieldStr))
            ->where($where)
            ->join('company', 'company.id', '=', 'waste_gas.company_id')
            ->groupBy('company.industry_category')
            ->get()->toArray();
        return $result;
    }

    /**
     * 获取废水柱状图
     * @param $params
     * @return array
     */
    public function getWasteWaterReport($params)
    {
        $where = [];
        if (isset($params['start_time']) && $params['start_time'] > 0) {
            $where[] = ['created_at', '>=', $params['start_time']];
        }
        if (isset($params['end_time']) && $params['end_time'] > 0) {
            $where[] = ['created_at', '<=', $params['end_time']];
        }
        $fieldStr = 'SUM(water_discharge) as installations, COUNT(DISTINCT company_id) AS company_num,waste_name';
        $result = $this->_wasteWaterModel->select(DB::raw($fieldStr))
            ->where($where)
            ->groupBy('waste_name')
            ->get()->toArray();
        if (!empty($result)) {
            $wasteId = array_column($result, 'waste_name');
            $wasteInfo = Setting::searchWasteForList($wasteId, ['name'], 'id');
            foreach ($result as &$item) {
                if (isset($wasteInfo[$item['waste_name']])) {
                    $item['waste_name'] = $wasteInfo[$item['waste_name']]['name'];
                }
            }
        }
        return $result;
    }

    /**
     * 获取行业废水报表
     * @param $params
     * @return array
     */
    public function getWasteWaterReportByIndustry($params)
    {
        $where = [];
        if (isset($params['start_time']) && $params['start_time'] > 0) {
            $where[] = ['protect_waste_gas.created_at', '>=', $params['start_time']];
        }
        if (isset($params['end_time']) && $params['end_time'] > 0) {
            $where[] = ['protect_waste_water.created_at', '<=', $params['end_time']];
        }
        $fieldStr = 'SUM(protect_waste_water.water_discharge) as installations,protect_company.industry_category';
        $result = DB::table('waste_water')
            ->select(DB::raw($fieldStr))
            ->where($where)
            ->join('company', 'company.id', '=', 'waste_water.company_id')
            ->groupBy('company.industry_category')
            ->get()->toArray();
        return $result;
    }

    /**
     * 获取危废年产量
     * @param $params
     * @return mixed
     */
    public function getWasteMaterialReport($params)
    {
        $where = [];
        if (isset($params['start_time']) && $params['start_time'] > 0) {
            $where[] = ['protect_waste_material.created_at', '>=', $params['start_time']];
        }
        if (isset($params['end_time']) && $params['end_time'] > 0) {
            $where[] = ['protect_waste_material.created_at', '<=', $params['end_time']];
        }
        $fieldStr = 'SUM(protect_waste_material.annual_scale) as installations,protect_company.industry_category';
        $result = DB::table('waste_material')
            ->select(DB::raw($fieldStr))
            ->where($where)
            ->join('company', 'company.id', '=', 'waste_material.company_id')
            ->groupBy('company.industry_category')
            ->get()->toArray();
        return $result;
    }

    /**
     * 废气管理员列表
     * @param $params
     * @param int $page
     * @param int $pageSize
     * @param array $orderBy
     * @return mixed
     */
    public function getWasteGasAdminList($params, $page = 1, $pageSize = 10, $orderBy = [])
    {
        $userInfo = getUserInfo();
        $where = [];
        if ($userInfo['role_type'] == Role::ROLE_COMMON_TYPE) {
            $where['waste_gas.company_id'] = $userInfo['company_id'];
        } elseif (isset($params['company_id']) && $params['company_id']) {
            $where['waste_gas.company_id'] = $params['company_id'];
        }
        if (isset($params['waste']) && $params['waste']) {
            $where[] = ['waste_gas.waste_name', 'LIKE', '%' . $params['waste'] . '%'];
        }
        $fields = [];
        foreach ($this->_wasteGasModel->fillable as $field) {
            $fields[] = 'waste_gas.' . $field;
        }
        $fields = array_merge($fields, ['waste.name AS waste', 'tube.item_no AS tube_no', 'tube.pics', 'tube.check']);
        $model = DB::table('waste_gas')
            ->select($fields)
            ->where($where)
            ->whereNull('waste_gas.deleted_at');
        $total = $model->count();
        $totalPage = $pageSize > 0 ? ceil($total / $pageSize) : 0;
        $result = $model->join('tube', 'tube.id', '=', 'waste_gas.tube_id')
            ->join('waste', 'waste.id', '=', 'waste_gas.waste_name')
//            ->orderBy($orderBy)
            ->offset(($page - 1) * $pageSize)
            ->limit($pageSize)
            ->get()->toArray();
        if (!empty($result)) {
            $companyIds = $fileIds = [];
            foreach ($result as &$item) {
                $item->pics = json_decode($item->pics, true);
                $item->check = json_decode($item->check, true);
                $fileIds = array_merge($fileIds, $item->pics);
                $fileIds = array_merge($fileIds, $item->check);
                $companyIds[] = $item->company_id;
            }
            $fileIds = array_unique($fileIds);
            $companyIds = array_unique($companyIds);
            $fileInfo = Files::searchFilesForList($fileIds, 2);
            $companyInfo = Company::searchCompanyForList($companyIds);
            foreach ($result as &$gas) {
                $gas->pics_files = $gas->check_files = [];
                if (!empty($gas->pics)) {
                    foreach ($gas->pics as $pic) {
                        if (isset($fileInfo[$pic])) {
                            $gas->pics_files[] = $fileInfo[$pic];
                        }
                    }
                    foreach ($gas->check as $check) {
                        if (isset($fileInfo[$check])) {
                            $gas->check_files[] = $fileInfo[$check];
                        }
                    }
                }
                $gas->company_name = isset($companyInfo[$gas->company_id]) ? $companyInfo[$gas->company_id]['name'] : '';
                $gas->type_name = Waste::WASTE_GAS_TYPE_MAP[$gas->type] ?? '未知类型';
            }
        }
        return ['total' => $total, 'total_page' => $totalPage, 'rows' => $result];
    }

    /**
     * 废水管理员列表
     * @param $params
     * @param int $page
     * @param int $pageSize
     * @param array $orderBy
     * @return mixed
     */
    public function getWasteWaterAdminList($params, $page = 1, $pageSize = 10, $orderBy = [])
    {
        $userInfo = getUserInfo();
        $where = [];
        if ($userInfo['role_type'] == Role::ROLE_COMMON_TYPE) {
            $where['waste_water.company_id'] = $userInfo['company_id'];
        } elseif (isset($params['company_id']) && $params['company_id']) {
            $where['waste_water.company_id'] = $params['company_id'];
        }
        if (isset($params['waste']) && $params['waste']) {
            $where[] = ['waste_water.waste_name', 'LIKE', '%' . $params['waste'] . '%'];
        }
        $fields = [];
        foreach ($this->_wasteWaterModel->fillable as $field) {
            $fields[] = 'waste_water.' . $field;
        }
        $fields = array_merge($fields, ['waste.name AS waste', 'tube.item_no AS tube_no', 'tube.pics', 'tube.check']);
        $model = DB::table('waste_water')
            ->select($fields)
            ->where($where)
            ->whereNull('waste_water.deleted_at');
        $total = $model->count();
        $totalPage = $pageSize > 0 ? ceil($total / $pageSize) : 0;
        $result = $model->join('tube', 'tube.id', '=', 'waste_water.tube_id')
            ->join('waste', 'waste.id', '=', 'waste_water.waste_name')
//            ->orderBy($orderBy)
            ->offset(($page - 1) * $pageSize)
            ->limit($pageSize)
            ->get()->toArray();
        if (!empty($result)) {
            $companyIds = $fileIds = [];
            foreach ($result as &$item) {
                $item->pics = json_decode($item->pics, true);
                $item->check = json_decode($item->check, true);
                $fileIds = array_merge($fileIds, $item->pics);
                $fileIds = array_merge($fileIds, $item->check);
                $companyIds[] = $item->company_id;
            }
            $fileIds = array_unique($fileIds);
            $companyIds = array_unique($companyIds);
            $fileInfo = Files::searchFilesForList($fileIds, 2);
            $companyInfo = Company::searchCompanyForList($companyIds);
            foreach ($result as &$water) {
                $water->pics_files = $water->check_files = [];
                if (!empty($water->pics)) {
                    foreach ($water->pics as $pic) {
                        if (isset($fileInfo[$pic])) {
                            $water->pics_files[] = $fileInfo[$pic];
                        }
                    }
                    foreach ($water->check as $check) {
                        if (isset($fileInfo[$check])) {
                            $water->check_files[] = $fileInfo[$check];
                        }
                    }
                }
                $water->company_name = isset($companyInfo[$water->company_id]) ? $companyInfo[$water->company_id]['name'] : '';
                $water->type_name = Waste::WASTE_WATER_TYPE_MAP[$water->type] ?? '未知类型';
            }
        }
        return ['total' => $total, 'total_page' => $totalPage, 'rows' => $result];
    }
}
