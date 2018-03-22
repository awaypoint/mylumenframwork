<?php

namespace App\Modules\Setting;

use App\Modules\Common\CommonRepository;
use App\Modules\Role\Facades\Role;
use App\Modules\User\Facades\User;

class SettingRepository extends CommonRepository
{
    private $_menuModel;
    private $_wasteTypeModel;
    private $_myPermissions = [];
    private $_hideMenuIds = [];

    const SETTING_MENU_LEGAL_STATUS = 1;

    public function __construct(
        EloquentMenuModel $menuModel,
        EloquentWasteTypeModel $wasteTypeModel
    )
    {
        $this->_menuModel = $menuModel;
        $this->_wasteTypeModel = $wasteTypeModel;
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
}
