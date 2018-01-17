<?php
namespace Core\Framework\Request;

use Core\Framework\Response;

class RequestCookie
{
    public $params;

    public $contentType;

    public function __construct($data = [], $contentType = '')
    {
        $this->contentType = $contentType;
        $this->params = $data;
    }

    public function getParam($name, $default = false, $func = false)
    {

    }

    public function getParams(array $name = [], $default = false, $func = [])
    {

    }

    public function setCookie($name, $value = null, $expire = null, $path = null, $domain = null, $secure = null, $httponly = null)
    {
        if(!Response::getInstance()->isEndResponse()) {
            $temp = " {$name}={$value};";
            if($expire != null) {
                $temp .= " Expires=".date('D, d M Y H:i:s', $expire).' GMT;';
                $maxAge = $expire - time();
                $temp .= " Max-age={$maxAge};";
            }
            if($path != null) {
                $temp .= " Path={$path};";
            }
            if($domain != null) {
                $temp .= " Domain={$domain};";
            }
            if($secure != null) {
                $temp .= " Secure;";
            }
            if($httponly != null) {
                $temp .= " HttpOnly;";
            }
            $this->setCookie('Set-Cookie', $temp);
            return true;
        }else{
            trigger_error('response has end');
            return false;
        }
    }
}