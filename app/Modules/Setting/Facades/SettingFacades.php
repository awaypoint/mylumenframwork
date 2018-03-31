<?php

namespace App\Modules\Setting\Facades;

use App\Modules\Setting\SettingRepository;

class SettingFacades
{
    private $_settingRepository;

    public function __construct(SettingRepository $settingRepository)
    {
        $this->_settingRepository = $settingRepository;
    }

    /**
     * 匹配危废类型
     * @param $wasteTypeIds
     * @param array $fields
     * @param string $indexKey
     * @param array $replaceWhere
     * @return array|mixed
     */
    public function searchWasteTypeForList($wasteTypeIds, $fields = [], $indexKey = '', $replaceWhere = [])
    {
        return $this->_settingRepository->searchWasteTypeForList($wasteTypeIds, $fields, $indexKey, $replaceWhere);
    }

    /**
     * 通过条件获取污染物名称
     * @param $id
     * @return mixed
     */
    public function checkWasteExist($id, $type, $fields = [])
    {
        return $this->_settingRepository->checkWasteExist($id, $type, $fields);
    }

    /**
     * 获取污染物名称列表匹配
     * @param $wasteIds
     * @param array $fields
     * @param string $indexKey
     * @param array $replaceWhere
     * @return mixed
     */
    public function searchWasteForList($wasteIds, $fields = [], $indexKey = '', $replaceWhere = [])
    {
        return $this->_settingRepository->searchWasteForList($wasteIds, $fields, $indexKey, $replaceWhere);
    }
}
