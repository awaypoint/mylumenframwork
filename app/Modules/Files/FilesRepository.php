<?php

namespace App\Modules\Files;

use App\Modules\Common\CommonRepository;
use App\Modules\Files\Exceptions\FilesException;
use JohnLui\AliyunOSS;
use Exception;

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

    const FILES_PREFIX_MAP = [
        'env_approve_code' => '环评资料/'
    ];

    public function __construct(
        EloquentFilesModel $filesModel,
        $isInternal = false
    )
    {
        $this->_fileModel = $filesModel;
        if ($this->networkType == 'VPC' && !$isInternal) {
            throw new Exception("VPC 网络下不提供外网上传、下载等功能");
        }

        $this->ossClient = AliyunOSS::boot(
            env('OSS_CITY'),
            $this->networkType,
            $isInternal,
            env('OSS_ACCESSKEY_ID'),
            env('OSS_ACCESSKEY_SECRET')
        );
        $this->ossClient->setBucket($this->bucketName);
    }

    private function setFile($file)
    {
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
    public function upLoadFile($relationField, $file)
    {
        $this->setFile($file);
        $prefix = env('OSS_ENVIRONMENT','');
        $prefix .= self::FILES_PREFIX_MAP[$relationField] ?? '';
        $ossKey = md5($this->_originalName . time()) . '.' . $this->_originalExtension;
        $options = [
            'ContentType' => $this->_mimeType,
            'ContentDisposition' => 'filename=' . $this->_originalName,
        ];
        $result = $this->ossClient->uploadFile($prefix . $ossKey, $this->_pathname, $options);
        if ($result === false) {
            throw new FilesException(50001);
        }
        $url = $this->getPublicObjectURL($ossKey);
        $fileLogId = $this->addFilesLog($relationField, $ossKey, $url);
        $returnData = [
            'id' => $fileLogId,
            'file_name' => $this->_originalName,
            'url' => $url,
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
    public function addFilesLog($relationField, $ossKey, $url)
    {
        $userInfo = getUserInfo(['company_id']);
        $addData = [
            'company_id' => $userInfo['company_id'],
            'relation_field' => $relationField,
            'file_name' => $this->_originalName,
            'url' => $url,
            'oss_key' => $ossKey,
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
        return $this->ossClient->getPublicUrl($ossKey);
    }
}
