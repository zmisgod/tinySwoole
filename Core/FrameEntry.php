<?php
namespace Core;
use Core\Swoole\ConfigStatus;
use \Core\Uti\Tools\AutoLoader;
use \Core\Swoole\Server;
use \Core\Uti\Tools\Config;
use Core\Uti\Tools\Constant;
use Core\Uti\Tools\Di;
use Core\Uti\Tools\SysConstant;
use \Core\Uti\Tools\Tools;
class FrameEntry
{
    static $instance;
    public static function getInstance()
    {
        if(!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    function defineConstant()
    {
        define('ROOT', __DIR__.'/../');
        define('PUBLIC_DIR', ROOT.'/Public');
    }

    function autoLoader()
    {
        require_once ROOT.'/Core/Uti/Tools/AutoLoader.php';
        $loader = AutoLoader::getInstance();
        $loader->addNamespace('Core', 'Core');
        $loader->addNamespace('App', 'App');
        $loader->addNamespace('Config', 'Config');
    }

    function setConstant()
    {
        Di::getInstance()->set(SysConstant::FRAMEWORK_VERSION, '0.1');
        Di::getInstance()->set(SysConstant::REQUIRE_PHP_VERSION, '7.0.0');
        Di::getInstance()->set(SysConstant::REQUIRE_SWOOLE_VERSION, '1.9.0');
    }

    function loadComponents()
    {
        //config
        Config::getInstance(ROOT.'/Config');

        //constant pool
        Constant::getInstance();

        //tools
        Tools::getInstance();

        ConfigStatus::getInstance();
    }

    function initialization()
    {
        $this->defineConstant();
        $this->autoLoader();
        $this->setConstant();
        $this->loadComponents();
        return $this;
    }

    function getServerConfig()
    {
        return Config::getInstance()->getConfig('config.server');
    }

    function stopServer()
    {
        $pid = file_get_contents(ROOT.'/Log/Server/pid.pid');
        if(!empty($pid)) {
            try{
                if(\swoole_process::kill($pid)){
                    file_put_contents(ROOT.'/Log/Server/pid.pid', 0);
                    return ['code' => 200, 'msg' => 'successful'];
                }else{
                    return ['code' => 400, 'msg' => error_get_last()];
                }
            }catch (\Exception $e) {
                return ['code' => 400, 'msg' => $e->getMessage()];
            }
        }else{
            return ['code' => 400, 'msg' => 'are you start this framework?'];
        }
    }

    function startServer()
    {
        $serverConfig = $this->getServerConfig();
        try{
            Server::getInstance($serverConfig)->startServer();
        }catch(\Exception $e) {
            return ['code' => 400, 'msg' => $e->getMessage()];
        }
    }

    function reloadServer()
    {
        $pid = file_get_contents(ROOT.'/Log/Server/pid.pid');
        if(!empty($pid)) {
            try{
                if(\swoole_process::kill($pid, SIGUSR1)){
                    return ['code' => 200, 'msg' => 'successful'];
                }else{
                    return ['code' => 400, 'msg' => error_get_last()];
                }
            }catch (\Exception $e) {
                return ['code' => 400, 'msg' => $e->getMessage()];
            }
        }else{
            return ['code' => 400, 'msg' => 'are you start this framework?'];
        }
    }

    function getStatus()
    {
        $host = ConfigStatus::getInstance()->host();
        $port = ConfigStatus::getInstance()->httpPort();
        $cmd = "curl -s ".$host.':'.$port.'/index/status';
        exec($cmd, $data);
        if(empty($data) || !isset($data[0])) {
            return ['code' => 400, 'msg' => 'are you start this framework?'];
        }
        $res = json_decode($data[0], true);
        $msg = '';
        foreach($res['result'] as $k => $v) {
            $msg .= $k.' = '.$v.PHP_EOL;
        }
        return ['code' => 200, 'msg' => $msg];
    }
}