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

    public function getMenu()
    {
        $result = $this->_settingRepository->getMenuList($this->uid);
        return responseTo($result);
    }
}
