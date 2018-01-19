<?php

namespace Core\Swoole;

use Core\Framework\AbstractTask;
use Core\Framework\AbstractTcpInstance;
use Core\Framework\AbstractUdpInstance;
use Core\Framework\Dispatch;
use Core\Framework\Request;
use Core\Framework\Response;
use Core\Framework\Status;
use Core\Uti\Tools\Config;
use Core\Uti\Tools\Tools;

class Server
{
    const SERVER_TYPE_SERVER = 'SERVER_TYPE_SERVER';
    const SERVER_TYPE_WEB = 'SERVER_TYPE_WEB';
    const SERVER_TYPE_WS = 'SERVER_TYPE_WS';
    const LISTEN_PORT_TCP = 'tcp_server';
    const LISTEN_PORT_UDP = 'udp_server';

    protected static $instance;
    //主server
    protected $server;

    //监听端口的tcp server
    protected $tcp_server;

    //监听端口的udp server;
    protected $udp_server;

    private $listen_servers = [];

    public $server_config;

    private $is_start = false;

    function __construct($config)
    {
        $this->server_config = $config;
        if ($this->server_config['server_type'] == self::SERVER_TYPE_WEB) {
            $this->server = new \swoole_http_server($this->server_config['host'], $this->server_config['port'], $this->server_config['mode']);
        } elseif ($this->server_config['server_type'] == self::SERVER_TYPE_WS) {
            $this->server = new \swoole_websocket_server($this->server_config['host'], $this->server_config['port'], $this->server_config['mode']);
        } elseif ($this->server_config['server_type'] == self::SERVER_TYPE_SERVER) {
            $this->server = new \swoole_server($this->server_config['host'], $this->server_config['port'], $this->server_config['mode'], $this->server_config['socket_type']);
        } else {
            die('please reset your App\Config\config.php server.server_type column');
        }
    }

    public static function getInstance($config = null)
    {
        if (!isset(self::$instance)) {
            self::$instance = new static($config);
        }
        return self::$instance;
    }

    /**
     * 主server
     *
     * @return \swoole_server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * 多端口监听 -- tcp server
     *
     * @return \Swoole\Server
     */
    private function getTcpServer()
    {
        return $this->tcp_server;
    }

    /**
     * 多端口监听 -- udp server
     *
     * @return \Swoole\Server
     */
    private function getUpdServer()
    {
        return $this->udp_server;
    }

    /**
     * start server
     */
    public function startServer()
    {
        $this->server->set($this->server_config['setting']);
        $this->serverStart();
        $this->workerStart();
        if ($this->server_config['socket_type'] != self::SERVER_TYPE_SERVER) {
            $this->onRequest();
        }
        if ($this->server_config['multi_port']) {
            $this->listenMultiPort();
        }
        $this->onTask();
        $this->onTaskFinish();
        $this->workerStop();
        $this->workerError();
        $this->is_start = true;
        //server start
        $this->getServer()->start();
        return $this;
    }

    function is_start()
    {
        return $this->is_start;
    }

    /**
     * 重点
     * 之前的fpm的模式下，request，response，header都是通过获取全局函数$_XXX获取
     * 现在使用swoole的时候，我们就不能根据之前的想法写代码，需要转换思路
     *
     * 1。request的数据都是通过获取swoole来获取
     * 2。response的status，cookie，html等数据都是先通过框架发送给swoole，再由swoole发送到用户浏览器界面
     * 3。修改代码需要重启swoole后生效
     * 4。html的数据可以先存到数据流中，最后swoole直接读取数据流发送到用户浏览器中
     * 5。swoole的类是会保存到内存中，所以单列模式的优越性会很好的体现。
     * 6。不要使用静态属性（保存单列属性除外）
     */
    private function onRequest()
    {
        $this->getServer()->on('request',
            function (\swoole_http_request $swoole_request, \swoole_http_response $swoole_response) {
                //框架请求的类
                Request::getInstance($swoole_request);
                //框架响应的类
                $userResponse = Response::getInstance($swoole_response);
                try {
                    //框架路由
                    Dispatch::getInstance()->dispatch();
                } catch (\Exception $e) {
                    $userResponse->writeJson($e->getCode(), '', $e->getMessage());
                }
                //设置这次请求结束
                $userResponse->end(true);
            });
    }

    private function serverStart()
    {
        $this->getServer()->on("start", function (\swoole_http_server $server) {

        });
    }

    private function workerStart()
    {
        $this->getServer()->on("WorkerStart", function (\swoole_server $server, $workerId) {

        });
    }

    private function workerStop()
    {
        $this->getServer()->on("WorkerStop", function (\swoole_server $server, $workerId) {

        });
    }

    private function pipeMessage()
    {
        $this->getServer()->on('pipeMessage', function (\swoole_server $server, $from_id, $data) {

        });
    }

    private function onTask()
    {
        $task_num = Config::getInstance()->getConfig('config.server.setting.task_worker_num');
        if ($task_num) {
            $this->getServer()->on("task", function (\swoole_http_server $server, $taskId, $workerId, $data) {
                try {
                    if (is_string($data) && class_exists($data)) {
                        $data = new $data();
                    }
                    if ($data instanceof AbstractTask) {
                        return $data->handleTask($server, $taskId, $workerId);
                    }
                    return null;
                } catch (\Exception $e) {
                    return null;
                }
            });
        }
    }

    private function onTaskFinish()
    {
        $task_num = Config::getInstance()->getConfig('config.server.setting.task_worker_num');
        if ($task_num) {
            $this->getServer()->on("finish", function (\swoole_http_server $server, $taskId, $taskObj) {
                if ($taskObj instanceof AbstractTask) {
                    $taskObj->finishTask($server, $taskId, $taskObj->getDataForFinishCallBack());
                }
            });
        }
    }

    private function workerError()
    {
        $this->getServer()->on("workerError", function (\swoole_http_server $server, $worker_id, $worker_pid, $exit_code) {
            echo 'worker_id : ' . $worker_id . ' worker pid :' . $worker_pid . ' exit_code : ' . $exit_code . PHP_EOL;
        });
    }

    /**
     * 多端口监听
     */
    private function listenMultiPort()
    {
        foreach ($this->server_config['multi_port_settings'] as $server_type => $v) {
            if ($v['open']) {
                $this->listen_servers[$server_type.'_server'] = $v['setting'];
                $this->{$server_type.'_server'} = $this->getServer()->addlistener($this->server_config['host'], $v['port'], $v['socket_type']);
                $this->{$server_type.'_server'}->set($v['setting']);
            }
        }
        if (!empty($this->listen_servers)) {
            foreach ($this->listen_servers as $type => $setting) {
                if ($type == self::LISTEN_PORT_TCP) {
                    $this->tcpOnReceive();
                } else if ($type == self::LISTEN_PORT_UDP) {
                    $this->udpOnPacket();
                }
            }
        }
    }

    /**
     * 监听端口使用connect与close会出现swoole异常退出
     */
//    private function tcpOnConnect()
//    {
//        $this->getTcpServer()->on('connect', function(\swoole_server $server, $fd){
//            echo 'tcp fd = '.$fd. ' connected';
//        });
//    }
//
//    private function tcpOnClose()
//    {
//        $this->getTcpServer()->on('close', function(\swoole_server $server, $fd){
//            echo 'tcp fd = '.$fd. ' closed';
//        });
//    }

    private function tcpOnReceive()
    {
        $this->getTcpServer()->on('Receive', function (\swoole_server $server, $fd, $from_id, $data) {
            $receive = json_decode(rtrim($data, $this->listen_servers[self::LISTEN_PORT_TCP]), true);
            if (class_exists($receive['obj'])) {
                $obj = new $receive['obj']();
                if ($obj instanceof AbstractTcpInstance) {
                    try {
                        $reflect = new \ReflectionClass($obj);
                        $action = $reflect->getMethod($receive['action']);
                        if ($action->isPublic()) {
                            $result = call_user_func_array([$obj, $receive['action']], $receive['params']);
                            $response = Tools::getInstance()->SEND_JSON(Status::CODE_OK, $result, Status::getReasonPhrase(Status::CODE_OK));
                            $server->send($fd, $response);
                            $server->close($fd);
                        } else {
                            $response = Tools::getInstance()->SEND_JSON(Status::CODE_INTERNAL_SERVER_ERROR, '', 'your request function is not public function');
                            $server->send($fd, $response);
                            $server->close($fd);
                        }
                    } catch (\ReflectionException $e) {
                        $response = Tools::getInstance()->SEND_JSON($e->getCode(), '', $e->getMessage());
                        $server->send($fd, $response);
                        $server->close($fd);
                    } catch (\Exception $e) {
                        $response = Tools::getInstance()->SEND_JSON($e->getCode(), '', $e->getMessage());
                        $server->send($fd, $response);
                        $server->close($fd);
                    }
                } else {
                    $response = Tools::getInstance()->SEND_JSON(Status::CODE_INTERNAL_SERVER_ERROR, '', 'your request class not instance AbstractTcpInstance');
                    $server->send($fd, $response);
                    $server->close($fd);
                }
            } else {
                $response = Tools::getInstance()->SEND_JSON(Status::CODE_INTERNAL_SERVER_ERROR, '', 'your request class not exists');
                $server->send($fd, $response);
                $server->close($fd);
            }
        });
    }

    private function udpOnPacket()
    {
        $this->getUpdServer()->on('Packet', function (\swoole_server $server, $data, $client_info) {
            $data = json_decode($data, true);
            if (class_exists($data['obj'])) {
                $obj = new $data['obj']();
                if ($obj instanceof AbstractUdpInstance) {
                    try {
                        $reflect = new \ReflectionClass($obj);
                        $action = $reflect->getMethod($data['action']);
                        if ($action->isPublic()) {
                            $result = call_user_func_array([$obj, $data['action']], $data['params']);
                            $response = Tools::getInstance()->SEND_JSON(Status::CODE_OK, $result, Status::getReasonPhrase(Status::CODE_OK));
                            $server->sendto($client_info['address'], $client_info['port'], $response);
                        } else {
                            $response = Tools::getInstance()->SEND_JSON(Status::CODE_INTERNAL_SERVER_ERROR, '', 'your request function is not public function');
                            $server->sendto($client_info['address'], $client_info['port'], $response);
                        }
                    } catch (\ReflectionException $e) {
                        $response = Tools::getInstance()->SEND_JSON($e->getCode(), '', $e->getMessage());
                        $server->sendto($client_info['address'], $client_info['port'], $response);
                    } catch (\Exception $e) {
                        $response = Tools::getInstance()->SEND_JSON($e->getCode(), '', $e->getMessage());
                        $server->sendto($client_info['address'], $client_info['port'], $response);
                    }
                } else {
                    $response = Tools::getInstance()->SEND_JSON(Status::CODE_INTERNAL_SERVER_ERROR, '', 'your request class not instance AbstractUdpInstance');
                    $server->sendto($client_info['address'], $client_info['port'], $response);
                }
            } else {
                $response = Tools::getInstance()->SEND_JSON(Status::CODE_INTERNAL_SERVER_ERROR, '', 'your request class not exists');
                $server->sendto($client_info['address'], $client_info['port'], $response);
            }
        });
    }
}