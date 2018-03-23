<?php

namespace App\Modules\Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CommonEloquentModel extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this::saving(function ($model){
            $model->updated_by = getUserInfo()['id'];
        });
        $this::updating(function ($model){
            $model->updated_by = getUserInfo()['id'];
        });
    }

    /**
     * 新建
     * @param $addData
     * @return bool
     */
    public function add($addData)
    {
        $result = $this->create($addData);
        if ($result) {
            return $result->id;
        }
        return false;
    }

    /**
     * 批量插入
     * @param $addData
     * @return mixed
     */
    public function addBatch($addData)
    {
        return DB::table($this->getTable())->insert($addData);
    }

    /**
     * 更新数据
     * @param $updateData
     * @param $updateWhere
     * @return mixed
     */
    public function updateData($updateData, $updateWhere)
    {
        return $this->where($updateWhere)->update($updateData);
    }

    /**
     * 删除数据
     * @param $id
     * @return mixed
     */
    public function deleteData($id, $isForce = false)
    {
        if ($isForce) {
            return $this->where('id', $id)->forceDelete();
        }
        return $this->where('id', $id)->delete();
    }

    /**
     * 批量删除数据
     * @param $where
     * @param bool $isForce
     * @return mixed
     */
    public function deleteByFields($where, $isForce = false)
    {
        if ($isForce) {
            return $this->_createModel($where)->forceDelete();
        }
        return $this->_createModel($where)->delete();
    }

    /**
     * 获取单条记录
     * @param array $where
     * @param array $fields
     * @param string $orderBy
     * @param string $sortBy
     * @return mixed
     */
    public function getOne(array $where, array $fields = [], $orderBy = [], $sortBy = '')
    {
        $model = $this->_createModel($where);
        $this->_createCondition($model, $fields, 0, 0, $orderBy, $sortBy);
        $result = $model->first();
        if (!is_null($result)) {
            return $result->toArray();
        }
        return $result;
    }

    /**
     * 获取列表数据
     * @param array $where
     * @param array $fields
     * @param int $page
     * @param int $pageSize
     * @param string $orderBy
     * @param string $sortBy
     * @return mixed
     */
    public function getList(array $where, array $fields = [], $page = 0, $pageSize = 0, $orderBy = [], $sortBy = '')
    {
        $model = $this->_createModel($where);
        $result['total'] = $model->count();
        $result['total_page'] = $pageSize > 0 ? ceil($result['total'] / $pageSize) : 0;
        $this->_createCondition($model, $fields, $page, $pageSize, $orderBy, $sortBy);
        $result['rows'] = $model->get()->toArray();
        return $result;
    }

    /**
     * 查找数据
     * @param array $where
     * @param array $fields
     * @param string $orderBy
     * @param string $sortBy
     * @return mixed
     */
    public function searchData(array $where, array $fields = [], $orderBy = [], $sortBy = '')
    {
        $model = $this->_createModel($where);
        $this->_createCondition($model, $fields, 0, 0, $orderBy, $sortBy);
        //echo $model->toSql();die;
        return $model->get()->toArray();
    }

    /**
     * 分组统计
     * @param array $where
     * @param array $fields
     * @param string $countField
     * @return mixed
     */
    public function groupCount(array $where, array $fields = [], string $countField = 'id')
    {
        $model = $this->_createModel($where);
        $strFields = '';
        if (!empty($fields)) {
            $strFields = ',' . implode(',', $fields);
        }
        $model->select(DB::raw('count(' . $countField . ') AS count' . $strFields));
        return $model->get()->toArray();
    }

    /**
     * 构造模型
     * @param $where
     * @return mixed
     */
    private function _createModel(& $where)
    {
        $builtIn = [];
        if (isset($where['built_in'])) {
            $builtIn = $where['built_in'];
            unset($where['built_in']);
        }
        $model = $this->where($where);
        if (!empty($builtIn)) {
            foreach ($builtIn as $keyWord => $condition) {
                if (is_array($condition) && count($condition) > 1) {
                    if (count($condition) == 2) {
                        $model->$keyWord($condition[0], $condition[1]);
                    }
                    if (count($condition) == 3) {
                        $model->$keyWord($condition[0], $condition[1], $condition[2]);
                    }
                    if (count($condition) == 4) {
                        $model->$keyWord($condition[0], $condition[1], $condition[2], $condition[3]);
                    }
                } else {
                    $model->$keyWord($condition);
                }
            }
        }
        return $model;
    }

    /**
     * 构造查询条件
     * @param $model
     * @param array $fields
     * @param int $page
     * @param int $pageSize
     * @param string $orderBy
     * @param string $sortBy
     */
    private function _createCondition(& $model, array $fields, int $page = 0, int $pageSize = 0, array $orderBy = [], string $sortBy = '')
    {
        if (empty($fields)) {
            $fields = $this->fillable;
        }
        $model->select($fields);
        if (!empty($orderBy)) {
            $model->orderBy($orderBy[0], $orderBy[1]);
        }
        if ($sortBy) {
            $model->sortBy($sortBy);
        }
        if ($page > 0 && $pageSize > 0) {
            $model->offset(($page - 1) * $pageSize);
            $model->limit($pageSize);
        }
    }
}
