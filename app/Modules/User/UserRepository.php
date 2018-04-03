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
    private $_http;

    public function __construct(
        Client $guezzClient,
        EloquentUserModel $userModel
    )
    {
        $this->_http = $guezzClient;
        $this->_userModel = $userModel;
    }

    public function loginByPassword($params)
    {
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
        //获取用户信息
        $userInfo = $this->getUserInfoByUsername($params['username']);
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
        try {
            $companyParams = [
                'name' => $params['username'],
            ];
            $companyResult = Company::addCompany($companyParams);
            if (!$companyResult) {
                DB::rollBack();
                throw new UserException(10010);
            }
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
        $userInfo = $this->getUserInfo(['id']);
        $where = [
            'id' => $userInfo['id'],
        ];
        $checkPassword = $this->_userModel->getOne($where, ['password']);
        if (is_null($checkPassword)) {
            throw new UserException(10006);
        }
        if ($checkPassword['password'] != $params['old_password']) {
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
}