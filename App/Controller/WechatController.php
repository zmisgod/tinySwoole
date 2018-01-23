<?php
namespace App\Controller;

use App\Components\Wechat\WechatAbstract;
use App\Components\Wechat\WechatMessageType\WechatEvent;
use App\Components\Wechat\WechatMessageType\WechatException;
use App\Components\Wechat\WechatMessageType\WechatProperty;
use App\Components\Wechat\WechatRequest;
use Core\Uti\Tools\Config;
use EasyWeChat\Factory;

class WechatController extends WechatAbstract
{
    public function index()
    {
        $message['ToUserName']   = 'ToUserName';
        $message['FromUserName'] = 'FromUserName';
        $message['CreateTime']   = 'CreateTime';
        $message['MsgId']        = 'MsgId';
        $message['Content'] = '你好';

        try{
            $obj = new WechatEvent();
            $obj->setData($message);
            $res = $obj->setType('text')->run('defaultResponse');
            $this->response()->setHeader('Content-Type', 'text/html; charset=utf-8')->write($res);
        }catch(WechatException $e ) {
            $this->response()->write($e);
        }catch(\ReflectionException $e) {
            $this->response()->write($e);
        }catch(\Exception $e) {
            $this->response()->write($e);
        }
    }

    public function server()
    {
        if(isset($_GET['echostr'])){
            $this->response()->write($_GET['echostr']);
        }else {
            $server = $this->app()->server;
            $this->app()->request = new WechatRequest();
            $server->push(function($message) {
                if(in_array($message['MsgType'], ['event', 'text', 'image', 'voice', 'video', 'location', 'link'])) {
                    $className = 'Wechat'.ucfirst($message['MsgType']);
                }else{
                    $className = 'WechatOthers';
                }
                $className .= 'App\Components\Wechat\WechatMessageType\\'.$className;
                $obj = new $className;
                $obj->setReceive($message);
            });
            $response = $server->serve();
            $this->response()->write($response->getContent());
        }
    }
}