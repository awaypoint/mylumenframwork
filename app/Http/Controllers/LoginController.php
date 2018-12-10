<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Modules\User\UserRepository;
use Illuminate\Http\Request;

class LoginController extends BaseController
{
    private $_userRepository;

    public function __construct(
        UserRepository $userRepository
    )
    {
        $this->_userRepository = $userRepository;
    }

    /**
     * 登录
     * @param Request $request
     * @return array
     * @throws \App\Modules\User\Exceptions\UserException
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);
        $params = $request->all();
        $result = $this->_userRepository->loginByPassword($params);
        return responseTo($result);
    }

    /**
     * 注册
     * @param Request $request
     * @return array
     * @throws \App\Modules\User\Exceptions\UserException
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
            'password_confirm' => 'required|same:password',
        ]);

        $result = $this->_userRepository->register($request->all());
        return responseTo($result, '注册成功');
    }

    /**
     * 登出
     */
    public function logOut()
    {
        Session::flush();
        return responseTo('退出成功');
    }


    public function combo()
    {
        $result = $this->_userRepository->getUserInfo([]);
        dd($result);
    }
}
