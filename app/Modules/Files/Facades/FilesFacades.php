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

    /**
     * 匹配文件信息
     * @param $fileIds
     * @return mixed
     */
    public function searchFilesForList($fileIds, $type = 1)
    {
        return $this->_filesRepository->searchFilesForList($fileIds, $type);
    }

    /**
     * 通过关联字段获取文件信息
     * @param $params
     * @return mixed
     */
    public function getFileByRelationField($params)
    {
        return $this->_filesRepository->getFileByRelationField($params);
    }
}
