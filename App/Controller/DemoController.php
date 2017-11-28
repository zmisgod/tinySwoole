<?php
namespace App\Controller;

use App\Task\Test;
use Core\Framework\AbstractController;
use Core\Swoole\Server;
use Core\Swoole\Timer;
use Swoole\Client;

class DemoController extends AbstractController
{
    public function index()
    {
        $this->response()->write("this DemoController can show how to use swoole function in this framework, just see the code!");
    }

    /**
     * swoole_task
     */
    public function taskTest()
    {
        Server::getInstance()->getServer()->task(new Test());
    }

    /**
     * swoole_timer_tick
     * swoole_timer_clear
     */
    public function tickTest()
    {
        $i = 0;
        Timer::tick(1000, function($timer_id, $array) use (&$i){
            $res = $array[0].$array[1];
            echo $array[0].'+'.$array[1] .'='.$res.PHP_EOL;
            echo $i.PHP_EOL;
            $i++;
            if($i > 10 ) {
                Timer::clear($timer_id);
            }
        }, [1, 2]);
    }

    /**
     * swoole_timer_after
     */
    public function afterTest()
    {
        $i = 0;
        Timer::after(1000, function($array) use (&$i){
            $res = $array[0].$array[1];
            echo $array[0].'+'.$array[1] .'='.$res.PHP_EOL;
            echo $i.PHP_EOL;
            $i++;
        }, [1, 2]);
    }

    /**
     * tcp client
     */
    public function tcpClient()
    {
        $client = new \swoole_client(SWOOLE_SOCK_TCP);
        $client->connect('127.0.0.1', 9520);
        $client->send('this is demo controller tcpClient method'."\r\n");
        $res = $client->recv();
        echo 'client receive :' .$res;
    }

    /**
     * upd client
     */
    public function udpClient()
    {
        $client = new \swoole_client(SWOOLE_SOCK_UDP);
        $client->connect('127.0.0.1', 9521);
        $client->send('this is demo controller udpClient method');
        $res = $client->recv();
        echo 'client receive :' .$res.PHP_EOL;
    }
}