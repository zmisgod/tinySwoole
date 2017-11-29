<?php
namespace App\Tcp;

use Core\Framework\AbstractTcpInstance;

class TcpDemo extends AbstractTcpInstance
{
    public function index()
    {
        echo 'default action';
    }

    public function say($who, $say_what)
    {
        return $who.' said : '.$say_what;
    }

    public function drink($who, $drink_what)
    {
        return $who.' is drinking '.$drink_what;
    }
}