<?php

namespace App\Http\Controllers;

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
        $result = $this->_settingRepository->getMenuList($this->uid);
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
}
