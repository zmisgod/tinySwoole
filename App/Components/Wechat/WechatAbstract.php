<?php
namespace App\Components\Wechat;

use Core\Framework\AbstractController;

abstract class WechatAbstract extends AbstractController
{
    public function beforeAction()
    {
        $_GET = $this->request()->getServerRequest()->get;
        $_POST = $this->request()->getServerRequest()->post;
        $_COOKIE = $this->request()->getServerRequest()->cookie;
        $server = $this->request()->getServerRequest()->server;
        if($server) {
            foreach($server as $key => $value) {
                $_SERVER[strtoupper($key)] = $value;
            }
        }
    }

    public function afterAction()
    {
        unset($_GET);
        unset($_POST);
        unset($_COOKIE);
        unset($_SERVER);
    }
}