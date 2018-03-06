<?php

namespace App\Modules\Setting\Exceptions;

use App\Exceptions\ProtectException;

class SettingException extends ProtectException
{
    protected $_codeList = [
        50001=>['msg'=>'设备注册失败'],
    ];
}
