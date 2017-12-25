<?php
namespace Core\Swoole;

use Core\Uti\Tools\Config;

class ConfigStatus
{
    protected static $instance;

    private $config;

    static function getInstance()
    {
        if(!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->config = Config::getInstance()->getConfig('config.server');
    }

    public function httpPort()
    {
        return $this->config['port'];
    }

    public function host()
    {
        return $this->config['host'];
    }

    public function serverType()
    {
        return $this->config['server_type'];
    }

    public function socketType()
    {
        return $this->config['socket_type'];
    }

    public function mode()
    {
        return $this->config['mode'];
    }

    public function setting($key = false)
    {
        if($key !== false) {
            if(isset($this->config[$key])) {
                return $this->config[$key];
            }else{
                return false;
            }
        }
        return $this->config['setting'];
    }

    public function multiPort()
    {
        return $this->config['multi_port'];
    }

    public function tcpOn()
    {
        if($this->multiPort()) {
            return $this->config['multi_port_settings']['tcp']['open'];
        }
        return false;
    }

    public function tcpSetting($key = false)
    {
        if($key !== false) {
            if(isset($this->config['multi_port_settings']['tcp'][$key])) {
                return $this->config['multi_port_settings']['tcp'][$key];
            }
        }
        return $this->config['multi_port_settings']['tcp'];
    }

    public function udpOn()
    {
        if($this->multiPort()) {
            return $this->config['multi_port_settings']['udp']['open'];
        }
        return false;
    }

    public function udpSetting($key = false)
    {
        if($key !== false) {
            if(isset($this->config['multi_port_settings']['udp'][$key])) {
                return $this->config['multi_port_settings']['udp'][$key];
            }
        }
        return $this->config['multi_port_settings']['udp'];
    }

    public function tcpPort()
    {
        return $this->tcpSetting('port');
    }

    public function udpPort()
    {
        return $this->udpSetting('port');
    }
}