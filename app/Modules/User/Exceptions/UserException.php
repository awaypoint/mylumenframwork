<?php

namespace App\Modules\User\Exceptions;

use App\Exceptions\BaseException;

class UserException extends BaseException
{
    protected $_codeList = [
        10001 => ['msg' => '用户名密码错误'],
    ];
}
