<?php

namespace App\Modules\User;

use App\Modules\Common\CommonRepository;
use App\Modules\User\Exceptions\UserException;
use Illuminate\Support\Facades\Redis;

class UserRepository extends CommonRepository
{
    private $_userModel;

    public function __construct()
    {
    }

    public function loginByPassword($params)
    {
        $result = [];

        $token = app()->make('oauth2-server.authorizer')->issueAccessToken();
        $result['token'] = $token['access_token'];
        dd($result);die;
        //获取用户信息
        $userInfo = $this->_userModel->getUserInfoByMobile($params['username'], ['id', 'nickname', 'headimgurl']);
        $result['uid'] = $userInfo['id'];
        $result['nickname'] = $userInfo['nickname'];
        $result['headimgurl'] = $userInfo['headimgurl'];
        //设置缓存
        $this->setUserCache($token['access_token'], $result);

        return $result;
    }
}