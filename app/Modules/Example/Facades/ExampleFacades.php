<?php

namespace App\Modules\Example\Facades;

use App\Modules\Example\ExampleRepository;

class ExampleFacades
{
    private $_exampleRepository;

    public function __construct(ExampleRepository $exampleRepository)
    {
        $this->_exampleRepository = $exampleRepository;
    }
}
