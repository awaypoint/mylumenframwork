<?php

namespace App\Http\Controllers;

use App\Modules\Role\RoleRepository;
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
}
