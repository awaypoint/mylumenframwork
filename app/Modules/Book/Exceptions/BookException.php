<?php

namespace App\Modules\Book\Exceptions;

use App\Exceptions\BaseException;

class BookException extends BaseException
{
    protected $_codeList = [
        80001 => ['msg' => '预订单不存在'],
    ];
}
