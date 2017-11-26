<?php
return [
    'server' => [
        'host' => '127.0.0.1',
        'port' => 9519,
        'setting' => [
            'task_worker_num' => 2, //异步任务进程
            "task_max_request" => 10,
            'max_request' => 10000,//强烈建议设置此配置项
            'worker_num' => 8,
            'log_file' => ROOT . "/Log/Server/swoole.log",
            'pid_file' => ROOT . "/Log/Server/pid.pid",
        ]
    ]
];