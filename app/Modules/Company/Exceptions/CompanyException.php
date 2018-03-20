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
    ];
}
