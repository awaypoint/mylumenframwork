<?php

namespace App\Modules\Waste\Exceptions;

use App\Exceptions\BaseException;

class WasteException extends BaseException
{
    protected $_codeList = [
        60001 => ['msg' => '危险废物信息添加失败'],
        60002 => ['msg' => '您无权操作此信息'],
        60003 => ['msg' => '危废信息不存在'],
        60004 => ['msg' => '危废信息更新失败'],
        60005 => ['msg' => '删除危废信息失败'],
        60006 => ['msg' => '废气类型参数错误'],
        60007 => ['msg' => '排放口添加失败'],
        60008 => ['msg' => '排放口信息不存在'],
        60009 => ['msg' => '排放口修改失败'],
        60010 => ['msg' => '排放口删除失败'],
        60011 => ['msg' => '废气信息不存在'],
        60012 => ['msg' => '排放口类型错误'],
        60013 => ['msg' => '废水类型参数错误'],
        60014 => ['msg' => '废水信息不存在'],
        60015 => ['msg' => '噪音信息不存在'],
    ];
}
