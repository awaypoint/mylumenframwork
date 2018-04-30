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
    public function addCase($params)
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
     * 获取案例详情
     * @param $id
     * @param array $fields
     * @return mixed
     * @throws WebsiteException
     */
    public function getCaseDetail($id, $fields = [])
    {
        $where = [
            'id' => $id,
        ];
        $result = $this->_caseModel->getOne($where, $fields);
        if (is_null($result)) {
            throw new WebsiteException(70004);
        }
        return $result;
    }

    /**
     * 修改案例
     * @param $id
     * @param $params
     * @return array
     */
    public function updateCase($id, $params)
    {
        return $this->_caseModel->up($id, $params);
    }

    /**
     * 刪除案例
     * @param $id
     * @return bool|null
     */
    public function delCase($id)
    {
        return $this->_caseModel->del($id);
    }

    /**
     * 获取案例列表
     * @param $params
     * @param int $page
     * @param int $pageSize
     * @param array $orderBy
     * @return mixed
     */
    public function getCasesList($params, $page = 0, $pageSize = 0, $orderBy = [])
    {
        $where = [];

        if (isset($params['category_id']) && $params['category_id']) {
            $where['category_id'] = $params['category_id'];
        }
        $fields = ['id', 'title', 'desc'];
        $result = $this->_caseModel->getList($where, $fields, $page, $pageSize, $orderBy);
        return $result;
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
     * 获取专家详情
     * @param $id
     * @param array $fields
     * @return mixed
     * @throws WebsiteException
     */
    public function getExpertDetail($id, $fields = [])
    {
        $where = [
            'id' => $id,
        ];
        $result = $this->_expertModel->getOne($where, $fields);
        if (is_null($result)) {
            throw new WebsiteException(70005);
        }
        return $result;
    }

    /**
     * 修改专家
     * @param $id
     * @param $params
     * @return array
     */
    public function updateExpert($id, $params)
    {
        return $this->_expertModel->up($id, $params);
    }

    /**
     * 删除专家
     * @param $id
     * @return bool|null
     */
    public function delExpert($id)
    {
        return $this->_expertModel->del($id);
    }

    /**
     * 获取专家列表
     * @param $params
     * @param int $page
     * @param int $pageSize
     * @param array $orderBy
     * @return mixed
     */
    public function getExpertsList($params, $page = 0, $pageSize = 0, $orderBy = [])
    {
        $where = [];
        if (isset($params['category_id']) && $params['category_id']) {
            $where['category_id'] = $params['category_id'];
        }
        $fields = ['id', 'expert', 'img'];
        $result = $this->_expertModel->getList($where, $fields, $page, $pageSize, $orderBy);
        return $result;
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

    /**
     * 获取新闻详情
     * @param $id
     * @param array $fields
     * @return mixed
     * @throws WebsiteException
     */
    public function getNewsDetail($id, $fields = [])
    {
        $where = [
            'id' => $id,
        ];
        $result = $this->_newsModel->getOne($where, $fields);
        if (is_null($result)) {
            throw new WebsiteException(70006);
        }
        $result['updated_at'] = date('Y.m.d', $result['updated_at']);
        return $result;
    }

    /**
     * 修改新闻
     * @param $id
     * @param $params
     * @return array
     */
    public function updateNews($id, $params)
    {
        return $this->_newsModel->up($id, $params);
    }

    /**
     * 删除新闻
     * @param $id
     * @return bool|null
     */
    public function delNews($id)
    {
        return $this->_newsModel->del($id);
    }

    /**
     * 获取新闻列表
     * @param $params
     * @param int $page
     * @param int $pageSize
     * @param array $orderBy
     * @return mixed
     */
    public function getNewsList($params, $page = 0, $pageSize = 0, $orderBy = [])
    {
        $where = [];
        if (isset($params['type']) && $params['type']) {
            $where['type'] = $params['type'];
        }
        $fields = ['id', 'title', 'detail'];
        $result = $this->_newsModel->getList($where, $fields, $page, $pageSize, $orderBy);
        return $result;
    }
}