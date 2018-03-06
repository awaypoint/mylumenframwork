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

    public function getMenuList($uid)
    {
        $where = [
            'status' => self::SETTING_MENU_LEGAL_STATUS,
        ];
        $result = [];
        $fields = ['id', 'parents_id', 'name', 'url', 'icon'];
        $menuInfo = $this->_menuModel->searchData($where, $fields, ['listorder', 'DESC']);
        if (!empty($menuInfo)) {
            foreach ($menuInfo as $menuItem) {
                $uniqueId = $menuItem['parents_id'];
                $mainMenu = [];
                if ($menuItem['parents_id'] == 0) {
                    $uniqueId = $menuItem['id'];
                    $mainMenu['name'] = $menuItem['name'];
                    $mainMenu['url'] = $menuItem['url'];
                    $mainMenu['icon'] = $menuItem['icon'];
                }
                if (!isset($result[$uniqueId])) {
                    $result[$uniqueId] = [];
                    $result[$uniqueId]['items'] = [];
                }
                unset($menuItem['id'], $menuItem['parents_id']);
                if (!empty($mainMenu)) {
                    $result[$uniqueId] = array_merge($result[$uniqueId], $mainMenu);
                } else {
                    $result[$uniqueId]['items'][] = $menuItem;
                }
            }
        }
        return array_values($result);
    }
}
