<?php

namespace App\Modules\Role\Facades;

use App\Modules\Role\RoleRepository;

class RoleFacades
{
    public $_roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->_roleRepository = $roleRepository;
    }

    /**
     * 获取用户权限列表
     * @param $uid
     * @return mixed
     */
    public function getUserPermissions()
    {
        return $this->_roleRepository->getUserPermissions();
    }

    /**
     * 获取角色信息
     * @param $roleId
     * @return mixed
     */
    public function getRoleInfo($roleId, $fields = [])
    {
        return $this->_roleRepository->getRoleInfo($roleId, $fields);
    }
}
