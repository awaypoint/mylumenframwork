<?php

namespace App\Modules\User;

use App\Modules\Common\CommonRepository;
use App\Modules\Company\Facades\Company;
use App\Modules\Role\Facades\Role;
use GuzzleHttp\Client;
use App\Modules\User\Exceptions\UserException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class UserRepository extends CommonRepository
{
    private $_userModel;
    private $_userPermisionsModel;
    private $_http;

    public function __construct(
        Client $guezzClient,
        EloquentUserModel $userModel,
        EloquentUsersPermissionsModel $usersPermissionsModel
    )
    {
        $this->_http = $guezzClient;
        $this->_userModel = $userModel;
        $this->_userPermisionsModel = $usersPermissionsModel;
    }

    public function loginByPassword($params)
    {
        $userInfo = $this->getUserInfoByUsername($params['username']);
        if (is_null($userInfo)) {
            throw new UserException(10006);
        }
        if ($userInfo['password'] != $params['password']) {
            throw new UserException(10007);
        }
        //本地访问直接查询数据库
        /*
         try {
            $response = $this->_http->post(env('SERVER_REQUEST_URL') . 'oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => env('OAUTH_CLIENT_ID'),
                    'client_secret' => env('OAUTH_CLIENT_SECRET'),
                    'username' => $params['username'],
                    'password' => $params['password'],
                    'scope' => '',
                ],
            ]);
        } catch (\Exception $e) {
            throw new UserException(10001);
        }
        $result = json_decode((string)$response->getBody(), true);
        */
        $result['token_type'] = 'Bearer';
        $result['expires_in'] = '100000';
        $result['access_token'] = 'Bearer';
        $result['refresh_token'] = 'Bearer';
        //获取用户信息
        $roleInfo = Role::getRoleInfo($userInfo['role_id'], ['type']);
        Session::put('uid', $userInfo['id']);
        Session::put('lifetime', time() + env('LIFE_SEC', 10800));
        $result['uid'] = $userInfo['id'];
        $result['company_id'] = $userInfo['company_id'];
        $result['role_type'] = $roleInfo['type'];
        $result['hide_menu_ids'] = $userInfo['hide_menu_ids'];
        $result['username'] = $userInfo['username'];
        $result['avatar_url'] = $userInfo['avatar_url'];

        return $result;
    }

    /**
     * 通过用户名获取用户信息
     * @param $username
     * @param array $fields
     * @return mixed
     */
    public function getUserInfoByUsername($username, $fields = [])
    {
        return $this->_userModel->getOne(['username' => $username], $fields);
    }

    /**
     * 获取用户信息
     * @param $uid
     * @param array $fields
     * @return mixed
     */
    public function getUserInfo($fields = [])
    {
        if (!Session::has('user_info')) {
            $where = [
                'id' => Session::get('uid'),
            ];
            $userInfo = $this->_userModel->getOne($where);
            $roleInfo = Role::getRoleInfo($userInfo['role_id'], ['type']);
            $userInfo['role_type'] = is_null($roleInfo) ? Role::ROLE_COMMON_TYPE : $roleInfo['type'];
            $userInfo['hide_menu_ids'] = json_decode($userInfo['hide_menu_ids'], true);
            $userInfo['companies'] = [];
            if ($userInfo['role_type'] == Role::ROLE_COMMON_TYPE) {
                $userInfo['companies'] = [$userInfo['company_id']];
            }
            if ($userInfo['role_type'] == Role::ROLE_ADMIN_TYPE) {
                $companyIds = Company::getPermissionCompanies($userInfo['id']);
                if (empty($companyIds)) {
                    $companyIds = [-1];//如果没有权限保证这个有值
                }
                $userInfo['companies'] = $companyIds;
            }
            unset($userInfo['password']);
            setUserCache($userInfo);
        }
        $result = Session::get('user_info');
        if (!empty($fields)) {
            foreach ($result as $field => $value) {
                if (!in_array($field, $fields)) {
                    unset($result[$field]);
                }
            }
        }
        return $result;
    }

    /**
     * 更新用户公司
     * @param $companyId
     * @throws UserException
     */
    public function updateCompanyId($companyId)
    {
        $where = [
            'id' => Session::get('uid'),
        ];
        $updateData = [
            'company_id' => $companyId,
        ];
        $result = $this->_userModel->updateData($updateData, $where);
        if (!$result) {
            throw new UserException(10002);
        }
        //重新设置缓存
        $userInfo = $this->getUserInfo();
        $userInfo['company_id'] = $companyId;
        setUserCache($userInfo);
    }

    /**
     * 注册
     * @param $params
     * @return array
     * @throws UserException
     */
    public function register($params)
    {
        $nameWhere = [
            'username' => $params['username'],
        ];
        $isNameRegistered = $this->_userModel->getOne($nameWhere, ['id']);
        if (!is_null($isNameRegistered)) {
            throw new UserException(10003, ['name' => $params['username']]);
        }
        if (isset($params['mobile']) && $params['mobile']) {
            if (!isMobile($params['mobile'])) {
                throw new UserException(10005);
            }
            $mobileWhere = [
                'mobile' => $params['mobile'],
            ];
            $isMobileRegistered = $this->_userModel->getOne($mobileWhere, ['id']);
            if (!is_null($isMobileRegistered)) {
                throw new UserException(10003, ['name' => $params['mobile']]);
            }
        }
        $addData = [
            'username' => $params['username'],
            'password' => $params['password'],
            'mobile' => $params['mobile'] ?? 0,
            'avatar_url' => $params['avatar_url'] ?? '',
            'hide_menu_ids' => '[]',
        ];
        DB::beginTransaction();
        $companyParams = [
            'name' => $params['username'],
        ];
        $companyResult = Company::addCompany($companyParams);
        if (!$companyResult) {
            DB::rollBack();
            throw new UserException(10010);
        }
        try {
            $addData['company_id'] = $companyResult['id'];
            $result = $this->_userModel->add($addData);
            if (!$result) {
                DB::rollBack();
                throw new UserException(10004);
            }
            DB::commit();
            return ['id' => $result];
        } catch (\Exception $e) {
            DB::rollBack();
            throw new UserException(10004);
        }
    }

    /**
     * 修改密码
     * @param $params
     * @return array
     * @throws UserException
     */
    public function modifyPassword($params)
    {
        $userInfo = $this->getUserInfo();
        $uid = $userInfo['id'];
        if ($userInfo['role_type'] == Role::ROLE_SUPER_ADMIN_TYPE && isset($params['id'])) {
            $uid = $params['id'];
        }
        $where = [
            'id' => $uid,
        ];
        $checkPassword = $this->_userModel->getOne($where, ['password']);
        if (is_null($checkPassword)) {
            throw new UserException(10006);
        }
        if (!isset($params['reset']) && $checkPassword['password'] != $params['old_password']) {
            throw new UserException(10007);
        }
        $updateData = [
            'password' => $params['new_password'],
        ];
        $result = $this->_userModel->updateData($updateData, $where);
        if ($result === false) {
            throw new UserException(10008);
        }
        return ['id' => $userInfo['id']];
    }

    /**
     * 用户自定义菜单更新
     * @param $hideMenuIds
     * @return bool
     * @throws UserException
     */
    public function updateUserMenu($hideMenuIds)
    {
        $userInfo = $this->getUserInfo();
        $where = [
            'id' => $userInfo['id'],
        ];
        $result = $this->_userModel->updateData(['hide_menu_ids' => json_encode($hideMenuIds, 256)], $where);
        if ($result === false) {
            throw new UserException(10009);
        }
        $userInfo['hide_menu_ids'] = $hideMenuIds;
        Session::put('user_info', $userInfo);
        return true;
    }

    /**
     * 添加管理员帐号
     * @param $params
     * @return array
     * @throws UserException
     */
    public function addAdminUser($params)
    {
        $userInfo = $this->getUserInfo();
        if ($userInfo['role_type'] != Role::ROLE_SUPER_ADMIN_TYPE) {
            throw new UserException(10012);
        }
        $nameWhere = [
            'username' => $params['username'],
        ];
        $isNameRegistered = $this->_userModel->getOne($nameWhere, ['id']);
        if (!is_null($isNameRegistered)) {
            throw new UserException(10003, ['name' => $params['username']]);
        }
        $addData = [
            'username' => $params['username'],
            'password' => md5(123456),
            'role_id' => Role::ROLE_ADMIN_TYPE,
            'mobile' => $params['mobile'] ?? 0,
            'avatar_url' => $params['avatar_url'] ?? '',
            'hide_menu_ids' => '[]',
        ];
        try {
            $result = $this->_userModel->add($addData);
            if (!$result) {
                throw new UserException(10011);
            }
            return ['id' => $result];
        } catch (\Exception $e) {
            throw new UserException(10011);
        }
    }

    /**
     * 获取用户列表
     * @param $params
     * @param int $page
     * @param int $pageSize
     * @param array $orderBy
     * @return mixed
     */
    public function getUserList($params, $page = 1, $pageSize = 10, $orderBy = [])
    {
        $where = [
            'built_in' => [
                'join' => ['role', 'users.role_id', '=', 'role.id']
            ]
        ];
        if (isset($params['username']) && $params['username']) {
            $where[] = ['users.username', 'LIKE', '%' . $params['username'] . '%'];
        }
        if (isset($params['role_type']) && $params['role_type']) {
            $where[] = ['role.type', '=', $params['role_type']];
        }
        $fields = ['users.id', 'users.username', 'role.type AS rolt_type'];
        $result = $this->_userModel->getList($where, $fields, $page, $pageSize, $orderBy);
        if (isset($result['rows']) && !empty($result['rows'])) {
            foreach ($result['rows'] as &$row) {
                $row['role_type'] = Role::ROLE_TYPE_MAP[$row['rolt_type']] ?? '无';
            }
        }
        return $result;
    }

    /**
     * 删除用户
     * @param $id
     * @return bool|null
     * @throws UserException
     */
    public function delUser($id)
    {
        $userInfo = $this->getUserInfo();
        if ($userInfo['role_type'] != Role::ROLE_SUPER_ADMIN_TYPE) {
            throw new UserException(10012);
        }
        return $this->_userModel->del($id);
    }

    /**
     * 通过公司删除用户
     * @param $companyId
     * @return mixed
     */
    public function delUserByCompany($companyId)
    {
        $where = [
            'company_id' => $companyId,
        ];
        return $this->_userModel->deleteByFields($where);
    }

    /**
     * 获取用户城市权限
     * @param $uid
     * @return mixed
     */
    public function getUserCityPermissions($uid, $fields = [])
    {
        $where = [
            'uid' => $uid,
        ];
        return $this->_userPermisionsModel->searchData($where, $fields);
    }
}