<?php

namespace App\Modules\Setting;

use App\Modules\Common\CommonRepository;
use App\Modules\Role\Facades\Role;
use Illuminate\Support\Facades\Session;

class SettingRepository extends CommonRepository
{
    private $_menuModel;
    private $_myPermissions = [];

    const SETTING_MENU_LEGAL_STATUS = 1;

    public function __construct(
        EloquentMenuModel $menuModel
    )
    {
        $this->_menuModel = $menuModel;
    }

    /**
     * 获取菜单列表
     * @param $uid 之后可能需要控制用户权限
     * @return array
     */
    public function getMenuList($uid)
    {
        $where = [
            'status' => self::SETTING_MENU_LEGAL_STATUS,
        ];
        $this->_myPermissions = Role::getUserPermissions($uid);

        $fields = ['id', 'parents_id', 'name', 'leaf', 'url', 'icon', 'permission'];
        $menuInfo = $this->_menuModel->searchData($where, $fields, ['listorder', 'ASC']);
        $result = $this->builtMenuItem($menuInfo);
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
                if ($item['parents_id'] == $parentId && in_array($item['permission'], $this->_myPermissions)) {
                    $mainMenu = [];
                    $id = $item['id'];
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
