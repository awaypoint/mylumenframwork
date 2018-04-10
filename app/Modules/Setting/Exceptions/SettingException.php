<?php

namespace App\Modules\Setting\Exceptions;

use App\Exceptions\BaseException;

class SettingException extends BaseException
{
    protected $_codeList = [
        30001=>['msg'=>'工业园区添加失败'],
        30002=>['msg'=>'污染物添加失败'],
        30003=>['msg'=>'污染物类型错误'],
        30004=>['msg'=>'污染物不存在'],
        30005=>['msg'=>'工业园区不存在'],
        30006=>['msg'=>'工业园区更新失败'],
        30007=>['msg'=>'工业园区删除失败'],
        30008=>['msg'=>'【$field_name】参数错误'],
        30009=>['msg'=>'权限设置失败'],
    ];
}
