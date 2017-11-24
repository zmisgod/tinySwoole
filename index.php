<?php
define('ROOT', __DIR__);
require_once __DIR__.'/Core/Uti/Tools/AutoLoader.php';
use \Core\Uti\Tools\AutoLoader;
use \Core\Swoole\Server;


$loader = AutoLoader::getInstance();
$loader->addNamespace('Core', 'Core');
$loader->addNamespace('App', 'App');
Server::getInstance()->startServer();