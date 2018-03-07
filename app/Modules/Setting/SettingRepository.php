<?php

namespace App\Modules\Setting;

use App\Modules\Common\CommonRepository;

class SettingRepository extends CommonRepository
{
    private $_menuModel;

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
        $fields = ['id', 'parents_id', 'name', 'leaf', 'url', 'icon'];
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
                if ($item['parents_id'] == $parentId) {
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
