<?php
namespace App\Components\Wechat\WechatMessageType;

class WechatEvent extends AbstractEvent
{
    protected $event;

    public function defaultResponse()
    {
        return $this->event;
    }

    function setData($message)
    {
        $this->event = $message['Event'];
        parent::setData($message);
    }
}