<?php

namespace App\Http\Controllers;

use App\Modules\Files\Facades\Files;
use App\Modules\Files\FilesRepository;
use function GuzzleHttp\Promise\all;
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
     * @throws \App\Modules\Files\Exceptions\FilesException
     */
    public function upLoadFile(Request $request)
    {
        $files = $request->allFiles();
        $result = new \stdClass();
        foreach ($files as $key => $file) {
            $result = $this->_filesgRepository->upLoadFile2Local($key, $file, $request->all());
        }
        return responseTo($result);
    }

    /**
     * 多图上次，用于富文本
     * @param Request $request
     * @return array
     * @throws \App\Modules\Files\Exceptions\FilesException
     */
    public function multUploadFiles(Request $request)
    {
        $files = $request->allFiles();
        $result = [];
        foreach ($files as $key => $file) {
            $upRes = $this->_filesgRepository->upLoadFile2Local($key, $file, $request->all());
            $result[] = $upRes['url'];
        }
        return response2($result);
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

    /**
     * 通过关联字段获取文件
     * @param Request $request
     * @return array
     */
    public function getFileByRelationField(Request $request)
    {
        $this->validate($request, [
            'relation_field' => 'required',
        ]);
        $result = $this->_filesgRepository->getFileByRelationField($request->all());
        return responseTo($result);
    }

    public function testExcel(Request $request)
    {
        $this->_filesgRepository->excel2Html($request->file('text'));
    }
}