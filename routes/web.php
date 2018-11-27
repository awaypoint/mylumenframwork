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
//登录、注册
$app->post('/login', 'LoginController@login');
$app->post('/register', 'LoginController@register');
$app->get('/combo', 'LoginController@combo');
$app->get('/testSession', 'TestController@testSession');
$app->get('/logout', 'LoginController@logout');

//用户
$app->group(['prefix' => 'users', 'middleware' => 'auth'], function () use ($app) {
    $app->get('getUserInfo', 'UserController@getUserInfo');
    $app->put('modifyPassword', 'UserController@modifyPassword');
    $app->post('addAdminUser', 'UserController@addAdminUser');
    $app->put('resetPassword', 'UserController@resetPassword');
    $app->get('getUserList', 'UserController@getUserList');
    $app->delete('delUser', 'UserController@delUser');
});
//文件
$app->group(['prefix' => 'files', 'middleware' => 'auth'], function () use ($app) {
    $app->post('upLoadFile', 'FilesController@upLoadFile');
    $app->post('multUploadFiles', 'FilesController@multUploadFiles');
    $app->put('updateFileExtraFields', 'FilesController@updateFileExtraFields');
    $app->delete('delFile', 'FilesController@delFile');
    $app->get('getFileByRelationField', 'FilesController@getFileByRelationField');
    $app->post('testExcel', 'FilesController@testExcel');
});