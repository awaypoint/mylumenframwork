<?php

namespace App\Modules\Waste;

use App\Modules\Common\CommonEloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentWasteTubeModel extends CommonEloquentModel
{
    use SoftDeletes;

    protected $table = 'tube';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'type', 'company_id', 'item_no', 'height', 'pics', 'check', 'remark'];
    //不可编辑字段
    public $guardFillable = ['id', 'type'];

    /**
     * 关联gas表
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gases()
    {
        return $this->hasMany('App\Modules\Waste\EloquentWasteGasModel', 'tube_id');
    }

    /**
     * 关联water表
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function water()
    {
        return $this->hasMany('App\Modules\Waste\EloquentWasteWaterModel', 'tube_id');
    }
}
