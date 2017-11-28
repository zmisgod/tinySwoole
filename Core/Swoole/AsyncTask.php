<?php
namespace Core\Swoole;

class AsyncTask
{
    static $instance;

    static function getInstance()
    {
        if(!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}