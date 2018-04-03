<?php

namespace App\Modules\Role;

use App\Modules\Common\CommonRepository;
use Illuminate\Support\Facades\Session;

class RoleRepository extends CommonRepository
{
    private $_roleModel;

    public function __construct(
        EloquentRoleModel $eloquentRoleModel
    )
    {
        $this->_roleModel = $eloquentRoleModel;
    }

    /**
     * 获取用户权限
     * @param $uid
     * @return mixed
     */
    public function getUserPermissions()
    {
        if (!Session::has('permissions')) {
            $userInfo = getUserInfo();
            $where = [
                'id' => $userInfo['role_id'],
                'built_in' => [
                    'with' => 'relation'
                ],
            ];
            $result = $this->_roleModel->getOne($where);
            Session::put('permissions', $result);
        }
        $permissions = [];
        $rolePermissions = Session::get('permissions');
        if (!is_null($rolePermissions) && isset($rolePermissions['relation']) && !empty($rolePermissions['relation'])) {
            foreach ($rolePermissions['relation'] as $relation) {
                if ($relation['permission']) {
                    $permissions[] = $relation['permission'];
                }
                $relationPersmissions = json_decode($relation['relation_permission'], true);
                if (!empty($relationPersmissions)) {
                    $permissions = array_merge($permissions, $relationPersmissions);
                }
            }
        }
        return $permissions;
    }

    /**
     * 获取角色信息
     * @param $roleId
     * @param array $fields
     * @return mixed
     */
    public function getRoleInfo($roleId, $fields = [])
    {
        return $this->_roleModel->getOne(['id' => $roleId], $fields);
    }
}
