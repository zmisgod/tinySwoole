<?php
define('ROOT', __DIR__);
require_once ROOT.'/Core/Uti/Tools/AutoLoader.php';
use \Core\Uti\Tools\AutoLoader;
use \Core\Swoole\Server;
use \Core\Uti\Tools\Config;
use \Core\Uti\Tools\Register;


$loader = AutoLoader::getInstance();
$loader->addNamespace('Core', 'Core');
$loader->addNamespace('App', 'App');
$loader->addNamespace('Config', 'Config');
Register::getInstance();
$serverConfig = Config::getInstance(ROOT.'/Config')->getConfig('config.server');
Server::getInstance($serverConfig)->startServer();