<?php

namespace App\Modules\Setting\Exceptions;

use App\Exceptions\BaseException;

class SettingException extends BaseException
{
    protected $_codeList = [
        50001=>['msg'=>'设备注册失败'],
    ];
}
