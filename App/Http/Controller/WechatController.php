<?php
namespace App\Http\Controller;

use App\Components\Wechat\WechatAbstract;
use App\Components\Wechat\WechatMessageType\WechatEvent;
use App\Components\Wechat\WechatMessageType\WechatException;
use App\Components\Wechat\WechatRequest;

class WechatController extends WechatAbstract
{
    public function index()
    {
        $message['ToUserName']   = 'ToUserName';
        $message['FromUserName'] = 'FromUserName';
        $message['CreateTime']   = 'CreateTime';
        $message['MsgId']        = 'MsgId';
        $message['MsgType'] = 'text';
        $message['Content'] = '我爱你';
        try{
            $obj = new WechatEvent();
            $res = $obj->setMessage($message)->setType($message['MsgType'])->run('defaultResponse');
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
                try{
                    $obj = new WechatEvent();
                    return $obj->setMessage($message)->setType($message['MsgType'])->run('defaultResponse');
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