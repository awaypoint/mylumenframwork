<?php

namespace App\Exceptions;

class BaseException extends \Exception
{
    protected $_defaultCode = [
        'msg'=>'用户中心的异常'
    ];

    //模板替换格式
    private $_tmplData = [];

    /**
     * 默认异常
     * @var array
     */
    private $_defaultCodeList = [
        400 => [ 'msg' => "请求参数有误。"],
        401 => [ 'msg' => "授权Token错误。"],
        402 => [ 'msg' => "暂无权限。"],
        403 => [ 'msg' => "服务器拒绝执行。"],
        404 => [ 'msg' => "请求方法不存在。"],
        405 => [ 'msg' => "禁止操作。"],
        415 => [ 'msg' => "请求类型错误。"],
        408 => [ 'msg' => "请求超时。"],
        409 => [ 'msg' => "登录超时"],
        500 => [ 'msg' => "网络异常"],
        501 => [ 'msg' => "服务器不支持当前请求所需要的某个功能。"],
    ];

    protected $_codeList = [];

    public function __construct(int $code, array $tmplData = [], \Exception $previous = null)
    {
        $this->code         = $code;
        $this->_tmplData    = $tmplData;
        $message            = $this->generateMsg();
        parent::__construct($message, $code, $previous);
    }

    /**
     * 生成错误内容文本
     * @return mixed
     */
    public function generateMsg(){
        $_tmpl = $this->_getMsgTemplate();
        if(empty($this->_tmplData)){
            return $_tmpl;
        }
        $replace = array_keys($this->_tmplData);
        foreach($replace as &$v){
            $v = '{$'.$v.'}';
        }
        return str_replace($replace, $this->_tmplData, $_tmpl);
    }

    /**
     * 获取消息文本
     * @return string
     */
    private function _getMsgTemplate() : string{
        $_errorCode = $this->_defaultCode;
        if(isset($this->_codeList[$this->code])){
            $_errorCode = $this->_codeList[$this->code];
        }elseif(isset($this->_defaultCodeList[$this->code])){
            $_errorCode = $this->_defaultCodeList[$this->code];
        }

        return $_errorCode['msg'];
    }
}
