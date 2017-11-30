<?php
namespace App\Udp;

use Core\Framework\AbstractUdpInstance;

class UdpDemo extends AbstractUdpInstance
{
    function index()
    {
        // TODO: Implement index() method.
    }

    function youSayWhatIReplayWhat($msg)
    {
        return str_ireplace('i am', 'you are', $msg);
    }
}