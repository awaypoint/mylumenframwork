<?php

namespace App\Modules\Website\Exceptions;

use App\Exceptions\BaseException;

class WebsiteException extends BaseException
{
    protected $_codeList = [
        70001=>['msg'=>'案例添加失败'],
        70002=>['msg'=>'专家添加失败'],
        70003=>['msg'=>'新闻添加失败'],
    ];
}
