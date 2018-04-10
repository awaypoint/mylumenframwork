<?php

namespace App\Modules\User;

use App\Modules\Common\CommonEloquentModel;
use App\Modules\User\Exceptions\UserException;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Auth\Authorizable;
use Laravel\Passport\HasApiTokens;

class EloquentUserModel extends CommonEloquentModel
{
    use SoftDeletes,HasApiTokens, Authenticatable, Authorizable;

    protected $table = 'users';
    protected $dateFormat = 'U';
    //采用白名单模式
    public $fillable = ['id', 'password', 'username', 'role_id', 'avatar_url', 'company_id', 'hide_menu_ids'];

    /**
     * 关联adminUser表
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function relation()
    {
        return $this->hasMany('App\Modules\User\EloquentUserRelationModel', 'uid');
    }

    /**
     * 登录验证用户名
     * @param $username
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public function findForPassport($username)
    {
        $result = $this->where('username', $username)->first();
        if (is_null($result)){
            throw new UserException(10006);
        }
        return $result;
    }

    /**
     * 登录验证密码
     * @param $password
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public function validateForPassportPasswordGrant($password)
    {
        if ($this->password != $password){
            throw new UserException(10007);
        }
        return $this;
    }

    /**
     * 登录获取验证成功用户id
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->id;
    }
}
