<?php
namespace App\Components\Wechat;

use Core\Framework\AbstractController;

abstract class WechatAbstract extends AbstractController
{
    /**
     * @var \EasyWeChat\OfficialAccount\Application
     */
    public $app;

    public function beforeAction()
    {
        $_GET = isset($this->request()->getServerRequest()->get) ? $this->request()->getServerRequest()->get : [];
        $_POST = isset($this->request()->getServerRequest()->post) ? $this->request()->getServerRequest()->post : [];
        $_COOKIE = isset($this->request()->getServerRequest()->cookie) ? $this->request()->getServerRequest()->cookie : [];
        $server = $this->request()->getServerRequest()->server;
        if($server) {
            foreach($server as $key => $value) {
                $_SERVER[strtoupper($key)] = $value;
            }
        }
    }

    /**
     * @return \EasyWeChat\OfficialAccount\Application
     */
    public function app()
    {
        return $this->app = Wechat::getInstance()->getApplication();
    }

    public function afterAction()
    {
        unset($_GET);
        unset($_POST);
        unset($_COOKIE);
        unset($_SERVER);
    }
}