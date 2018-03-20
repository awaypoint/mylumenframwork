<?php

namespace App\Modules\User;

use App\Modules\Common\CommonRepository;
use GuzzleHttp\Client;
use App\Modules\User\Exceptions\UserException;
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
        if ($response->getStatusCode() == 401) {
            throw new UserException(10001);
        }
        $result = json_decode((string)$response->getBody(), true);
        //获取用户信息
        $userInfo = $this->getUserInfoByUsername($params['username']);
        Session::put('uid', $userInfo['id']);
        $result['uid'] = $userInfo['id'];
        $result['username'] = $userInfo['username'];
        $result['avatar_url'] = $userInfo['avatar_url'];
        //设置缓存
        setUserCache($userInfo);

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
            Session::put('user_info', $this->_userModel->getOne($where));
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
        Session::put('user_info', $userInfo);
    }
}