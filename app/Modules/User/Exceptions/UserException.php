<?php

namespace App\Modules\User\Exceptions;

use App\Exceptions\ProtectException;

class UserException extends ProtectException
{
    protected $_codeList = [
        10001 => ['msg' => '用户名密码错误'],
    ];
}
