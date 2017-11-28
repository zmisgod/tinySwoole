<?php
return [
    'server' => [
        'host' => '127.0.0.1',
        'port' => 9519,
        'server_type' => \Core\Swoole\Server::SERVER_TYPE_WEB,
        'socket_type' => SWOOLE_TCP,
        "mode" => SWOOLE_PROCESS,//不建议更改此项
        'setting' => [
            'open_http_protocol' => true,
            'task_worker_num' => 1, //异步任务进程
            "task_max_request" => 10,
            'max_request' => 10000,//强烈建议设置此配置项
            'worker_num' => 8,
            'log_file' => ROOT . "/Log/Server/swoole.log",
            'pid_file' => ROOT . "/Log/Server/pid.pid",
        ],
        //是否开启多端口监听
        'multi_port' => true,
        'multi_port_settings' => [
            [
                'open' => true,//是否开启tcp
                'type' => \Core\Swoole\Server::LISTEN_PORT_TCP, //端口类型
                'port' => 9520,
                'socket_type' => SWOOLE_SOCK_TCP,
                'setting' => [
                    'open_eof_split' => true,
                    'package_eof' => "\r\n",
                ]
            ],
            [
                'open' => false,//是否开启udp
                'type' => \Core\Swoole\Server::LISTEN_PORT_UDP,//端口类型
                'port' => 9521,
                'socket_type' => SWOOLE_SOCK_UDP,
                'setting' => [
                    'open_length_check' => true,
                    'package_length_type' => 'N',
                    'package_length_offset' => 0,
                    'package_max_length' => 800000
                ]
            ]
        ]
    ]
];