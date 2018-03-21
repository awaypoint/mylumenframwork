<?php

namespace App\Http\Controllers;

use App\Modules\User\Exceptions\UserException;
use App\Modules\User\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    private $_userRepository;

    public function __construct(
        UserRepository $userRepository
    )
    {
        parent::__construct();
        $this->_userRepository = $userRepository;
    }

    /**
     * 获取用户信息
     * @return array
     */
    public function getUserInfo()
    {
        $result = $this->_userRepository->getUserInfo();
        return responseTo($result, '获取用户信息成功');
    }

    /**
     * 修改密码
     * @param Request $request
     * @return array
     */
    public function modifyPassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'new_password' => 'required',
        ]);
        $result = $this->_userRepository->modifyPassword($request->all());
        return responseTo($result, '密码修改成功');
    }
}
