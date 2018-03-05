<?php

use App\Modules\User\Facades\User;
use App\Modules\User\Exceptions\UserException;
use Illuminate\Support\Facades\Session;

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
 * 获取用户信息
 */
if (!function_exists('getUserInfo')) {
    function getUserInfo($uid, $fields = [])
    {
        return User::getUserInfo($uid, $fields);
    }
}

/**
 * 检验是否为超级管理员
 */
if (!function_exists('checkIsSuperUser')) {
    function checkIsSuperUser($uid, $fields = [])
    {
        $userInfo = getUserInfo($uid, $fields);
        if (!$userInfo['is_superuser']) {
            throw new UserException(10010);
        }
        return $userInfo;
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