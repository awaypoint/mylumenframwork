<?php

namespace App\Http\Controllers;

use App\Modules\Files\Facades\Files;
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
            'relation_field' => 'required',
        ]);
        $result = $this->_filesgRepository->updateFileExtraFields($request->get('id'), $request->all());
        return responseTo($result);
    }

    /**
     * 获取企业信息下文件管理
     * @param Request $request
     * @return array
     */
    public function getCompanyFiles(Request $request)
    {
        $params = $request->all();
        $params['module_type'] = Files::FILES_COMPANY_MODULE_TYPE;
        $result = $this->_filesgRepository->getCompanyFiles($params);
        return responseTo($result);
    }

    /**
     * 删除文件
     * @param Request $request
     * @return array
     */
    public function delFile(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);
        $result = $this->_filesgRepository->delFile($request->get('id'));
        return responseTo($result, '删除文件成功');
    }
}
