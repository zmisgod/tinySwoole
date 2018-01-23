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


    }

    public function server()
    {
        if(isset($_GET['echostr'])){
            $this->response()->write($_GET['echostr']);
        }else {
            $server = $this->app()->server;
            $this->app()->request = new WechatRequest();
            $server->push(function($message) {
                try{
                    $obj = new WechatEvent();
                    $obj->setData($message);
                    return $obj->setType($message['MsgType'])->run('defaultResponse');
                }catch(WechatException $e ) {
                    return $e;
                }catch(\ReflectionException $e) {
                    return $e;
                }catch(\Exception $e) {
                    return $e;
                }
            });
            $response = $server->serve();
            $this->response()->setHeader('Content-Type', 'text/html; charset=utf-8')->write($response->getContent());
        }
    }
}