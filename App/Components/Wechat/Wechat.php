<?php
namespace App\Components\Wechat;

use Core\Uti\Tools\Config;
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

    public function __construct($config = null)
    {
        if(empty($config)) {
            $config = Config::getInstance()->getConfig('config.wechat');
        }
        $application    = Factory::officialAccount($config);

        $this->application = $application;
    }

    public function getApplication()
    {
        return $this->application;
    }
}