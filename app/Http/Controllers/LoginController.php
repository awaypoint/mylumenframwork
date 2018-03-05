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
     * @api            {get} /logOut Logout-登出
     * @apiName        logOut
     * @apiGroup       Login
     *
     * @apiDescription 登出
     *
     * @apiSuccessExample {json} 结果描述
     *   {
     *      "msg": "",
     *      "code": 0,
     *      "result":1
     *   }
     */
    public function logOut()
    {
        $token = getToken();
        if ($token) {
            return responseTo($this->_userRepository->logOut($token));
        }
        throw new ProxyException(401);
    }
}
