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
//登录、注册
$app->post('/login', 'LoginController@login');
$app->post('/register', 'LoginController@register');
$app->get('/combo', 'LoginController@combo');


$app->group(['prefix' => '/', 'middleware' => 'auth'], function () use ($app) {
    $app->get('logout', 'UserController@logout');
});
//系统设置
$app->group(['prefix' => '/setting', 'middleware' => 'auth'], function () use ($app) {
    $app->get('getMenu', 'SettingController@getMenu');
    $app->get('getWasteTypeCombo', 'SettingController@getWasteTypeCombo');
});
//用户
$app->group(['prefix' => 'users', 'middleware' => 'auth'], function () use ($app) {
    $app->get('getUserInfo', 'UserController@getUserInfo');
    $app->put('modifyPassword', 'UserController@modifyPassword');
});
//企业信息
$app->group(['prefix' => 'company', 'middleware' => 'auth'], function () use ($app) {
    $app->post('addCompany', 'CompanyController@addCompany');
    $app->get('getCompanyDetail', 'CompanyController@getCompanyDetail');
    $app->put('updateCompany', 'CompanyController@updateCompany');
    $app->post('addProduct', 'CompanyController@addProduct');
    $app->get('getProductList', 'CompanyController@getProductList');
    $app->get('getProductDetail', 'CompanyController@getProductDetail');
    $app->put('updateProduct', 'CompanyController@updateProduct');
    $app->delete('delProduct', 'CompanyController@delProduct');
});
//文件
$app->group(['prefix' => 'files', 'middleware' => 'auth'], function () use ($app) {
    $app->post('upLoadFile', 'FilesController@upLoadFile');
});