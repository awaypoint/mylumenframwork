<?php

namespace App\Modules\User\Exceptions;

use App\Exceptions\BaseException;

class UserException extends BaseException
{
    protected $_codeList = [
        10001 => ['msg' => '用户名密码错误'],
        10002 => ['msg' => '公司更新失败'],
        10003 => ['msg' => '【{$name}】已被注册'],
        10004 => ['msg' => '注册失败'],
        10005 => ['msg' => '手机号输入错误'],
    ];
}
