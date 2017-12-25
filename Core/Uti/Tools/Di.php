<?php
namespace Core\Uti\Tools;

class Di
{
    protected static $instance;

    protected $container = [];
    static function getInstance()
    {
        if(!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    function set($key, $obj, ...$arg)
    {
        if(count($arg) == 1 && is_array($arg)) {
            $arg = $arg[0];
        }
        $this->container[$key] = [
            'obj' => $obj,
            'params' => $arg
        ];
        return $this;
    }

    function delete($key)
    {
        unset($this->container[$key]);
    }

    function clear()
    {
        $this->container = [];
    }

    function get($key) {
        if(isset($this->container[$key])) {
            $result = $this->container[$key];
            if(is_object($result['obj'])) {
                return $result['obj'];
            }else if(is_callable($result['obj'])) {
                $ret = call_user_func_array($result['obj'], $result['params']);
                $this->container[$key]['obj'] = $ret;
                return $this->container[$key]['obj'];
            }else if(is_string($result['obj']) && class_exists($result['obj'])) {
                $reflection = new \ReflectionClass($result['obj']);
                $ins = $reflection->newInstanceArgs($result['params']);
                $this->container[$key]['obj'] = $ins;
                return $this->container[$key]['obj'];
            }else{
                return $result['obj'];
            }
        }else{
            return null;
        }
    }
}