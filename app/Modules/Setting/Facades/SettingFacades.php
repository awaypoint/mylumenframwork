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
}
