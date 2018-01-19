<?php
require_once __DIR__ . '/Core/FrameEntry.php';
$server            = \Core\FrameEntry::getInstance()->initialization();
$php_version       = \Core\Uti\Tools\Di::getInstance()->get(\Core\Uti\Tools\SysConstant::REQUIRE_PHP_VERSION);
$swoole_version    = \Core\Uti\Tools\Di::getInstance()->get(\Core\Uti\Tools\SysConstant::REQUIRE_SWOOLE_VERSION);
$framework_version = \Core\Uti\Tools\Di::getInstance()->get(\Core\Uti\Tools\SysConstant::FRAMEWORK_VERSION);
if(phpversion() < $php_version) {
    exit("only support php version >= " . $php_version);
}

if(!extension_loaded('swoole') || swoole_version() <= $swoole_version) {
    exit("you need install swoole and swoole version >= " . $swoole_version);
}

if(function_exists('apc_clear_cache')) {
    apc_clear_cache();
}

if(function_exists('opcache_reset')) {
    opcache_reset();
}
command($server);

function command($server)
{
    global $argv;
    $command = isset($argv[1]) && $argv[1] ? strtolower($argv[1]) : 'start';
    if(in_array($command, ['start', 'stop', 'reload', 'status'])) {
        $command($server);
    }else{
        help($server);
    }
}

/**
 * start framework
 *
 * @param \Core\Swoole\Server $server
 */
function start($server)
{
    $http = true;
    $udp  = $tcp = false;
    if(\Core\Swoole\ConfigStatus::getInstance()->tcpOn()) {
        $tcp = true;
    }
    if(\Core\Swoole\ConfigStatus::getInstance()->udpOn()) {
        $udp = true;
    }
    if($http) {
        $http = success_msg('open');
    } else {
        $http = error_msg('close');
    }
    if($tcp) {
        $tcp = success_msg('open');
    } else {
        $tcp = error_msg('close');
    }
    if($udp) {
        $udp = success_msg('open');
    } else {
        $udp = error_msg('close');
    }
    $http_port = \Core\Swoole\ConfigStatus::getInstance()->httpPort();
    $tcp_port  = \Core\Swoole\ConfigStatus::getInstance()->tcpPort();
    $udp_port  = \Core\Swoole\ConfigStatus::getInstance()->udpPort();
    try {
        $res = <<<EOF
\033[40;32m            TinySwoole starting Successful!         \033[0m
----------------------------------------------------
SERVER TYPE  |  SERVER PORT |   STATUS    |
    HTTP     |     {$http_port}     |   {$http}   |
    TCP      |     {$tcp_port}     |   {$tcp}    |
    UDP      |     {$udp_port}     |   {$udp}    |
----------------------------------------------------

EOF;
        echo $res;
        $server->startServer();
    } catch(\Exception $e) {
        echo "\e[40;31merror: {$e->getMessage()}\e[0m";
    }
}

/**
 * stop framework
 *
 * @param \Core\FrameEntry $server
 */
function stop($server)
{
    $res = <<<EOF
SERVER IS STOPPING:\n
EOF;
    echo $res;
    $res = $server->stopServer();
    checkResult($res);
}

/**
 * stop framework
 *
 * @param \Core\FrameEntry $server
 */
function status($server)
{
    $res = <<<EOF
SERVER STATUS:\n
EOF;
    echo $res;
    $res = $server->getStatus();
    checkResult($res,false);
}

function help($server)
{
    $res = <<<EOF
           \033[49;32m TinySwoole Command List \033[0m
----------------------------------------------------
  \033[49;36m php index.php start \033[0m: start server
   \033[49;36m               stop \033[0m: stop server
 \033[49;36m               reload \033[0m: reload server
 \033[49;36m               status \033[0m: server status
 \033[49;36m               --help \033[0m: need help
----------------------------------------------------

EOF;
    echo $res;
}

function reload(\Core\FrameEntry $server)
{
    $res = <<<EOF
SERVER IS RELOADING:\n
EOF;
    echo $res;
    $res = $server->reloadServer();
    checkResult($res);
}

function error_msg($msg)
{
    return "\e[49;31merror: {$msg}\e[0m";
}

function success_msg($msg)
{
    return "\e[49;32m$msg\e[0m";
}

function checkResult($res, $show_color = true)
{
    if($res['code'] == 200) {
        if($show_color) {
            echo success_msg($res['msg']) . PHP_EOL;
        }else{
            echo $res['msg'].PHP_EOL;
        }
    } else {
        echo error_msg($res['msg']) . PHP_EOL;
    }
}