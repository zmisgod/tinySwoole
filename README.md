# TinySwoole

这是一个很简单的基于swoole的http框架，这也是我边看easyswoole框架边写的框架，主要实现了基础的`swoole_http_server`的功能。就是为了使用者了解如何swoole如何工作以及与常见的fpm的区别。

### 使用

##### 1.clone代码后直接在cli中执行php index.php，默认端口绑定在`9519`端口上。
##### 2.swoole的配置在Core\Swoole\Server.php中。

### Tips
###### 提示端口已被使用，请使用`lsof -i:你的端口`，如果有信息，请`kill -9 PID` 
###### 停止运行直接使用 `ctrl + c`
###### swoole文档默认是`9501`，为什么是`9501`呢，答案是：九五至尊`95`+`01`（01就不用解释了吧）。

