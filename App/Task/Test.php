<?php
namespace App\Task;

use Core\Framework\AbstractTask;

/**
 * task demo
 */
class Test extends AbstractTask
{
    public function handleTask(\swoole_server $server, $task_id, $from_worker_id)
    {
        echo 'start task'.PHP_EOL;
        $this->finish('this is task demo');
    }

    public function finishTask(\swoole_server $server, $task_id, $dataForFinishCallBackData)
    {
        echo $dataForFinishCallBackData.PHP_EOL;
        echo 'task finish'.PHP_EOL;
    }
}