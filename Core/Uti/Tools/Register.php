<?php
namespace Core\Uti\Tools;

class Register
{
    static $instance;
    private $pool = [];
    private $poolType = ['controller', 'model'];

    static function getInstance()
    {
        if(!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    function getPool($name, $type = 'controller')
    {
        if(in_array($type, $this->poolType)) {
            if(isset($this->pool[$type][$name])) {
                return @unserialize($this->pool[$type][$name]);
            }
        }

        return null;
    }

    function setPool($name, $value, $type = 'controller')
    {
        if(in_array($type, $this->poolType)) {
            $this->pool[$type][$name] = serialize($value);
            return $this;
        }
        return false;
    }
}