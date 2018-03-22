<?php

namespace App\Http\Controllers;

use App\Modules\Role\EloquentRolePermissionsModel;
use App\Modules\Role\EloquentRoleRelationModel;
use App\Modules\Setting\SettingRepository;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    private $_settingRepository;

    public function __construct(
        SettingRepository $settingRepository
    )
    {
        parent::__construct();
        $this->_settingRepository = $settingRepository;
    }

    /**
     * 获取菜单列表
     * @return array
     */
    public function getMenu()
    {
        $result = $this->_settingRepository->getMenuList();
        return responseTo($result);
    }

    /**
     * 获取危废类型下拉列表
     * @param Request $request
     * @return array
     */
    public function getWasteTypeCombo(Request $request)
    {
        $this->validate($request, [
            'parents_id' => 'required|numeric'
        ]);
        $result = $this->_settingRepository->getWasteTypeCombo($request->all());
        return responseTo($result);
    }

    /**
     * 用户自定义菜单
     * @param Request $request
     * @return array
     */
    public function updateUserMenu(Request $request)
    {
        $this->validate($request, [
            'hide_menu_ids' => 'array'
        ]);
        if (!$request->has('hide_menu_ids')) {
            return responseTo(false, '参数缺失');
        }
        $result = $this->_settingRepository->updateUserMenu($request->all());
        return responseTo($result, '用户菜单更新成功');
    }

    /**
     * 禁用管理员权限
     */
    public function forbittenAdmin()
    {
        $permissionModel = new EloquentRolePermissionsModel();
        $relationModel = new EloquentRoleRelationModel();
        $allPermissions = $permissionModel->searchData([], ['permission', 'relation_permission']);
        $addData = [];
        $nowTime = time();
        foreach ($allPermissions as $item) {
            $tmp = [
                'role_id' => 1,
                'permission' => $item['permission'],
                'relation_permission' => $item['relation_permission'],
                'created_at' => $nowTime,
                'updated_at' => $nowTime,
            ];
            $addData[] = $tmp;
        }
        $result = $relationModel->addBatch($addData);
        return responseTo($result);
    }
}
