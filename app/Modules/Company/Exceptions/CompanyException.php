<?php

namespace App\Modules\Company\Exceptions;

use App\Exceptions\BaseException;

class CompanyException extends BaseException
{
    protected $_codeList = [
        40001=>['msg'=>'您已经注册过公司，请联系管理员'],
        40002=>['msg'=>'【{$companyName}】公司已被注册，请联系管理员'],
        40003=>['msg'=>'新建公司资料失败'],
        40004=>['msg'=>'企业不存在'],
        40005=>['msg'=>'手机号输入错误'],
        40006=>['msg'=>'邮箱输入错误'],
        40007=>['msg'=>'【{$fileName}】参数错误'],
        40008=>['msg'=>'您还未注册公司'],
        40009=>['msg'=>'企业信息修改失败'],
        40010=>['msg'=>'产品添加失败'],
        40011=>['msg'=>'产品不存在'],
        40012=>['msg'=>'产品修改失败'],
        40013=>['msg'=>'产品删除失败'],
        40014=>['msg'=>'分厂信息添加失败'],
        40015=>['msg'=>'分厂信息删除失败'],
        40016=>['msg'=>'分厂地址不能为空'],
        40017=>['msg'=>'您没有查看公司列表的权限'],
    ];
}
