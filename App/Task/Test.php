<?php
namespace App\Task;

use Core\Framework\AbstractTask;

class Test extends AbstractTask
{
    public function handleTask(\swoole_server $server, $task_id, $from_worker_id)
    {
        for ($i = 0; $i< 10; $i++) {
            echo $i;
        }
        return $this;
    }

    public function finishTask(\swoole_server $server, $task_id, $dataForFinishCallBack)
    {
        echo 'finish';
    }
}