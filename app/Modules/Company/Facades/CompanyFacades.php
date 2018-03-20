<?php

namespace App\Modules\Company\Facades;

use App\Modules\Company\CompanyRepository;

class CompanyFacades
{
    private $_companyRepository;

    public function __construct(CompanyRepository $repository)
    {
        $this->_companyRepository = $repository;
    }
}
