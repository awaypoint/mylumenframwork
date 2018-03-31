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
    ];
}
