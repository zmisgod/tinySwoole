<?php
namespace App\Controller;

use App\Components\Wechat\WechatAbstract;
use App\Components\Wechat\WechatRequest;
use Core\Uti\Tools\Config;
use EasyWeChat\Factory;

class WechatController extends WechatAbstract
{
    public function index()
    {
        $get = $this->request()->input->get->getParam('name');
        $this->response()->write([$get]);
    }

    public function server()
    {
        if(isset($_GET['echostr'])){
            $this->response()->write($_GET['echostr']);
        }else {
            $server = $this->app()->server;
            $this->app()->request = new WechatRequest();
            $server->push(function($message) {
                switch ($message['MsgType']) {
                    case 'event':
                        return '收到事件消息';
                        break;
                    case 'text':
                        return '收到文字消息';
                        break;
                    case 'image':
                        return '收到图片消息';
                        break;
                    case 'voice':
                        return '收到语音消息';
                        break;
                    case 'video':
                        return '收到视频消息';
                        break;
                    case 'location':
                        return '收到坐标消息';
                        break;
                    case 'link':
                        return '收到链接消息';
                        break;
                    // ... 其它消息
                    default:
                        return '收到其它消息';
                        break;
                }
            });
            $response = $server->serve();
            $this->response()->write($response->getContent());
        }
    }
}