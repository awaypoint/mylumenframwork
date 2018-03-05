<?php

namespace App\Http\Controllers;

use App\Modules\Approve\ApproveRepository;
use App\Modules\User\Exceptions\UserException;
use App\Modules\User\UserRepository;
use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    private $wxApp;
    private $_userRepository;
    private $_approveRepository;

    public function __construct(
        UserRepository $userRepository,
        ApproveRepository $approveRepository
    )
    {
        parent::__construct();
        $this->_userRepository = $userRepository;
        $this->_approveRepository = $approveRepository;
    }

    /**
     * @api            {post} /onLogin 登录
     * @apiName        onLogin
     * @apiGroup       Others
     *
     * @apiParam {String} code
     * @apiParam {String} iv
     * @apiParam {String} encryptedData
     * @apiDescription 登录
     *
     * @apiSuccessExample {json} 结果描述
     *   {
     *      "msg":"",
     *      "code":0,
     *      "result": {
     *           "id": "1",//用户id
     *           "token": "abc23dfdfd123dfdfdfdfdf",//token
     *           "role_type": 0,//角色类型 0 啥都不是 1业主 2管理员 --只有管理员才有开关电闸的权限
     *      }
     *   }
     * @apiSuccessExample {json} 参数描述
     *   {
     *       "code": "no example",
     *       "iv": "no example",
     *       "encryptedData": "no example",
     *   }
     */
    public function onLogin(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
            'iv' => 'required',
            'encryptedData' => 'required',
        ]);
        app()->configure('wxConfig');
        $this->wxApp = new Application(Config::get('wxConfig'));
        $miniProgram = $this->wxApp->mini_program;

        $userSession = $miniProgram->sns->getSessionKey($request->get('code'));
        if (isset($userInfo->errcode)) {
            throw new UserException(10001);
        }
        $openId = $userSession->openid;
        $userInfo = $miniProgram->encryptor->decryptData($userSession->session_key, $request->get('iv'), $request->get('encryptedData'));
//        $userInfo = [];
//        $openId = 'oSEQG0fj81fXPC5tm-XUfaOAkC0Y';
        $result = $this->_userRepository->onLogin($openId, $userInfo);
        if (is_null($result)) {
            throw new UserException(10001);
        }
        return responseTo($result);
    }

    /**
     * @api            {get} /clear/user 清除用户缓存
     * @apiName        userClear
     * @apiGroup       Others
     *
     * @apiParam {Int} uid 用户id
     * @apiParam {String=token} [type] 类型 ''用户信息 'token' token信息
     * @apiDescription 清除用户缓存
     *
     * @apiSuccessExample {json} 结果描述
     *   {
     *      "msg":"",
     *      "code":0,
     *      "result": 1
     *   }
     * @apiSuccessExample {json} 参数描述
     *   {
     *       "uid": 1,
     *       "type": "token",
     *   }
     */
    public function celarUserRedis(Request $request)
    {
        $this->validate($request, [
            'uid' => 'required',
        ]);
        $prefix = TUJIA_UID_PREFIX;
        $value = $request->get('uid');
        if ($request->has('type') && $request->get('type') == 'token') {
            $lastToken = $this->_userRepository->getLastTokenByUid($value);
            if (is_null($lastToken)) {
                return responseTo(-1);
            }
            $value = $lastToken['token'];
            $prefix = TUJIA_TOKEN_PREFIX;
        }
        return responseTo(Redis::expire($prefix . $value, 0));
    }

    /**
     * @api            {get} /users/getUserInfo 获取用户信息
     * @apiName        getUserInfo
     * @apiGroup       Users
     *
     * @apiDescription 获取用户信息
     *
     * @apiSuccessExample {json} 结果描述
     *   {
     *      "msg":"",
     *      "code":0,
     *      "result": [{
     *           "id": 1,//用户id
     *           "nickname": 1,//昵称
     *           "role_type": 0,//角色类型 0 啥都不是 1业主 2管理员 --只有管理员才有开关电闸的权限
     *           "is_superuser": 0,//是否是超级管理员 0 否 1是 -- 超级管理员具有【权限管理】菜单
     *           "approve_type": 0,//审核类型 0 不处于审核状态 1业主审核状态 2管理员审核状态
     *           "avatar_url": "https://abcd",//微信头像
     *           "approve_count": 0,//未审核单据数量
     *      },{...}]
     *   }
     * @apiSuccessExample {json} 参数描述
     *   {
     *   }
     */
    public function getUserInfo()
    {
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
