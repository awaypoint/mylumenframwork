<?php

namespace App\Modules\Files\Exceptions;

use App\Exceptions\BaseException;

class FilesException extends BaseException
{
    protected $_codeList = [
        50001=>['msg'=>'文件上传失败'],
    ];
}
