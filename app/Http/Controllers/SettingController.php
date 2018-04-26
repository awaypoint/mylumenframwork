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

    /**
     * 添加工业园区
     * @param Request $request
     * @return array
     */
    public function addIndustrialPark(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'province_code' => 'required',
            'province' => 'required',
            'city_code' => 'required',
            'city' => 'required',
            'area_code' => 'required',
            'area' => 'required',
        ]);
        $result = $this->_settingRepository->addIndustrialPark($request->all());
        return responseTo($result, '添加工业园区成功');
    }

    /**
     * 获取工业园区列表
     * @param Request $request
     * @return array
     */
    public function getIndustrialParkCombo(Request $request)
    {
        $result = $this->_settingRepository->getIndustrialParkCombo($request->all());
        return responseTo($result);
    }

    /**
     * 获取工业园区列表
     * @param Request $request
     * @return array
     */
    public function getIndustrialParkList(Request $request)
    {
        list($page, $pageSize, $order) = getPageSuit($request);

        $result = $this->_settingRepository->getIndustrialParkList($request->all(), $page, $pageSize, $order);
        return responseTo($result);
    }

    /**
     * 更新工业园区
     * @param Request $request
     * @return array
     */
    public function updateIndustrialPark(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'name' => 'required',
            'province_code' => 'required',
            'city_code' => 'required',
            'area_code' => 'required',
        ]);
        $result = $this->_settingRepository->updateIndustrialPark($request->get('id'), $request->all());
        return responseTo($result, '编辑工业园区成功');
    }

    /**
     * 删除工业园区
     * @param Request $request
     * @return array
     */
    public function delIndustrialPark(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_settingRepository->delIndustrialPark($request->get('id'));
        return responseTo($result, '删除工业园区成功');
    }

    /**
     * 添加污染物
     * @param Request $request
     * @return array
     */
    public function addWaste(Request $request)
    {
        $this->validate($request, [
            'type' => 'required',
            'name' => 'required',
        ]);
        $result = $this->_settingRepository->addWaste($request->all());
        return responseTo($result, '添加污染物成功');
    }

    /**
     * 更新污染物
     * @param Request $request
     * @return array
     */
    public function updateWaste(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'type' => 'required',
            'name' => 'required',
        ]);
        $result = $this->_settingRepository->updateWaste($request->get('id'), $request->all());
        return responseTo($result, '修改污染物成功');
    }

    /**
     * 删除污染物
     * @param Request $request
     * @return array
     */
    public function delWaste(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_settingRepository->delWaste($request->get('id'));
        return responseTo($result);
    }

    /**
     * 获取污染物下拉框
     * @param Request $request
     * @return array
     */
    public function getWasteCombo(Request $request)
    {
        $this->validate($request, [
            'type' => 'required'
        ]);
        $result = $this->_settingRepository->getWasteCombo($request->all());
        return responseTo($result);
    }

    /**
     * 设置用户地区权限
     * @param Request $request
     * @return array
     */
    public function setUserCityPermissions(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'permissions' => 'required',
        ]);
        $result = $this->_settingRepository->setUserCityPermissions($request->get('id'), $request->get('permissions'));
        return responseTo($result, '权限设置成功');
    }

    /**
     * 获取用户地区权限
     * @param Request $request
     * @return array
     */
    public function getUserCityPermissions(Request $request)
    {
        $this->validate($request,[
            'admin_uid'=>'required',
        ]);
        $result = $this->_settingRepository->getUserCityPermissions($request->get('admin_uid'));
        return responseTo($result);
    }
}
