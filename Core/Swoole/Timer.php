<?php
namespace Core\Swoole;

class Timer
{
    /**
     * 增加tick定时器
     *
     * @param $microSeconds
     * @param \Closure $func
     * @param null $args
     * @return int
     */
    static function tick($microSeconds, \Closure $func, $args = null)
    {
        return Server::getInstance()->getServer()->tick($microSeconds, $func, $args);
    }

    /**
     * 在指定的时间后执行函数
     *
     * @param $microSeconds
     * @param \Closure $func
     * @param null $args
     */
    static function after($microSeconds, \Closure $func, $args = null)
    {
        Server::getInstance()->getServer()->after($microSeconds, $func, $args);
    }

    static function clear($timer_id)
    {
        Server::getInstance()->getServer()->clearTimer($timer_id);
    }
}