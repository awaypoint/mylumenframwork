<?php

use App\Modules\User\Facades\User;
use Illuminate\Support\Facades\Session;
use App\Modules\Role\Facades\Role;
use App\Exceptions\BaseException;

/**
 * 统一返回
 */
if (!function_exists('responseTo')) {
    function responseTo($content = [], string $msg = '', int $code = 0)
    {
        $response = [
            'msg' => $msg,
            'code' => $code,
            'result' => $content,
        ];
        return $response;
    }
}

/**
 * 页面跳转统一
 */
if (!function_exists('redirectTo')) {
    function redirectTo($url)
    {
        header('Refresh:0;url=' . $url);
        exit();
    }
}

/**
 * 生成单号
 */
if (!function_exists('createItemNo')) {
    function createItemNo($prefix = '')
    {
        return $prefix . time() . rand(1000, 9999);
    }
}

/**
 * 获取时间段时间戳
 */
if (!function_exists('getDayTime')) {
    function getDayTime($time = '', $startTime = '00:00')
    {
        $time = $time ? $time : time();
        $formatTime = strtotime(date('Ymd', $time) . ' ' . $startTime);
        return ['start_time' => $formatTime, 'end_time' => $formatTime + 86400];
    }
}

/**
 * 分钟转换成X小时X分钟
 * 支持开始时间-结束时间
 */
if (!function_exists('minToStr')) {
    function minToStr(int $minute = 0, int $startTime = 0, int $endTime = 0)
    {
        if ($minute <= 0 && $startTime >= 0 && $endTime > 0 && $endTime > $startTime) {
            $minute = ceil(($endTime - $startTime) / 60);
        }
        $str = '';
        $hour = intval($minute / 60);
        if ($hour > 0) {
            $str .= $hour . '小时';
        }
        $min = $minute % 60;
        if ($min > 0) {
            $str .= $min . '分钟';
        }

        return $str ? $str : '0分钟';
    }
}

/**
 * 检验手机号合法性
 */
if (!function_exists('isMobile')) {
    function isMobile($mobile)
    {
        return (preg_match("/^1[34578]{1}\d{9}$/", $mobile)) ? true : false;
    }
}

/**
 * 检验邮箱合法性
 */
if (!function_exists('isEmail')) {
    function isEmail($email)
    {
        return filter_var($email,FILTER_VALIDATE_EMAIL) ? true : false;
    }
}

/**
 * 获取用户信息
 */
if (!function_exists('getUserInfo')) {
    function getUserInfo($fields = [])
    {
        return User::getUserInfo($fields);
    }
}

/**
 * 设置用户缓存
 */
if (!function_exists('setUserCache')) {
    function setUserCache($cache)
    {
        Session::put('user_info', $cache);
    }
}

/**
 * 检查用户权限
 */
if (!function_exists('checkCompanyPermission')) {
    function checkCompanyPermission($companyId = 0)
    {
        $userInfo = getUserInfo();
        if ($userInfo['role_type'] == Role::ROLE_COMMON_TYPE && $companyId != $userInfo['company_id']){
            throw new BaseException(406);
        }
    }
}