<?php

namespace App\Modules\Files;

use App\Modules\Common\CommonRepository;
use App\Modules\Files\Exceptions\FilesException;
use JohnLui\AliyunOSS;
use Exception;
use DateTime;

class FilesRepository extends CommonRepository
{
    /* 城市名称：
     *
     *  经典网络下可选：杭州、上海、青岛、北京、张家口、深圳、香港、硅谷、弗吉尼亚、新加坡、悉尼、日本、法兰克福、迪拜
     *  VPC 网络下可选：杭州、上海、青岛、北京、张家口、深圳、硅谷、弗吉尼亚、新加坡、悉尼、日本、法兰克福、迪拜
     */
    private $city = '杭州';
    // 经典网络 or VPC
    private $networkType = '经典网络';
    private $ossClient;
    private $bucketName = 'ghhbgj';
    private $_file;
    private $_fileModel;

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
            $this->city,
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
    }

    private function getClientOriginalName()
    {
        return $this->_file->getClientOriginalName();
    }

    private function getClientOriginalExtension()
    {
        return $this->_file->getClientOriginalExtension();
    }

    private function getMimeType()
    {
        return $this->_file->getMimeType();
    }

    private function getPathname()
    {
        return $this->_file->getPathname();
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
        $prefix = self::FILES_PREFIX_MAP[$relationField] ?? '';
        $ossKey = $prefix . md5($this->getClientOriginalName() . time()) . '.' . $this->getClientOriginalExtension();
        $options = [
            'ContentType' => $this->getMimeType(),
        ];
        $result = $this->ossClient->uploadFile($ossKey, $this->getPathname(), $options);
        if ($result === false) {
            throw new FilesException(50001);
        }
        $url = $this->getPublicObjectURL($ossKey);
        $fileLogId = $this->addFilesLog($relationField, $ossKey, $url);
        $returnData = [
            'id' => $fileLogId,
            'file_name' => $this->getClientOriginalName(),
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
            'file_name' => $this->getClientOriginalName(),
            'url' => $url,
            'oss_key' => $ossKey,
        ];
        $result = $this->_fileModel->add($addData);
        if (!$result) {
            throw new FilesException(50001);
        }
        return $result;
    }

    // 获取公开文件的 URL
    public function getPublicObjectURL($ossKey)
    {
        return $this->ossClient->getPublicUrl($ossKey);
    }
}
