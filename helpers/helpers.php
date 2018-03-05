<?php

use App\Modules\User\Facades\User;
use App\Modules\User\Exceptions\UserException;

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
 * 发送curl请求
 */
if (!function_exists('curlRequest')) {
    function curlRequest($url, $method, $params = [], $extraHeaders = [])
    {
        $headerArray = [
            "Content-type:application/json;",
            "Accept:application/json",
            "Authorization:Basic YXdheToxMjM0NTY=",
        ];
        if (!empty($extraHeaders)) {
            $headerArray = array_merge($extraHeaders, $headerArray);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); //设置请求方式
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
        if (!empty($params)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));//设置提交的字符串
        }
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output, true);
        return $output;
    }
}

/**
 * 发布mqtt消息
 */
if (!function_exists('publishEmqtt')) {
    function publishEmqtt($topic, $msg)
    {
        $clientId = 'PHP_CLIENT_' . str_random(16);

        $mqtt = new \Bluerhinos\phpMQTT(env('MQTT_SERVER'), env('MQTT_PORT'), $clientId);
        if ($mqtt->connect(true, NULL, env('MQTT_PHP_USERNAME'), env('MQTT_PHP_PASSWORD'))) {
            $mqtt->publish($topic, $msg, 0);
            $mqtt->close();
        }
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
        $userInfo = getUserInfo($uid,$fields);
        if (!$userInfo['is_superuser']) {
            throw new UserException(10010);
        }
        return $userInfo;
    }
}