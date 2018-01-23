<?php
namespace App\Components\Wechat\WechatMessageType;

class WechatImage extends AbstractEvent
{
    protected $media_id;

    protected $pic_url;

    function setData($message)
    {
        $this->media_id = $message['MediaId'];
        $this->pic_url = $message['PicUrl'];
        parent::setData($message);
    }

    public function defaultResponse()
    {

    }
}