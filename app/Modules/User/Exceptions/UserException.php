<?php

namespace App\Modules\User\Exceptions;

use App\Exceptions\TujiaException;

class UserException extends TujiaException
{
    protected $_codeList = [
        10001 => ['msg' => '登录失败'],
        10002 => ['msg' => '用户添加失败'],
        10003 => ['msg' => '登录日志插入失败'],
        10004 => ['msg' => '用户未登录'],
        10005 => ['msg' => '用户不存在'],
        10006 => ['msg' => '授权已过期，请重新登录'],
        10007 => ['msg' => '用户更新失败'],
        10008 => ['msg' => 'token获取失败'],
        10009 => ['msg' => 'admin插入失败'],
        10010 => ['msg' => '您不是超级管理员'],
        10011 => ['msg' => '权限用户不存在'],
        10012 => ['msg' => '无法删除超级用户'],
        10013 => ['msg' => '权限用户删除失败'],
        10014 => ['msg' => 'type参数错误'],
    ];
}
