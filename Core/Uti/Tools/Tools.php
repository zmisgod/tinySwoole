<?php
namespace Core\Uti\Tools;

class Tools
{
    static $instance;

    static function getInstance()
    {
        if(!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    function SEND_JSON($code, $result, $msg)
    {
        return json_encode([
            'code' => $code,
            'msg' => $msg,
            'result' => $result
        ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
    }
}