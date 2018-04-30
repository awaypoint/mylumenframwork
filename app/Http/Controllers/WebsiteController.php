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
        $this->validate($request,[
            'logo'=>'required',
            'banners'=>'required|array',
            'longitude'=>'required',
            'latitude'=>'required',
        ]);
        $setId = env('WEBSITE_SETTING_ID', 1);
        $result = $this->_websiteRepository->upBaseSet($setId, $request->all());
        return responseTo($result, '基础设置修改成功');
    }

    /**
     * 获取基础设置详情
     * @return array
     */
    public function getBaseSetDetail()
    {
        $setId = env('WEBSITE_SETTING_ID', 1);
        $result = $this->_websiteRepository->getBaseSetDetail($setId);
        return responseTo($result);
    }

    /**
     * 添加案例
     * @param Request $request
     * @return array
     */
    public function addCase(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
            'title' => 'required',
            'img' => 'required',
            'desc' => 'required',
            'detail' => 'required',
        ]);
        $result = $this->_websiteRepository->addCase($request->all());
        return responseTo($result, '案例添加成功');
    }

    /**
     * 获取案例详情
     * @param Request $request
     * @return array
     */
    public function getCaseDetail(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_websiteRepository->getCaseDetail($request->get('id'));
        return responseTo($result);
    }

    /**
     * 更新案例
     * @param Request $request
     * @return array
     */
    public function updateCase(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'category_id' => 'required',
            'title' => 'required',
            'img' => 'required',
            'desc' => 'required',
            'detail' => 'required',
        ]);
        $result = $this->_websiteRepository->updateCase($request->get('id'), $request->all());
        return responseTo($result, '案例修改成功');
    }

    /**
     * 刪除案例
     * @param Request $request
     * @return array
     */
    public function delCase(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_websiteRepository->delCase($request->get('id'));
        return responseTo($result, '案例刪除成功');
    }

    /**
     * 获取案例列表
     * @param Request $request
     * @return array
     */
    public function getCasesList(Request $request)
    {
        list($page, $pageSize, $order) = getPageSuit($request);

        $result = $this->_websiteRepository->getCasesList($request->all(), $page, $pageSize, $order);
        return responseTo($result);
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

    /**
     * 获取专家详情
     * @param Request $request
     * @return array
     */
    public function getExertDetail(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_websiteRepository->getExpertDetail($request->get('id'));
        return responseTo($result);
    }

    /**
     * 修改专家
     * @param Request $request
     * @return array
     */
    public function updateExpert(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'category_id' => 'required',
            'expert' => 'required',
            'img' => 'required',
        ]);
        $result = $this->_websiteRepository->updateExpert($request->get('id'), $request->all());
        return responseTo($result, '专家修改成功');
    }

    /**
     * 删除专家
     * @param Request $request
     * @return array
     */
    public function delExpert(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_websiteRepository->delExpert($request->get('id'));
        return responseTo($result, '专家删除成功');
    }

    /**
     * 获取专家列表
     * @param Request $request
     * @return array
     */
    public function getExpertsList(Request $request)
    {
        list($page, $pageSize, $order) = getPageSuit($request);

        $result = $this->_websiteRepository->getExpertsList($request->all(), $page, $pageSize, $order);
        return responseTo($result);
    }

    /**
     * 添加新闻
     * @param Request $request
     * @return array
     */
    public function addNews(Request $request)
    {
        $this->validate($request, [
            'type' => 'required',
            'title' => 'required',
            'detail' => 'required',
        ]);
        $result = $this->_websiteRepository->addNews($request->all());
        return responseTo($result, '新闻添加成功');
    }

    /**
     * 获取新闻详情
     * @param Request $request
     * @return array
     */
    public function getNewsDetail(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_websiteRepository->getNewsDetail($request->get('id'));
        return responseTo($result);
    }

    /**
     * 修改新闻
     * @param Request $request
     * @return array
     */
    public function updateNews(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'type' => 'required',
            'title' => 'required',
            'detail' => 'required',
        ]);
        $result = $this->_websiteRepository->updateNews($request->get('id'), $request->all());
        return responseTo($result, '新闻修改成功');
    }

    /**
     * 删除新闻
     * @param Request $request
     * @return array
     */
    public function delNews(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_websiteRepository->delNews($request->get('id'));
        return responseTo($result, '新闻删除成功');
    }

    /**
     * 获取新闻列表
     * @param Request $request
     * @return array
     */
    public function getNewsList(Request $request)
    {
        list($page, $pageSize, $order) = getPageSuit($request);

        $result = $this->_websiteRepository->getNewsList($request->all(), $page, $pageSize, $order);
        return responseTo($result);
    }
}
