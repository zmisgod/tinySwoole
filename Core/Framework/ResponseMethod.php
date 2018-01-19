<?php
namespace Core\Framework;

use Core\Framework\Request\Cookie;

class ResponseMethod extends HttpBase
{
    private $statusCode = 200;

    private $statusMsg = 'OK';

    private $cookies = [];

    function getStatusCode()
    {
        return $this->statusCode;
    }

    function getResponseMsg()
    {
        return $this->statusMsg;
    }

    function setStatus($code, $statusMsg = '')
    {
        if($code === $this->statusCode) {
            return $this;
        }else{
            $this->statusCode = $code;
            if(empty($statusMsg)) {
                $this->statusMsg = Status::getReasonPhrase($code);
            }else{
                $this->statusMsg = $statusMsg;
            }
            return $this;
        }
    }

    /**
     * @return array Cookie
     */
    function getCookies()
    {
        return $this->cookies;
    }

    function setResponseCookie(Cookie $cookie)
    {
        $this->cookies[$cookie->getName()] = $cookie;
    }
}