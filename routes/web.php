<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$app->get('/', function () use ($app) {
    return $app->version();
});
$app->post('/webhook', function () use ($app) {
    $shellExec = shell_exec("bash /var/project/webhook.sh && echo 'ok'");
    var_dump($shellExec);
});

$app->post('/login', 'LoginController@login');
//登录
$app->group(['prefix' => '/', 'middleware' => 'auth'], function () use ($app) {
    $app->get('logout', 'LoginController@logout');
});
//系统设置
$app->group(['prefix' => '/setting', 'middleware' => 'auth'], function () use ($app) {
    $app->get('getMenu', 'SettingController@getMenu');
});
//用户
$app->group(['prefix' => 'users', 'middleware' => 'auth'], function () use ($app) {
    $app->get('getUserInfo', 'UserController@getUserInfo');
    $app->get('getAdminUser', 'UserController@getAdminUserList');
    $app->delete('delAdminUser', 'UserController@delAdminUser');
});