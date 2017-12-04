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

    function curl_get($url)
    {
        //初始化
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);
        //打印获得的数据
        return $output;
    }
}