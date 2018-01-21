<?php
namespace App\Components\Wechat;


use Symfony\Component\HttpFoundation\Request;

class WechatRequest extends Request
{
    public function getContent($asResource = false)
    {
        parent::getContent();
        if (null == $this->content || false === $this->content) {
            $this->content = \Core\Framework\Request::getInstance()->getBody()->__toString();
        }
        return $this->content;
    }
}