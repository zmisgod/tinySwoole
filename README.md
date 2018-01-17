# TinySwoole

这是一个很简单的基于swoole的http框架，主要实现了基础的`swoole_http_server`的功能以及监听`TCP`、`UDP`端口。
为了让使用者了解如何使用swoole、学习如何与swoole结合框架使用以及与swoole编程与之前的fpm编程的区别。

框架的结构很简单，核心文件在`Core`文件夹下。
- Framework 框架的核心文件，包括处理 Http 相关请求类，基础类等等
- Swoole swoole事件触发后对应的处理
- IO 处理IO
- Util 一些常用的工具 包括: mysql

## 相关命令

`php index.php start` 启动
 
`php index.php stop` 关闭

`php index.php reload` 重启

`php index.php status` 查看服务器状态

`php index.php --help` 显示帮助命令

## 使用

如果想要使用多端口监听`tcp`、`udp`，需要在配置文件中将`multi_port`设置为`true`,并在`tcp`或者`udp`的`open`选项中设置为`true`开启。<br />
default config

|server|port|open|
|-|-|-|
|http|9519|true|
|tcp|9520|true|
|udp|9521|true|

### Web路由 

|路由|对应文件|方法名|
|-|-|-|
|`http://127.0.0.1:9519/index/benchmark`|`App\Controller\IndexController.php`|`benchmark()`|

其中，并且类文件的方法需要为公开的方法（public function）并且类需要继承`Core\Framework\AbstractController`

### TCP

如果需要使用`tcp`服务，需要定义一个类，此类需要继承`Core\Framework\AbstractTcpInstance`抽象类,tcp client需要发送三个参数的json encode的字符串
```
[
 'obj' => 'App\Tcp\YourClassName',
 'action' => 'functionName',
 'params' => []
]
```
详情请见`App\Controller\DemoController`的`tcpClient`方法

### Mysql

内置mysqli,并实现相应的断线重连。使用方法：
```
use Core\Uti\DB\Mysqli;

Mysqli::getInstance()->query('show tables')->fetchall();

# debug
Mysqli::getInstance()->setDebug(true)->query('show tables')->printDebug();
```

### Swoole相关内置函数使用

详情见`App\Controller\DemoController`这个类，demo包括下列方法的使用
- swoole_task
- swoole_timer_tick
- swoole_timer_clear
- swoole_timer_after
- tcp_client
- udp_client
- mysqli
- get/post参数接收

### 配置文件

配置文件在Config文件夹中。获取配置文件示例：<br />
`$serverConfig = Config::getInstance()->getConfig('config.server');`<br />
其中，config为Config下的config.php文件

### 静态文件

静态文件在Public目录下（暂时需要配合nginx处理静态资源）

### Nginx配置域名

```
server {
    listen       80;
    server_name your.server.name;
    root to/your/path/TinySwoole/Public/;
    
    if ( $uri = "/" ) {
       rewrite ^(.*)$ /index last;
    }
    
    location / {
        proxy_http_version 1.1;
        proxy_set_header Connection "keep-alive";
        proxy_set_header X-Real-IP $remote_addr;
        # 判断文件是否存在，如果不存在，代理给Swoole
        if (!-e $request_filename){
            proxy_pass http://127.0.0.1:9519;
        }
    }
}
```

### 性能

机器: CPU: i5, RAM: 8G, OS: maxOS Sierra 10.12.6

性能报告 <br />
![image](https://github.com/zmisgod/TinySwoole/blob/master/Public/github_readme_pic/v3.png)

<br />
历史性能报告截图在`/Public/github_readme_pic`下，可以查看下每次更新性能提高多少，也可以见正我对框架做的努力。

### 关于swoole

<a href="https://wiki.swoole.com/">Swoole文档</a> <br />
swoole默认端口是`9501`，为什么是`9501`呢，答案是：九五至尊`95`+`01`（01就不用解释了吧）。

### 关于我

<a href="https://zmis.me/">zmis.me新博客</a><br />
<a href="https://weibo.com/zmisgod">@zmisgod</a>
