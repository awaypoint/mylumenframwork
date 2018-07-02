<?php

namespace App\Modules\Book;

use App\Modules\Book\Exceptions\BookException;
use App\Modules\Common\CommonRepository;
use App\Modules\Website\EloquentWebsiteExpertModel;

class BookRepository extends CommonRepository
{
    private $_bookModel;
    private $_expertModel;

    public function __construct(
        EloquentBookModel $bookModel,
        EloquentWebsiteExpertModel $expertModel
    )
    {
        $this->_bookModel = $bookModel;
        $this->_expertModel = $expertModel;
    }

    /**
     * 获取预定列表
     * @param $params
     * @param int $page
     * @param int $pageSize
     * @param array $orderBy
     * @return mixed
     */
    public function getBookList($params, $page = 1, $pageSize = 10, $orderBy = [])
    {
        $where = [];
        if (isset($params['start_time']) && $params['start_time']) {
            $where['created_at >= '] = strtotime($params['start_time']);
        }
        if (isset($params['end_time']) && $params['end_time']) {
            $where['created_at <= '] = strtotime($params['end_time']) + 86400 - 1;
        }
        $result = $this->_bookModel->getList($where, [], $page, $pageSize, $orderBy);
        if (isset($result['rows']) && !empty($result['rows'])) {
            $expertIds = array_column($result['rows'], 'expert_id');
            $replaceWhere = [
                'built_in' => [
                    'whereIn' => ['id', $expertIds],
                    'withTrashed' => '',
                ]
            ];
            $expertInfo = $this->_expertModel->searchForList($expertIds, ['expert'], 'id', $replaceWhere);
            foreach ($result['rows'] as &$row) {
                $row['expert_name'] = isset($expertInfo[$row['expert_id']]) ? $expertInfo[$row['expert_id']]['expert'] : '';
                $row['created_at'] = date('Y-m-d H:i', $row['created_at']);
            }
        }
        return $result;
    }

    /**
     * 获取预定单详情
     * @param $id
     * @return mixed
     * @throws BookException
     */
    public function getBookDetail($id)
    {
        $where = [
            'id' => $id,
        ];
        $result = $this->_bookModel->getOne($where);
        if (is_null($result)) {
            throw new BookException(60015);
        }
        $result['created_at'] = date('Y-m-d H:i', $result['created_at']);
        if ($result['expert_id'] > 0) {
            $expertWhere = [
                'id' => $result['expert_id'],
                'built_in' => [
                    'withTrashed' => ''
                ]
            ];
            $expertInfo = $this->_expertModel->getOne($expertWhere, ['expert']);
            $result['expert_name'] = is_null($expertInfo) ? '' : $expertInfo['expert'];
        }
        return $result;
    }

    /**
     * 标记预订单为已处理
     * @param $id
     * @param $params
     * @return array
     */
    public function dealBook($id, $params)
    {
        $params['status'] = 1;
        return $this->_bookModel->up($id, $params);
    }

    /**
     * 删除预订单
     * @param $id
     * @return bool|null
     */
    public function delBook($id)
    {
        return $this->_bookModel->del($id);
    }
}
