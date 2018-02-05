<?php
namespace Core\Framework;

use Core\Swoole\Server;
use Core\Uti\DB\Mysqli;
use Core\Uti\Tools\Config;

abstract class AbstractTask
{
    /**
     * task实现思路
     * 1。在server中设置onTask, onFinish事件
     * 2。定义一个Task抽象类，继承此抽象类都要实现两个方法，处理task任务的方法，处理完成后的回调函数
     * 3。每个task类需要继承上面的Task抽象类，
     * 其中处理task类函数需要有finish回调函数需要使用抽象类中的finish方法，
     * 将设置好的需要在$this传递到finish事件中
     */


    private $dataForTask;
    private $dataForFinsihCallBack;

    function __construct($dataForTask = null)
    {
        $this->dataForTask = $dataForTask;
    }

    /**
     * @return mixed
     */
    function mysqli()
    {
        try{
            return Mysqli::getInstance(Config::getInstance()->getConfig('config.mysqli'));
        }catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    function getDataForTask()
    {
        return $this->dataForTask;
    }

    function getDataForFinishCallBack()
    {
        return $this->dataForFinsihCallBack;
    }

    abstract function handleTask(\swoole_server $server, $task_id, $from_worker_id);

    abstract function finishTask(\swoole_server $server, $task_id, $dataForFinishCallBack);

    protected function finish($dataForFinishCallBack = null)
    {
        if($dataForFinishCallBack !== null) {
            $this->dataForFinsihCallBack = $dataForFinishCallBack;
        }
        Server::getInstance()->getServer()->finish($this);
    }
}