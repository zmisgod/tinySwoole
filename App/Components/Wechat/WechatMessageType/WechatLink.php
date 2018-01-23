<?php
namespace App\Components\Wechat\WechatMessageType;

class WechatLink extends AbstractEvent
{
    protected $title;

    protected $description;

    protected $url;

    public function setData($message)
    {
        $this->title = $message['Title'];
        $this->description = $message['Description'];
        $this->url = $message['Url'];
        parent::setData($message);
    }

    public function defaultResponse()
    {

    }
}