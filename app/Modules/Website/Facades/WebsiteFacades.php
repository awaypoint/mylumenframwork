<?php

namespace App\Modules\Website\Facades;

use App\Modules\Website\WebsiteRepository;

class WebsiteFacades
{
    private $_websiteRepository;

    public function __construct(WebsiteRepository $repository)
    {
        $this->_websiteRepository = $repository;
    }
}
