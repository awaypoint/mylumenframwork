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
            'waste_category' => 'required|numeric',
            'industry' => 'required|numeric',
            'waste_code' => 'required|numeric',
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
        $page = $request->get('page') ?? 0;
        $pageSigze = $request->get('page_size') ?? 0;
        $orderBy = $request->get('order_by') ?? 'id';
        $sortBy = $request->get('sort_by') ?? 'DESC';

        $params = $request->all();
        $params['company_id'] = getUserInfo()['company_id'];
        $order = [$orderBy, $sortBy];
        $result = $this->_wasteRepository->getWasteMaterialList($params, $page, $pageSigze, $order);
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
            'id' => 'required|numeric',
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
            'id' => 'required|numeric',
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
            'id' => 'required|numeric',
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
            'id' => 'required|numeric',
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
            'id' => 'required|numeric',
        ]);
        $result = $this->_wasteRepository->getWasteGasTubeDetail($request->get('id'));
        return responseTo($result);
    }

    /**
     * 获取排气管道下拉框
     * @return array
     */
    public function getWasteGasTubeCombo()
    {
        $result = $this->_wasteRepository->getWasteGasTubeCombo();
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
            'id' => 'required|numeric',
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
            'tube_id' => 'required|numeric',
            'type' => 'required|numeric',
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
            'id' => 'required|numeric',
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
            'id' => 'required|numeric',
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
            'id' => 'required|numeric',
        ]);
        $result = $this->_wasteRepository->getWasteGasDetail(getUserInfo()['company_id'], $request->get('id'));
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
}
