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

    public function getUserInfo($uid, $fields = [])
    {
        return $this->_userRepository->getUserInfo($uid, $fields);
    }

    public function clearUserCache($uid, $type = '')
    {
        return $this->_userRepository->clearUserCache($uid, $type);
    }
}
