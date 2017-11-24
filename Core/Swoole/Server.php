<?php
namespace Core\Swoole;

use Core\Framework\Request;

class Server
{
    protected static $instance;

    protected $server;

    public $server_config = [
        'host' => '127.0.0.1',
        'port' => 9519,
        'setting' => [
            'task_worker_num' => 2, //异步任务进程
            "task_max_request" => 10,
            'max_request' => 1000,//强烈建议设置此配置项
            'worker_num' => 2,
            "log_file" => __DIR__ . "/swoole.log",
            'pid_file' => __DIR__ . "/pid.pid",
        ]
    ];

    function __construct()
    {
        $this->server = new \swoole_http_server($this->server_config['host'], $this->server_config['port']);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Server();
        }
        return self::$instance;
    }

    public function startServer()
    {
        $this->server->set($this->server_config['setting']);
        $this->serverStart();
        $this->workerStart();
        $this->onRequest();
        $this->onTask();
        $this->onTaskFinish();
        $this->workerStop();
        $this->workerError();
        //server start
        $this->getServer()->start();
    }

    /**
     * 重点
     */
    private function onRequest()
    {
        $this->getServer()->on('request', function(\swoole_http_request $swoole_request, \swoole_http_response $swoole_response){
            $userRequest = new Request($swoole_request);

        });
    }

    public function getServer()
    {
        return $this->server;
    }

    private function serverStart(){
        $this->getServer()->on("start",function (\swoole_http_server $server){
            echo 'server start';
        });
    }

    private function workerStart(){
        $this->getServer()->on("WorkerStart",function (\swoole_server $server, $workerId){
            echo 'Worker start';
        });
    }

    private function workerStop(){
        $this->getServer()->on("WorkerStop",function (\swoole_server $server, $workerId){
            echo 'Worker stop';
        });
    }

    private function onTask()
    {
        $this->getServer()->on("task",function (\swoole_http_server $server, $taskId, $workerId,$data){
            echo 'start task';
        });
    }

    private function onTaskFinish()
    {
        $this->getServer()->on("finish",function (\swoole_http_server $server, $taskId, $taskObj){
            echo 'task finish';
        });
    }

    private function workerError()
    {
        $this->getServer()->on("workerError",function (\swoole_http_server $server,$worker_id, $worker_pid, $exit_code){
            echo 'worker Error';
        });
    }
}

$res = new Server();
$res->startServer();