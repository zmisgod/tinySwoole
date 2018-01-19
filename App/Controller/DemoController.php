<?php
namespace App\Controller;

use App\Task\Test;
use Core\Framework\AbstractController;
use Core\Swoole\Server;
use Core\Swoole\Timer;

class DemoController extends AbstractController
{
    public function index()
    {
        $this->response()->writeJson(200, "2this DemoController can show how to use swoole function in this framework, just see the code!");
    }

    /**
     * 执行sql
     */
    public function doMysql()
    {
        $res = $this->mysqli()->query('show tables')->fetchall();
        $this->response()->writeJson(200, $res, 'ok');
    }

    /**
     * 调试sql
     */
    public function debugMysql()
    {
        $debug = $this->mysqli()->setDebug(true)->query('show tables')->printDebug();
        $this->response()->writeJson(200, $debug, 'ok');
    }

    /**
     * get传参
     *
     * 支持
     * getParam('name', 'this is default value', 'trim') // trim($_GET['name'])
     * getParams(['*']) //get all params
     */
    public function getMethod()
    {
        $data = $this->request()->input->get->getParam('name');
        $this->response()->writeJson(200, $data, 'ok');
    }

    /**
     * post传参
     *
     * content/type = form-data or x-www-form-urlencoded
     */
    public function postMethod()
    {
        $data = $this->request()->input->post->getParams(['get']);
        $this->response()->writeJson(200, $data, 'ok');
    }

    /**
     * post传递过来的content/type = application/json
     */
    public function postJson()
    {
        $data = $this->request()->input->post->setContentType('application/json')->getParams(['*']);
        $this->response()->writeJson(200, $data, 'ok');
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
        $name = $this->request()->input->get->getParam('name');
        if(!$name) {
            $msg = 'i am zmisgod';
        }else{
            $msg = $name;
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