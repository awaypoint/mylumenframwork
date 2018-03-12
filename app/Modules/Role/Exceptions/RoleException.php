<?php

namespace App\Modules\Role\Exceptions;

use App\Exceptions\ProtectException;

class RoleException extends ProtectException
{
    protected $_codeList = [
        20001=>['msg'=>'设备注册失败'],
    ];
}
