<?php
namespace Core\Uti\Tools;

class Constant
{
    static $getInstance;

    static $constValue = [];

    static function getInstance()
    {
        if(!self::$getInstance) {
            self::$getInstance = new static();
        }
        return self::$getInstance;
    }

    function getConstant($name)
    {
        return isset(self::$constValue[$name]) ? self::$constValue[$name] : false;
    }

    function setConstant($name, $value, $over_ride = false)
    {
        if($over_ride) {
            self::$constValue[$name] = $value;
        }else{
            if($this->getConstant($name) === false) {
                self::$constValue[$name] = $value;
            }
        }
    }
}