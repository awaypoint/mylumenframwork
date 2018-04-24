<?php

namespace App\Http\Controllers;

use App\Modules\Website\WebsiteRepository;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class WebsiteController extends BaseController
{
    private $_websiteRepository;

    public function __construct(
        WebsiteRepository $websiteRepository
    )
    {
        $this->_websiteRepository = $websiteRepository;
    }

    /**
     * 修改基础设置
     * @param Request $request
     * @return array
     */
    public function upBaseSet(Request $request)
    {
        $setId = env('SETTING_ID', 1);
        $result = $this->_websiteRepository->upBaseSet($setId, $request->all());
        return responseTo($result, '基础设置修改成功');
    }

    /**
     * 添加案例
     * @param Request $request
     * @return array
     */
    public function addCases(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
            'title' => 'required',
            'img' => 'required',
            'desc' => 'required',
            'detail' => 'required',
        ]);
        $result = $this->_websiteRepository->addCases($request->all());
        return responseTo($result, '案例添加成功');
    }

    /**
     * 添加专家
     * @param Request $request
     * @return array
     */
    public function addExpert(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
            'expert' => 'required',
            'img' => 'required',
        ]);
        $result = $this->_websiteRepository->addExpert($request->all());
        return responseTo($result, '专家添加成功');
    }
}
