<?php
namespace Core\Framework\Request;

class RequestServer
{
    private $server;

    public function __construct($server)
    {
        $this->server = $server;
    }

    public function getServer($name)
    {
        return isset($this->server[$name]) ? $this->server[$name] : false;
    }

    public function setServer($name, $value)
    {
        if(isset($this->server[$name]) && $this->server[$name] === $value) {

        }else{
            if(is_string($value)) {
                $this->server[$name] = $value;
            }else if(is_array($value)) {
                $data = implode(', ', $value);
                $this->server[$name] = $data;
            }
        }
    }
}