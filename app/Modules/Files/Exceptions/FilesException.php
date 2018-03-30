<?php

namespace App\Modules\Files\Exceptions;

use App\Exceptions\BaseException;

class FilesException extends BaseException
{
    protected $_codeList = [
        50001=>['msg'=>'文件上传失败'],
        50002=>['msg'=>'文件信息不存在'],
        50003=>['msg'=>'您没有修改此文件权限'],
        50004=>['msg'=>'文件信息更新失败'],
        50005=>['msg'=>'文件删除失败'],
        50006=>['msg'=>'只支持上传小于5M的文件'],
    ];
}
