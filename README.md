# TinySwoole

这是一个很简单的基于swoole的http框架，这也是我边看easyswoole框架边写的框架，主要实现了基础的`swoole_http_server`的功能。就是为了让使用者了解如何使用swoole、学习如何与swoole结合框架使用以及与swoole编程与之前的fpm编程的区别。

## 使用

clone代码后直接在cli中执行php index.php，默认端口绑定在`9519`端口上。

### 路由 

###### `http://127.0.0.1:9519/index/benchmark`
在`App\Controller\IndexController`中的`benchmark()`，并且需要此公开的方法（public function）并且class需要继承`Core\Framework\AbstractController`


### Swoole相关内置函数使用

详情见`App\Controller\DemoController`这个类，包括
~ swoole_task
~ swoole_timer_tick
~ swoole_timer_clear
~ swoole_timer_after

### 配置文件

配置文件在Config文件夹中。获取配置文件示例：
###### `$serverConfig = Config::getInstance()->getConfig('config.server');`
其中，config为Config下的config.php文件

### Tips

提示端口已被使用，请使用`lsof -i:你的端口`，如果有信息，请`kill -9 PID` 
停止运行直接使用 `ctrl + c`

### Nginx配置域名

```
server {
    listen       80;
    server_name your.server.name;
    root to/your/path/TinySwoole/Public/;
    location / {
        proxy_http_version 1.1;
        proxy_set_header Connection "keep-alive";
        proxy_set_header X-Real-IP $remote_addr;
        # 判断文件是否存在，如果不存在，代理给Swoole
        if (!-e $request_filename){
            proxy_pass http://127.0.0.1:9519;
        }
        if ( $request_uri = '/') {
            proxy_pass http://127.0.0.1:9519;
        } 
    }
}
```
### 静态文件

静态文件在Public目录下

### 性能

机器: CPU: i5, RAM: 8G, OS: maxOS Sierra 10.12.6

###### 报告
![image](https://github.com/zmisgod/TinySwoole/blob/master/Public/github_readme_pic/v2.png)

###### 历史性能报告截图在`/Public/github_readme_pic`下，可以查看下每次更新性能提高多少，也可以见正我对框架做的努力。


### 关于swoole

###### <a href="https://wiki.swoole.com/">Swoole文档</a>
swoole默认端口是`9501`，为什么是`9501`呢，答案是：九五至尊`95`+`01`（01就不用解释了吧）。

### 关于我

<a href="https://zmis.me/">zmis.me新博客</a>