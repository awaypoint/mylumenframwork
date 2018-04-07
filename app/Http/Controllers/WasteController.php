<?php

namespace App\Http\Controllers;

use App\Modules\Waste\WasteRepository;
use Illuminate\Http\Request;

class WasteController extends Controller
{
    private $_wasteRepository;

    public function __construct(
        WasteRepository $wasteRepository
    )
    {
        parent::__construct();
        $this->_wasteRepository = $wasteRepository;
    }

    /**
     * 添加危废信息
     * @param Request $request
     * @return array
     */
    public function addWasteMaterial(Request $request)
    {
        $this->validate($request, [
            'waste_category' => 'required',
            'industry' => 'required',
            'waste_code' => 'required',
            'waste_name' => 'required',
        ]);
        $result = $this->_wasteRepository->addWasteMaterial($request->all());
        return responseTo($result, '添加危险废物信息成功');
    }

    /**
     * 获取危废信息列表
     * @param Request $request
     * @return array
     */
    public function getWasteMaterialList(Request $request)
    {
        list($page, $pageSigze, $order) = getPageSuit($request);

        $result = $this->_wasteRepository->getWasteMaterialList($request->all(), $page, $pageSigze, $order);
        return responseTo($result);
    }

    /**
     * 获取危废信息详情
     * @param Request $request
     * @return array
     */
    public function getWasteMaterialDetail(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_wasteRepository->getWasteMaterialDetail($request->get('id'));
        return responseTo($result);
    }

    /**
     * 更新危废信息
     * @param Request $request
     * @return array
     */
    public function updateWasteMaterial(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'waste_category' => 'required',
            'industry' => 'required',
            'waste_code' => 'required',
            'waste_name' => 'required',
        ]);
        $result = $this->_wasteRepository->updateWasteMaterial($request->get('id'), $request->all());
        return responseTo($result, '修改危废信息成功');
    }

    /**
     * 删除危废信息
     * @param Request $request
     * @return array
     */
    public function delWasteMaterial(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_wasteRepository->delWasteMaterial($request->get('id'));
        return responseTo($result, '删除危废信息成功');
    }

    /**
     * 添加排放口
     * @param Request $request
     * @return array
     */
    public function addWasteGasTube(Request $request)
    {
        $this->validate($request, [
            'item_no' => 'required',
            'type' => 'required',
        ]);
        $result = $this->_wasteRepository->addWasteGasTube($request->all());
        return responseTo($result, '添加排放口成功');
    }

    /**
     * 修改排放口
     * @param Request $request
     * @return array
     */
    public function updateWasteGasTube(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'item_no' => 'required',
        ]);
        $result = $this->_wasteRepository->updateWasteGasTube($request->get('id'), $request->all());
        return responseTo($result, '排放口修改成功');
    }

    /**
     * 获取排气口详情
     * @param Request $request
     * @return array
     */
    public function getWasteGasTubeDetail(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_wasteRepository->getWasteGasTubeDetail($request->get('id'));
        return responseTo($result);
    }

    /**
     * 获取排气管道下拉框
     * @return array
     */
    public function getWasteGasTubeCombo(Request $request)
    {
        $this->validate($request, [
            'type' => 'required',
        ]);
        $result = $this->_wasteRepository->getWasteGasTubeCombo($request->all());
        return responseTo($result);
    }

    /**
     * 删除排放口
     * @param Request $request
     * @return array
     */
    public function delWasteGasTube(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_wasteRepository->delWasteGasTube($request->get('id'));
        return responseTo($result, '删除排放口成功');
    }

    /**
     * 添加废气信息
     * @param Request $request
     * @return array
     */
    public function addWasteGas(Request $request)
    {
        $this->validate($request, [
            'tube_id' => 'required',
            'type' => 'required',
            'waste_name' => 'required',
        ]);
        $result = $this->_wasteRepository->addWasteGas($request->all());
        return responseTo($result, '添加废气信息成功');
    }

    /**
     * 修改废气信息
     * @param Request $request
     * @return array
     */
    public function updateWasteGas(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'tube_id' => 'required',
            'type' => 'required',
            'waste_name' => 'required',
        ]);
        $result = $this->_wasteRepository->updateWasteGas($request->get('id'), $request->all());
        return responseTo($result, '更新废气信息成功');
    }

    /**
     * 删除废气信息
     * @param Request $request
     * @return array
     */
    public function delWasteGas(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_wasteRepository->delWasteGas($request->get('id'));
        return responseTo($result, '废气信息删除成功');
    }

    /**
     * 获取废气信息详情
     * @param Request $request
     * @return array
     */
    public function getWasteGasDetail(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_wasteRepository->getWasteGasDetail($request->get('id'));
        return responseTo($result);
    }

    /**
     * 获取废气信息列表
     * @param Request $request
     * @return array
     */
    public function getWasteGasList(Request $request)
    {
        $result = $this->_wasteRepository->getWasteGasList($request->all());
        return responseTo($result);
    }

    /**
     *
     * @param Request $request
     * @return array
     */
    public function getWasteGasAdminList(Request $request)
    {
        list($page, $pageSize, $order) = getPageSuit($request);
        $result = $this->_wasteRepository->getWasteGasAdminList($request->all(), $page, $pageSize, $order);
        return responseTo($result);
    }

    /**
     * 添加废水信息
     * @param Request $request
     * @return array
     */
    public function addWasteWater(Request $request)
    {
        $this->validate($request, [
            'tube_id' => 'required',
            'type' => 'required',
            'waste_name' => 'required',
        ]);
        $result = $this->_wasteRepository->addWasterWater($request->all());
        return responseTo($result, '添加废水信息成功');
    }

    /**
     * 修改废气信息
     * @param Request $request
     * @return array
     */
    public function updateWasteWater(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_wasteRepository->updateWasteWater($request->get('id'), $request->all());
        return responseTo($result, '更新废水信息成功');
    }

    /**
     * 获取废水信息详情
     * @param Request $request
     * @return array
     */
    public function getWasteWaterDetail(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_wasteRepository->getWasteWaterDetail(getUserInfo()['company_id'], $request->get('id'));
        return responseTo($result);
    }

    /**
     * 获取废水信息列表
     * @param Request $request
     * @return array
     */
    public function getWasteWaterList(Request $request)
    {
        list($page, $pageSigze, $order) = getPageSuit($request);

        $result = $this->_wasteRepository->getWasteWaterList($request->all(), $page, $pageSigze, $order);
        return responseTo($result);
    }

    /**
     * 删除废水信息
     * @param Request $request
     * @return array
     */
    public function delWasteWater(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_wasteRepository->delWasteWater($request->get('id'));
        return responseTo($result, '废水信息删除成功');
    }

    /**
     * 添加噪音
     * @param Request $request
     * @return array
     */
    public function addNoise(Request $request)
    {
        $this->validate($request, [
            'equipment' => 'required',
        ]);
        $result = $this->_wasteRepository->addNoise($request->all());
        return responseTo($result, '添加噪音成功');
    }

    /**
     * 修改噪音
     * @param Request $request
     * @return array
     */
    public function updateNoise(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'equipment' => 'required',
        ]);
        $result = $this->_wasteRepository->updateNoise($request->get('id'), $request->all());
        return responseTo($result, '修改噪音成功');
    }

    /**
     * 获取噪音详情
     * @param Request $request
     * @return array
     */
    public function getNoiseDetail(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_wasteRepository->getNoiseDetail(getUserInfo()['company_id'], $request->get('id'));
        return responseTo($result);
    }

    /**
     * 获取噪音信息列表
     * @param Request $request
     * @return array
     */
    public function getNoiseList(Request $request)
    {
        list($page, $pageSigze, $order) = getPageSuit($request);

        $result = $this->_wasteRepository->getNoiseList($request->all(), $page, $pageSigze, $order);
        return responseTo($result);
    }

    /**
     * 删除噪音信息
     * @param Request $request
     * @return array
     */
    public function delNoise(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_wasteRepository->delNoise($request->get('id'));
        return responseTo($result, '噪音信息删除成功');
    }

    /**
     * 添加辐射
     * @param Request
     * @return array
     */
    public function addNucleus(Request $request)
    {
        $this->validate($request, [
            'equipment' => 'required',
        ]);
        $result = $this->_wasteRepository->addNucleus($request->all());
        return responseTo($result, '添加辐射成功');
    }

    /**
     * 修改噪音
     * @param Request $request
     * @return array
     */
    public function updateNucleus(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'equipment' => 'required',
        ]);
        $result = $this->_wasteRepository->updateNucleus($request->get('id'), $request->all());
        return responseTo($result, '修改辐射成功');
    }

    /**
     * 获取噪音详情
     * @param Request $request
     * @return array
     */
    public function getNucleusDetail(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_wasteRepository->getNucleusDetail(getUserInfo()['company_id'], $request->get('id'));
        return responseTo($result);
    }

    /**
     * 获取辐射信息列表
     * @param Request $request
     * @return array
     */
    public function getNucleusList(Request $request)
    {
        list($page, $pageSigze, $order) = getPageSuit($request);

        $result = $this->_wasteRepository->getNucleusList($request->all(), $page, $pageSigze, $order);
        return responseTo($result);
    }

    /**
     * 删除噪音信息
     * @param Request $request
     * @return array
     */
    public function delNucleus(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_wasteRepository->delNucleus($request->get('id'));
        return responseTo($result, '辐射信息删除成功');
    }

    /**
     * 获取废气柱状图
     * @param Request $request
     * @return array
     */
    public function getWasteGasReport(Request $request)
    {
        $result = $this->_wasteRepository->getWasteGasReport($request->all());
        return responseTo($result);
    }

    /**
     * 获取废水柱状图
     * @param Request $request
     * @return array
     */
    public function getWasteWaterReport(Request $request)
    {
        $result = $this->_wasteRepository->getWasteWaterReport($request->all());
        return responseTo($result);
    }

    /**
     * 获取行业废气排放量
     * @param Request $request
     * @return array
     */
    public function getWasteGasReportByIndustry(Request $request)
    {
        $result = $this->_wasteRepository->getWasteGasReportByIndustry($request->all());
        return responseTo($result);
    }

    /**
     * 获取行业废水排放量
     * @param Request $request
     * @return array
     */
    public function getWasteWaterReportByIndustry(Request $request)
    {
        $result = $this->_wasteRepository->getWasteWaterReportByIndustry($request->all());
        return responseTo($result);
    }

    /**
     * 获取危废信息排放量
     * @param Request $request
     * @return array
     */
    public function getWasteMaterialReport(Request $request)
    {
        $result = $this->_wasteRepository->getWasteMaterialReport($request->all());
        return responseTo($result);
    }
}
