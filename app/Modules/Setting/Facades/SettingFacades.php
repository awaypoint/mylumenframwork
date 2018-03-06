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
}
