<?php
namespace Core\Framework\Request;


class RequestCookie
{
    private $params;

    public function __construct($data = [])
    {
        $this->params = $data;
    }

    public function getCookie($name = null)
    {
        if($name === null) {
            return $this->params;
        }else{
            return isset($this->params[$name]) ? $this->params[$name] : false;
        }
    }
}