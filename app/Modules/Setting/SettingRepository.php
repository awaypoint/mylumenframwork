<?php

namespace App\Modules\Setting;

use App\Modules\Common\CommonRepository;
use App\Modules\Role\Facades\Role;
use App\Modules\Setting\Exceptions\SettingException;
use App\Modules\Setting\Facades\Setting;
use App\Modules\User\Facades\User;

class SettingRepository extends CommonRepository
{
    private $_menuModel;
    private $_wasteTypeModel;
    private $_industrialParkModel;
    private $_wasteModel;

    private $_myPermissions = [];
    private $_hideMenuIds = [];

    const SETTING_MENU_LEGAL_STATUS = 1;

    public function __construct(
        EloquentMenuModel $menuModel,
        EloquentWasteTypeModel $wasteTypeModel,
        EloquentIndustrialParkModel $industrialParkModel,
        EloquentWasteModel $wasteModel
    )
    {
        $this->_menuModel = $menuModel;
        $this->_wasteTypeModel = $wasteTypeModel;
        $this->_industrialParkModel = $industrialParkModel;
        $this->_wasteModel = $wasteModel;
    }

    /**
     * 获取菜单列表
     * @param $uid 之后可能需要控制用户权限
     * @return array
     */
    public function getMenuList()
    {
        $where = [
            'status' => self::SETTING_MENU_LEGAL_STATUS,
        ];
        $this->_myPermissions = Role::getUserPermissions();
        $this->_hideMenuIds = getUserInfo(['hide_menu_ids'])['hide_menu_ids'];

        $fields = ['id', 'parents_id', 'name', 'leaf', 'url', 'icon', 'permission'];
        $menuInfo = $this->_menuModel->searchData($where, $fields, ['listorder', 'ASC']);
        $result = $this->builtMenuItem($menuInfo);
        return $result;
    }

    /**
     * 获取危废类型下拉框列表
     * @param array $params
     * @return mixed
     */
    public function getWasteTypeCombo($params = [])
    {
        $where = [
            'parents_id' => $params['parents_id'],
        ];
        $result = $this->_wasteTypeModel->searchData($where);
        return $result;
    }

    /**
     * 用户自定义菜单
     * @param $params
     * @return mixed
     */
    public function updateUserMenu($params)
    {
        return User::updateUserMenu($params['hide_menu_ids']);
    }

    /**
     * 匹配危废类型
     * @param $wasteTypeIds
     * @param array $fields
     * @param string $indexKey
     * @param array $exceptParams
     * @return array|mixed
     */
    public function searchWasteTypeForList($wasteTypeIds, $fields = [], $indexKey = 'id', $replaceWhere = [])
    {
        $where = [
            'built_in' => [
                'whereIn' => ['id', $wasteTypeIds]
            ]
        ];
        if (!empty($replaceWhere)) {
            $where = $replaceWhere;
        }
        if (!empty($fields) && $indexKey) {
            $fields = array_unique(array_merge($fields, [$indexKey]));
        }
        $result = $this->_wasteTypeModel->searchData($where, $fields);
        if ($indexKey) {
            $result = array_column($result, null, $indexKey);
        }
        return $result;
    }

    /**
     * 递归构造菜单子项
     * @param $menuInfo
     * @param int $parentId
     * @return array
     */
    private function builtMenuItem(&$menuInfo, $parentId = 0)
    {
        $result = [];
        if (!empty($menuInfo)) {
            foreach ($menuInfo as $key => $item) {
                if ($item['parents_id'] == $parentId && !in_array($item['id'], $this->_hideMenuIds) && !in_array($item['permission'], $this->_myPermissions)) {
                    $mainMenu = [];
                    $id = $item['id'];
                    $mainMenu['id'] = $id;
                    $mainMenu['name'] = $item['name'];
                    $mainMenu['url'] = $item['url'];
                    $mainMenu['icon'] = $item['icon'];
                    $mainMenu['items'] = [];
                    unset($menuInfo[$key]);
                    if ($item['leaf']) {
                        $mainMenu['items'] = $this->builtMenuItem($menuInfo, $id);
                    }
                    $result[] = $mainMenu;
                }
            }
        }
        return $result;
    }

    /**
     * 添加工业园区
     * @param $params
     * @return array
     * @throws SettingException
     */
    public function addIndustrialPark($params)
    {
        $addData = [
            'name' => $params['name'],
            'province_code' => $params['province_code'],
            'city_code' => $params['city_code'],
            'area_code' => $params['area_code'],
        ];
        try {
            $result = $this->_industrialParkModel->add($addData);
            if (!$result) {
                throw new SettingException(30001);
            }
            return ['id' => $result];
        } catch (\Exception $e) {
            throw new SettingException(30001);
        }
    }

    /**
     * 获取工业园区列表
     * @param $params
     * @return mixed
     */
    public function getIndustrialParkCombo($params)
    {
        $where = [];
        if (isset($params['name']) && $params['name']) {
            $where[] = ['name', 'LIKE', '%' . $params['name'] . '%'];
        }
        if (isset($params['province_code']) && $params['province_code'] > 0) {
            $where[] = ['province_code', '=', $params['province_code']];
        }
        if (isset($params['city_code']) && $params['city_code'] > 0) {
            $where[] = ['city_code', '=', $params['city_code']];
        }
        if (isset($params['area_code']) && $params['area_code'] > 0) {
            $where[] = ['area_code', '=', $params['area_code']];
        }
        $fileds = ['id', 'name'];
        $result = $this->_industrialParkModel->searchData($where, $fileds);
        return $result;
    }

    /**
     * 更新工业园区
     * @param $id
     * @param $params
     * @throws SettingException
     */
    public function updateIndustrialPark($id, $params)
    {
        return $this->_industrialParkModel->up($id, $params);
    }

    /**
     * 删除工业园区
     * @param $id
     * @return bool|null
     */
    public function delIndustrialPark($id)
    {
        return $this->_industrialParkModel->del($id);
    }

    /**
     * 添加污染物
     * @param $params
     * @throws SettingException
     */
    public function addWaste($params)
    {
        if (!isset(Setting::SETTING_WASTE_TYPE_MAP[$params['type']])) {
            throw new SettingException(30003);
        }
        $addData = [
            'type' => $params['type'],
            'name' => $params['name'],
            'code' => $params['code'] ?? '',
        ];
        try {
            $result = $this->_wasteModel->add($addData);
            if (!$result) {
                throw new SettingException(30002);
            }
            return ['id' => $result];
        } catch (\Exception $e) {
            throw new SettingException(30002);
        }
    }

    /**
     * 更新污染物
     * @param $id
     * @param $params
     * @return array
     */
    public function updateWaste($id, $params)
    {
        return $this->_wasteModel->up($id, $params);
    }

    /**
     * 删除污染物
     * @param $id
     * @return bool|null
     */
    public function delWaste($id)
    {
        return $this->_wasteModel->del($id);
    }

    /**
     * 获取污染物下拉框
     * @param $params
     * @return mixed
     */
    public function getWasteCombo($params)
    {
        $where = [
            'type' => $params['type'],
        ];
        if (isset($params['name']) && $params['name']) {
            $where[] = ['name', 'LIKE', '%' . $params['name'] . '%'];
        }
        return $this->_wasteModel->searchData($where);
    }

    /**
     * 通过条件获取污染物名称
     * @param $id
     * @param array $fields
     * @return mixed
     */
    public function checkWasteExist($id, $type, $fields = [])
    {
        $where = [
            'id' => $id,
            'type' => $type,
        ];
        $result = $this->_wasteModel->getOne($where, $fields);
        if (is_null($result)) {
            throw new SettingException(30004);
        }
        return $result;
    }

    /**
     * 污染物列表匹配
     * @param $wasteIds
     * @param array $fields
     * @param string $indexKey
     * @param array $replaceWhere
     * @return array|mixed
     */
    public function searchWasteForList($wasteIds, $fields = [], $indexKey = '', $replaceWhere = [])
    {
        $where = [
            'built_in' => [
                'whereIn' => ['id', $wasteIds],
            ]
        ];
        if (!empty($replaceWhere)) {
            $where = $replaceWhere;
        }
        if (!empty($fields) && $indexKey) {
            $fields = array_unique(array_merge($fields, [$indexKey]));
        }
        $result = $this->_wasteModel->searchData($where, $fields);
        if ($indexKey) {
            $result = array_column($result, null, $indexKey);
        }
        return $result;
    }
}
