<?php
namespace App\Controller;

use App\Task\Test;
use App\Tcp\TcpDemo;
use Core\Framework\AbstractController;
use Core\Swoole\Server;
use Core\Swoole\Timer;
use Swoole\Client;

class DemoController extends AbstractController
{
    public function index()
    {
        $this->response()->writeJson(200, "this DemoController can show how to use swoole function in this framework, just see the code!");
    }

    /**
     * swoole_task
     *
     * http://127.0.0.1:9519/demo/taskTest
     */
    public function taskTest()
    {
        Server::getInstance()->getServer()->task(new Test());
    }

    /**
     * swoole_timer_tick
     * swoole_timer_clear
     *
     * http://127.0.0.1:9519/demo/tickTest
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
     *
     * http://127.0.0.1:9519/demo/afterTest
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
     *
     * http://127.0.0.1:9519/demo/tcpClient
     */
    public function tcpClient()
    {
        $client = new \swoole_client(SWOOLE_SOCK_TCP);
        $client->connect('127.0.0.1', 9520);
        $data = [
            'obj' => 'App\Tcp\TcpDemo',
            'action' => 'drink',
            'params' => ['zmisgod', 'coca']
        ];
        $client->send(json_encode($data)."\r\n");
        $res = json_decode($client->recv(), true);
        $this->response()->writeJson($res['code'], $res['result'], $res['msg']);
    }

    /**
     * upd client
     *
     * http://127.0.0.1:9519/demo/udpClient?name=i%20am%20a%20star
     */
    public function udpClient()
    {
        $get = $this->request()->parseGet();
        if(!isset($get['name'])) {
            $msg = 'i am zmisgod';
        }else{
            $msg = $get['name'];
        }
        $client = new \swoole_client(SWOOLE_SOCK_UDP);
        $client->connect('127.0.0.1', 9521);
        $data = [
            'obj' => 'App\Udp\UdpDemo',
            'action' => 'youSayWhatIReplayWhat',
            'params' => [$msg]
        ];
        $client->send(json_encode($data)."\r\n");
        $res = json_decode($client->recv(), true);
        $this->response()->writeJson($res['code'], $res['result'], $res['msg']);
    }
}