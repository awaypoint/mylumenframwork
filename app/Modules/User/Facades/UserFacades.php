<?php

namespace App\Modules\User\Facades;

use App\Modules\User\UserRepository;

class UserFacades
{
    private $_userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->_userRepository = $userRepository;
    }

    /**
     * 获取用户信息
     * @param array $fields
     * @return mixed
     */
    public function getUserInfo($fields = [])
    {
        return $this->_userRepository->getUserInfo($fields);
    }

    public function clearUserCache($uid, $type = '')
    {
        return $this->_userRepository->clearUserCache($uid, $type);
    }

    /**
     * 更新用户公司
     * @param $companyId
     */
    public function updateCompanyId($companyId)
    {
        return $this->_userRepository->updateCompanyId($companyId);
    }

    /**
     * 更新用户菜单
     * @param array $hideMenuIds
     * @return mixed
     */
    public function updateUserMenu(array $hideMenuIds)
    {
        return $this->_userRepository->updateUserMenu($hideMenuIds);
    }

    /**
     * 删除公司用户
     * @param $companyId
     * @return mixed
     */
    public function delUserByCompany($companyId)
    {
        return $this->_userRepository->delUserByCompany($companyId);
    }
}
