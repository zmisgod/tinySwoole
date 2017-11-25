<?php
namespace Core\Framework;

class Cookie
{
    private $cookie;

    function __construct(\swoole_http_request $swoole_http_request)
    {
        $this->cookie = $swoole_http_request->cookie;
    }

    function getCookie($name)
    {
        if(isset($this->cookie[$name])) {
            return $this->cookie[$name];
        }
        return null;
    }

    function setCookie($name, $value)
    {
        if(isset($this->cookie[$name]) && $this->cookie[$name] === $value) {
            return $this;
        }
        $this->cookie[$name] = $value;
        return $this;
    }
}