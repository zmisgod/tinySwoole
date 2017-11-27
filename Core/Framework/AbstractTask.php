<?php
namespace Core\Framework;

abstract class AbstractTask
{
    abstract function handleTask(\swoole_server $server, $task_id, $from_worker_id);

    abstract function finishTask(\swoole_server $server, $task_id, $dataForFinishCallBack);
}