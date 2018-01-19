<?php
return [
    'mysqli' => [
        'host' => '127.0.0.1',
        'user' => 'root',
        'password' => '111111',
        'port' => 3306,
        'charset' => 'utf8',
        'database' => 'mytest'
    ],
    'wechat' => [
        'app_id'    => 'wx187323b17af46795',
        'secret'    => 'e313b9b27058a1721033256779b95230',
        'token'     => 'zmisgod',
        'log' => [
            'level' => 'debug',
            'file'  => ROOT . '/Log/easywechat.log',
        ],
    ],
    'server' => [
        'host' => '127.0.0.1',
        'port' => 9519,
        'server_type' => \Core\Swoole\Server::SERVER_TYPE_WEB,
        'socket_type' => SWOOLE_SOCK_TCP,
        "mode" => SWOOLE_PROCESS,//不建议更改此项
        'setting' => [
            'daemonize' => true,
            'open_http_protocol' => true,
            'task_worker_num' => 1, //异步任务进程
            "task_max_request" => 10,
            'max_request' => 10000,//强烈建议设置此配置项
            'worker_num' => 4,
            'log_file' => ROOT . "/Log/Server/swoole.log",
            'pid_file' => ROOT . "/Log/Server/pid.pid",
        ],
        //是否开启多端口监听
        'multi_port' => true,
        'multi_port_settings' => [
            'tcp' => [
                'open' => true,//是否开启tcp
                'type' => \Core\Swoole\Server::LISTEN_PORT_TCP, //端口类型
                'port' => 9520,
                'socket_type' => SWOOLE_SOCK_TCP,
                'setting' => [
                    'open_eof_split' => true,
                    'package_eof' => "\r\n",
                ]
            ],
            'udp' => [
                'open' => true,//是否开启udp
                'type' => \Core\Swoole\Server::LISTEN_PORT_UDP,//端口类型
                'port' => 9521,
                'socket_type' => SWOOLE_SOCK_UDP,
                'setting' => [
                    'open_eof_split' => true,
                    'package_eof' => "\r\n",
                ]
            ]
        ]
    ]
];