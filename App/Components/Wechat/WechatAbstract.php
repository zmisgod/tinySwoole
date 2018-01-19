<?php
namespace App\Components\Wechat;

use Core\Framework\AbstractController;

abstract class WechatAbstract extends AbstractController
{
    public function __construct()
    {
        $_GET = $this->request()->getServerRequest()->get;
        $_POST = $this->request()->getServerRequest()->post;
        $_COOKIE = $this->request()->getServerRequest()->cookie;
        $_SERVER = $this->request()->getServerRequest()->server;
    }

    public function destroy()
    {
        unset($_GET);
        unset($_POST);
        unset($_COOKIE);
        unset($_SERVER);
    }
}