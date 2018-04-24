<?php

namespace App\Modules\Website;

use App\Modules\Common\CommonRepository;
use App\Modules\Website\Exceptions\WebsiteException;

class WebsiteRepository extends CommonRepository
{
    private $_websiteModel;
    private $_caseModel;
    private $_expertModel;
    private $_newsModel;

    public function __construct(
        EloquentWebsiteModel $websiteModel,
        EloquentWebsiteCasesModel $casesModel,
        EloquentWebsiteExpertModel $expertModel,
        EloquentWebsiteNewsModel $newsModel
    )
    {
        $this->_websiteModel = $websiteModel;
        $this->_caseModel = $casesModel;
        $this->_expertModel = $expertModel;
        $this->_newsModel = $newsModel;
    }

    /**
     * 更新基础设置
     * @param $setId
     * @param $params
     * @return array
     */
    public function upBaseSet($setId, $params)
    {
        return $this->_websiteModel->up($setId, $params);
    }

    /**
     * 添加案例
     * @param $params
     * @return array
     * @throws WebsiteException
     */
    public function addCases($params)
    {
        $addData = [
            'category_id' => $params['category_id'],
            'title' => $params['title'],
            'img' => $params['img'],
            'desc' => $params['desc'],
            'detail' => $params['detail'],
        ];
        try {
            $result = $this->_caseModel->add($addData);
            return ['id' => $result];
        } catch (\Exception $e) {
            throw new WebsiteException(70001);
        }
    }

    /**
     * 添加专家
     * @param $params
     * @return array
     * @throws WebsiteException
     */
    public function addExpert($params)
    {
        $addData = [
            'category_id' => $params['category_id'],
            'expert' => $params['expert'],
            'img' => $params['img'],
            'desc' => $params['desc'] ?? '',
        ];
        try {
            $result = $this->_expertModel->add($addData);
            return ['id' => $result];
        } catch (\Exception $e) {
            throw new WebsiteException(70002);
        }
    }

    /**
     * 添加新闻
     * @param $params
     * @return array
     * @throws WebsiteException
     */
    public function addNews($params)
    {
        $addData = [
            'type' => $params['type'],
            'title' => $params['title'],
            'detail' => $params['detail'],
        ];
        try {
            $result = $this->_newsModel->add($addData);
            return ['id' => $result];
        } catch (\Exception $e) {
            throw new WebsiteException(70003);
        }

    }
}
