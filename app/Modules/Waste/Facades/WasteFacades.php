<?php

namespace App\Modules\Waste\Facades;

use App\Modules\Waste\WasteRepository;

class WasteFacades
{
    private $_wasteRepository;

    public function __construct(WasteRepository $repository)
    {
        $this->_wasteRepository = $repository;
    }
}
