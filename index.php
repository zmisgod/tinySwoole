<?php
define('ROOT', __DIR__);
require_once __DIR__.'/Core/Uti/Tools/AutoLoader.php';
use \Core\Uti\Tools\AutoLoader;
use \Core\Swoole\Server;
use \Core\Uti\Tools\Config;


$loader = AutoLoader::getInstance();
$loader->addNamespace('Core', 'Core');
$loader->addNamespace('App', 'App');
$loader->addNamespace('Config', 'Config');
$config = Config::getInstance(ROOT.'/Config');
$serverConfig = $config->getConfig('config.server');
Server::getInstance($serverConfig)->startServer();