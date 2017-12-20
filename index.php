<?php
define('ROOT', __DIR__);
define('PUBLIC_DIR', ROOT.'/Public');
require_once ROOT.'/Core/Uti/Tools/AutoLoader.php';
use \Core\Uti\Tools\AutoLoader;
use \Core\Swoole\Server;
use \Core\Uti\Tools\Config;
use \Core\Uti\Tools\Register;
use \Core\Uti\Tools\Tools;


$loader = AutoLoader::getInstance();
$loader->addNamespace('Core', 'Core');
$loader->addNamespace('App', 'App');
$loader->addNamespace('Config', 'Config');
Register::getInstance();
Tools::getInstance();
$serverConfig = Config::getInstance(ROOT.'/Config')->getConfig('config.server');
if(function_exists('apc_clear_cache')) {
    apc_clear_cache();
}
if(function_exists('opcache_reset')) {
    opcache_reset();
}
Server::getInstance($serverConfig)->startServer();