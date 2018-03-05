<?php

namespace App\Http\Controllers;

use App\Modules\User\Exceptions\UserException;
use App\Modules\User\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;
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

    public function getUserInfo()
    {
        var_export(Session::all());die;
        $fields = ['id', 'nickname', 'role_type', 'is_superuser', 'avatar_url'];
        $result = getUserInfo($this->uid, $fields);
        $approveInfo = $this->_approveRepository->getApprovingData($this->uid, ['type']);
        $result['approve_type'] = $approveInfo['type'] ?? 0;
        $result['approve_count'] = 0;
        if ($result['is_superuser']) {
            $result['approve_count'] = $this->_approveRepository->getApproveCount($this->uid);
        }
        return responseTo($result);
    }

    /**
     * @api            {get} /users/getAdminUser 权限列表
     * @apiName        approveList
     * @apiGroup       Users
     *
     * @apiParam {Int} page 页码
     * @apiParam {Int} page_size 每页条数
     * @apiParam {Int=1,2} [role_type] 角色类型 1业主 2管理员
     * @apiDescription 权限列表
     *
     * @apiSuccessExample {json} 结果描述
     *   {
     *      "msg":"",
     *      "code":0,
     *      "result": {
     *          "total": 1,
     *          "total_page": 1,
     *          "rows"[{
     *              "id": 3,//用户id
     *              "company_id": 1,
     *              "nickname": "away",//微信昵称
     *              "avatar_url": "https://abcd",//微信头像
     *              "user_name": "",//用户名称
     *              "mobile": 0,//手机号
     *              "role_type": 1,//角色类型
     *          },{...}]
     *      }
     *   }
     * @apiSuccessExample {json} 参数描述
     *   {
     *       "page": 1,//页码
     *       "page_size": 10,//每页条数
     *       "role_type": 1,//角色类型
     *   }
     */
    public function getAdminUserList(Request $request)
    {
        $page = $request->get('page') ?? 1;
        $pageSize = $request->get('page_size') ?? 10;
        $result = $this->_userRepository->getAdminUserList($this->uid, [], $request->all(), $page, $pageSize);
        return responseTo($result);
    }

    /**
     * @api            {delete} /users/delAdminUser 删除权限用户
     * @apiName        delAdminUser
     * @apiGroup       Users
     *
     * @apiParam {Int} id 用户id
     * @apiDescription 删除权限用户
     *
     * @apiSuccessExample {json} 结果描述
     *   {
     *      "msg":"",
     *      "code":0,
     *      "result": {
     *          "id": 1,
     *      }
     *   }
     * @apiSuccessExample {json} 参数描述
     *   {
     *       "id": 1,//用户id
     *   }
     */
    public function delAdminUser(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_userRepository->delAdminUser($this->uid, $request->get('id'));
        return responseTo($result);
    }

    /**
     * @api            {get} /change 更改用户角色类型
     * @apiName        change
     * @apiGroup       Others
     *
     * @apiParam {Int} uid
     * @apiParam {Int=0,1,2,3} type
     * @apiDescription 更改用户角色类型
     *
     * @apiSuccessExample {json} 结果描述
     *   {
     *      "msg":"",
     *      "code":0,
     *      "result": true
     *   }
     * @apiSuccessExample {json} 参数描述
     *   {
     *      "uid":7,//用户id
     *      "type":1,//角色类型（0什么都不是 1业主 2管理员 3超级管理员）
     *   }
     */
    public function changeRoleType(Request $request)
    {
        $this->validate($request, [
            'uid' => 'required',
            'type' => 'required',
        ]);
        $result = $this->_userRepository->changeRoleType($request->get('uid'), $request->get('type'));
        return responseTo($result);
    }
}
