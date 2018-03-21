<?php

namespace App\Modules\Files\Facades;

use App\Modules\Files\FilesRepository;

class FilesFacades
{
    private $_filesRepository;

    public function __construct(FilesRepository $repository)
    {
        $this->_filesRepository = $repository;
    }
}
