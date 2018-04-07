<?php

namespace App\Http\Controllers;

use App\Modules\User\UserRepository;
use Illuminate\Http\Request;

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
            'new_password_confirm' => 'required|same:new_password',
        ]);
        $result = $this->_userRepository->modifyPassword($request->all());
        return responseTo($result, '密码修改成功');
    }

    /**
     * 添加管理员帐号
     * @param Request $request
     * @return array
     */
    public function addAdminUser(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
        ]);
        $result = $this->_userRepository->addAdminUser($request->all());
        return responseTo($result, '管理员帐号添加成功');
    }

    /**
     * 重置密码
     * @param Request $request
     * @return array
     */
    public function resetPassword(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $params = $request->all();
        $params['reset'] = true;
        $params['new_password'] = md5(123456);
        $result = $this->_userRepository->modifyPassword($params);
        return responseTo($result, '密码重置成功');
    }

    /**
     * 获取用户列表
     * @param Request $request
     * @return array
     */
    public function getUserList(Request $request)
    {
        list($page, $pageSize, $order) = getPageSuit($request);
        $result = $this->_userRepository->getUserList($request->all(), $page, $pageSize, $order);
        return responseTo($result);
    }

    /**
     * 删除用户
     * @param Request $request
     * @return array
     */
    public function delUser(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_userRepository->delUser($request->get('id'));
        return responseTo($result, '删除用户成功');
    }
}
