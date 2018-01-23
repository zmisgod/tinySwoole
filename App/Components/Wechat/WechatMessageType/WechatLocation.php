<?php
namespace App\Components\Wechat\WechatMessageType;

class WechatLocation extends AbstractEvent
{
    protected $location_x;

    protected $location_y;

    protected $scale;

    protected $label;

    public function setData($message)
    {
        $this->location_x = $message['Location_X'];
        $this->location_y = $message['Location_Y'];
        $this->scale = $message['Scale'];
        $this->label = $message['Label'];
        parent::setData($message);
    }

    public function defaultResponse()
    {

    }
}