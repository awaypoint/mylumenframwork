<?php

namespace App\Modules\Website\Exceptions;

use App\Exceptions\BaseException;

class WebsiteException extends BaseException
{
    protected $_codeList = [
        70001 => ['msg' => '案例添加失败'],
        70002 => ['msg' => '专家添加失败'],
        70003 => ['msg' => '新闻添加失败'],
        70004 => ['msg' => '案例不存在'],
        70005 => ['msg' => '专家不存在'],
        70006 => ['msg' => '新闻不存在'],
        70007 => ['msg' => '常见问题添加失败'],
        70008 => ['msg' => '常见问题不存在'],
    ];
}
