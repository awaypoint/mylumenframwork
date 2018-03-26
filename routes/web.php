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
$app->get('/testSession', 'TestController@testSession');


$app->group(['prefix' => '/', 'middleware' => 'auth'], function () use ($app) {
    $app->get('logout', 'UserController@logout');
});
//系统设置
$app->group(['prefix' => '/setting', 'middleware' => 'auth'], function () use ($app) {
    $app->get('getMenu', 'SettingController@getMenu');
    $app->get('getWasteTypeCombo', 'SettingController@getWasteTypeCombo');
    $app->put('updateUserMenu', 'SettingController@updateUserMenu');
    $app->post('forbittenAdmin', 'SettingController@forbittenAdmin');
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
    $app->get('getCompanyFiles', 'FilesController@getCompanyFiles');
});
//文件
$app->group(['prefix' => 'files', 'middleware' => 'auth'], function () use ($app) {
    $app->post('upLoadFile', 'FilesController@upLoadFile');
    $app->put('updateFileExtraFields', 'FilesController@updateFileExtraFields');
    $app->delete('delFile', 'FilesController@delFile');
});
//产污情况
$app->group(['prefix' => 'waste', 'middleware' => 'auth'], function () use ($app) {
    $app->post('addWasteMaterial', 'WasteController@addWasteMaterial');
    $app->get('getWasteMaterialList', 'WasteController@getWasteMaterialList');
    $app->get('getWasteMaterialDetail', 'WasteController@getWasteMaterialDetail');
    $app->put('updateWasteMaterial', 'WasteController@updateWasteMaterial');
    $app->delete('delWasteMaterial', 'WasteController@delWasteMaterial');
    $app->post('addWasteGas', 'WasteController@addWasteGas');
    $app->delete('delWasteGas', 'WasteController@delWasteGas');
    $app->put('updateWasteGas', 'WasteController@updateWasteGas');
    $app->post('addWasteGasTube', 'WasteController@addWasteGasTube');
    $app->put('updateWasteGasTube', 'WasteController@updateWasteGasTube');
    $app->get('getWasteGasTubeCombo', 'WasteController@getWasteGasTubeCombo');
    $app->delete('delWasteGasTube', 'WasteController@delWasteGasTube');
    $app->get('getWasteGasDetail', 'WasteController@getWasteGasDetail');
    $app->get('getWasteGasList', 'WasteController@getWasteGasList');
    $app->get('getWasteGasTubeDetail', 'WasteController@getWasteGasTubeDetail');
});