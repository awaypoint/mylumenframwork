<?php

namespace App\Modules\User\Exceptions;

use App\Exceptions\BaseException;

class UserException extends BaseException
{
    protected $_codeList = [
        10001 => ['msg' => '用户名密码错误'],
        10002 => ['msg' => '公司更新失败'],
        10003 => ['msg' => '【{$name}】已被注册'],
        10004 => ['msg' => '注册失败'],
        10005 => ['msg' => '手机号输入错误'],
        10006 => ['msg' => '用户不存在'],
        10007 => ['msg' => '密码输入错误'],
        10008 => ['msg' => '密码修改失败'],
        10009 => ['msg' => '用户信息更新失败'],
        10010 => ['msg' => '公司创建失败'],
        10011 => ['msg' => '管理员帐号添加失败'],
        10012 => ['msg' => '您不是超级管理员'],
    ];
}
