<?php

namespace App\Modules\Files;

use App\Modules\Common\CommonRepository;
use App\Modules\Files\Exceptions\FilesException;
use App\Modules\Files\Facades\Files;
use JohnLui\AliyunOSS;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use Upload\File;
use Upload\Storage\FileSystem;

class FilesRepository extends CommonRepository
{
    /* 城市名称：
     *
     *  经典网络下可选：杭州、上海、青岛、北京、张家口、深圳、香港、硅谷、弗吉尼亚、新加坡、悉尼、日本、法兰克福、迪拜
     *  VPC 网络下可选：杭州、上海、青岛、北京、张家口、深圳、硅谷、弗吉尼亚、新加坡、悉尼、日本、法兰克福、迪拜
     */
    // 经典网络 or VPC
    private $networkType = '经典网络';
    private $ossClient;
    private $bucketName = 'ghhbgj';
    private $_file;
    private $_fileModel;
    private $_originalName;
    private $_originalExtension;
    private $_mimeType;
    private $_pathname;
    private $_fileUp = null;//上传本地新model

    const FILES_COMPANY_RELATION_FIELDS = [
        'eia' => '环评资料',
        'waste' => '固废管理',
        'emergency' => '应急预案',
        'check' => '验收文件',
        'sewage' => '排污许可证',
        'clean' => '清洁生产',
        'nucleus' => '核与辐射',
    ];

    public function __construct(
        EloquentFilesModel $filesModel,
        $isInternal = false
    )
    {
        $this->_fileModel = $filesModel;

       /* if ($this->networkType == 'VPC' && !$isInternal) {
            throw new Exception("VPC 网络下不提供外网上传、下载等功能");
        }

        $this->ossClient = AliyunOSS::boot(
            env('OSS_CITY'),
            $this->networkType,
            $isInternal,
            env('OSS_ACCESSKEY_ID'),
            env('OSS_ACCESSKEY_SECRET')
        );
        $this->ossClient->setBucket($this->bucketName);*/
    }

    private function setFile($file)
    {
        if ($file->getSize() <= 0 || $file->getSize() > 5242880) {
            throw new FilesException(50006);
        }
        $this->_file = $file;
        $this->_originalName = $file->getClientOriginalName();
        $this->_originalExtension = $file->getClientOriginalExtension();
        $this->_mimeType = $file->getMimeType();
        $this->_pathname = $file->getPathname();
    }

    /**
     * 上传文件
     * @param $relationField
     * @param $file
     * @return array
     * @throws FilesException
     */
    public function upLoadFile($relationField, $file, $params = [])
    {
        $this->setFile($file);
        $userInfo = getUserInfo(['company_id']);
        $prefix = env('OSS_ENVIRONMENT', '') . DIRECTORY_SEPARATOR . $userInfo['company_id'] . DIRECTORY_SEPARATOR;
        $ossKey = md5($this->_originalName . time()) . '.' . $this->_originalExtension;
        $options = [
            'ContentType' => $this->_mimeType,
            'ContentDisposition' => 'filename=' . $this->_originalName,
        ];
        $result = $this->ossClient->uploadFile($prefix . $ossKey, $this->_pathname, $options);
        if ($result === false) {
            throw new FilesException(50001);
        }
        $url = $this->getPublicObjectURL($prefix . $ossKey);
        $previewUrl = $url;
        $companyId = isset($params['company_id']) && $params['company_id'] ? $params['company_id'] : $userInfo['company_id'];
        //checkCompanyPermission($companyId);
        $fileLogId = $this->addFilesLog($companyId, $relationField, $ossKey, $url, $previewUrl);
        $returnData = [
            'id' => $fileLogId,
            'file_name' => $this->_originalName,
            'url' => $url,
            'preview_url' => $previewUrl,
        ];
        return $returnData;
    }

    /**
     * 添加上传日志
     * @param $relationField
     * @param $ossKey
     * @param $url
     * @throws FilesException
     */
    public function addFilesLog($companyId, $relationField, $ossKey, $url, $previewUrl = '')
    {
        $addData = [
            'company_id' => $companyId,
            'relation_field' => $relationField,
            'file_name' => $this->_originalName,
            'url' => $url,
            'preview_url' => $previewUrl,
            'oss_key' => $ossKey,
            'extra_fields' => '',
            'module_type' => 0,
        ];
        $result = $this->_fileModel->add($addData);
        if (!$result) {
            throw new FilesException(50001);
        }
        return $result;
    }

    /**
     * 获取公开文件的 URL
     * @param $ossKey
     * @return string
     */
    public function getPublicObjectURL($ossKey)
    {
        return env('FILE_URL') . $ossKey;
    }

    /**
     * 更新文件额外信息
     * @param $id
     * @param $params
     * @return array
     * @throws FilesException
     */
    public function updateFileExtraFields($id, $params)
    {
        $where = [
            'id' => $id,
        ];
        $model = $this->_fileModel->where($where)->first();
        if (is_null($model)) {
            throw new FilesException(50002);
        }
        checkCompanyPermission($model->company_id);
        $updateData = [
            'relation_field' => $params['relation_field'],
            'module_type' => $params['module_type'] ?? Files::FILES_COMPANY_MODULE_TYPE,
            'extra_fields' => json_encode($params['extra_fields'], JSON_UNESCAPED_UNICODE),
        ];
        try {
            $result = $model->update($updateData);
            if ($result === false) {
                throw new FilesException(50004);
            }
            return ['id' => $id];
        } catch (\Exception $e) {
            throw new FilesException(50004);
        }
    }

    /**
     * 获取企业信息管理文件
     * @param $params
     * @return array
     */
    public function getCompanyFiles($params)
    {
        $companyId = $params['company_id'] ?? getUserInfo()['company_id'];
        checkCompanyPermission($companyId);
        $where = [
            'company_id' => $companyId,
            'module_type' => $params['module_type'],
        ];
        $fields = ['id', 'relation_field', 'file_name', 'preview_url', 'url', 'extra_fields'];
        $fileInfo = $this->_fileModel->searchData($where, $fields);
        $fileInfo = $this->_dealFilesRelation($fileInfo);
        $result = [];
        foreach (self::FILES_COMPANY_RELATION_FIELDS as $relationField => $name) {
            $tmp = [
                'relation_field' => $relationField,
                'name' => $name,
                'files' => [],
            ];
            if (isset($fileInfo[$relationField . '_files'])) {
                $tmp['files'] = $fileInfo[$relationField . '_files'];
            }
            $result[] = $tmp;
        }
        return $result;
    }

    /**
     * 匹配文件信息
     * @param $fileIds
     * @return array
     */
    public function searchFilesForList($fileIds, $type = 1)
    {
        $fields = ['id', 'relation_field', 'file_name', 'preview_url', 'url', 'extra_fields'];
        $fileInfo = $this->_fileModel->whereIn('id', $fileIds)
            ->select($fields)
            ->get()->toArray();
        if ($type == 1) {
            $result = $this->_dealFilesRelation($fileInfo);
        } else {
            $result = $this->_dealFilesRelation2($fileInfo);
        }
        return $result;
    }

    /**
     * 删除文件
     * @param $id
     * @return bool|null
     */
    public function delFile($id)
    {
        return $this->_fileModel->del($id);
    }

    /**
     * 通过关联字段获取最新文件
     * @param $companyId
     * @param $relationField
     * @return mixed
     */
    public function getFileByRelationField($params)
    {
        $companyId = isset($params['company_id']) && $params['company_id'] ? $params['company_id'] : getUserInfo()['company_id'];
        checkCompanyPermission($companyId);
        $where = [
            'company_id' => $companyId,
            'relation_field' => $params['relation_field'],
        ];
        $result = $this->_fileModel->getOne($where, [], ['id', 'DESC']);
        return $result;
    }

    /**
     * 上传文件到本地
     * @param $relationField
     * @param array $params
     * @throws FilesException
     */
    public function upLoadFile2Local($relationField, $file, $params = [])
    {
        $userInfo = getUserInfo(['company_id']);
        $prefix = env('OSS_ENVIRONMENT', '') . DIRECTORY_SEPARATOR . $userInfo['company_id'] . DIRECTORY_SEPARATOR;
        $dir = env('ROOT_FILE_PATH', '') . $prefix;

        if (!is_dir($dir)) {
            @mkdir($dir);
        }

        $this->setFile($file);
        $storage = new  FileSystem($dir);
        $this->_fileUp = new File($relationField, $storage);

        $ossKey = uniqid() . date('YmdHis');
        $this->_fileUp->setName($ossKey);
        $ossKey .=  '.' . $this->_fileUp->getExtension();
        try {
            $this->_fileUp->upload();

            $previewUrl = $url = $this->getPublicObjectURL($prefix . $ossKey);

            $companyId = isset($params['company_id']) && $params['company_id'] ? $params['company_id'] : $userInfo['company_id'];
            $fileLogId = $this->addFilesLog($companyId, $relationField, $ossKey, $url, $previewUrl);
            $returnData = [
                'id' => $fileLogId,
                'file_name' => $this->_originalName,
                'url' => $url,
                'preview_url' => $previewUrl,
            ];
            return $returnData;
        } catch (\Exception $e) {
            throw new FilesException(50001);
        }
    }

    /**
     * 构造关联数组
     * @param $fileInfo
     * @return array
     */
    private function _dealFilesRelation($fileInfo)
    {
        $result = [];
        if (!empty($fileInfo)) {
            foreach ($fileInfo as $file) {
                $key = $file['relation_field'] . '_files';
                if ($file['extra_fields']) {
                    $file['extra_fields'] = json_decode($file['extra_fields'], true);
                } else {
                    $file['extra_fields'] = new \stdClass();
                }
                if (!isset($result[$key])) {
                    $result[$key] = [];
                }
                $result[$key][] = $file;
            }
        }
        return $result;
    }

    /**
     * 列表匹配模式
     * @param $fileInfo
     * @return array
     */
    private function _dealFilesRelation2($fileInfo)
    {
        return array_column($fileInfo, null, 'id');
    }
}
