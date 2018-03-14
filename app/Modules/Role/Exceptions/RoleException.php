<?php

namespace App\Modules\Role\Exceptions;

use App\Exceptions\BaseException;

class RoleException extends BaseException
{
    protected $_codeList = [
        20001=>['msg'=>'设备注册失败'],
    ];
}
