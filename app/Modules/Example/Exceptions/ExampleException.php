<?php

namespace App\Modules\Example\Exceptions;

use App\Exceptions\TujiaException;

class ExampleException extends TujiaException
{
    protected $_codeList = [
        50001=>['msg'=>'设备注册失败'],
    ];
}
