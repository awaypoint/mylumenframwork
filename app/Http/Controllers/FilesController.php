<?php

namespace App\Http\Controllers;

use App\Modules\Files\FilesRepository;
use Illuminate\Http\Request;

class FilesController extends Controller
{
    private $_filesgRepository;

    public function __construct(
        FilesRepository $filesRepository
    )
    {
        parent::__construct();
        $this->_filesgRepository = $filesRepository;
    }

    /**
     * 上传文件
     * @param Request $request
     * @return array
     */
    public function upLoadFile(Request $request)
    {
        $files = $request->allFiles();
        $result = new \stdClass();
        foreach ($files as $key => $file) {
            $result = $this->_filesgRepository->upLoadFile($key, $file);
        }
        return responseTo($result);
    }

    /**
     * 更新文件额外字段
     * @param Request $request
     * @return array
     */
    public function updateFileExtraFields(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric',
            'extra_fields' => 'required',
        ]);
        $result = $this->_filesgRepository->updateFileExtraFields($request->get('id'), $request->all());
        return responseTo($result);
    }
}
