<?php
namespace App\Components\Wechat;

use Core\Uti\Tools\Config;
use Core\Uti\Tools\Log;
use EasyWeChat\Factory;

class Wechat
{
    protected static $instance;

    protected $application;

    static function getInstance()
    {
        if(!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $config = Config::getInstance()->getConfig('config.wechat');
        if(!$config) {
            Log::getInstance()->log('没有找到相关wechat配置文件');
        }else {
            $application = Factory::officialAccount($config);

            $this->application = $application;
        }
    }

    public function getApplication()
    {
        return $this->application;
    }
}