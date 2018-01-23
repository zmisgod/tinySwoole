<?php

namespace App\Components\Wechat\WechatMessageType;

abstract class AbstractEvent
{
    //OPEN ID
    protected $from_user_name;
    protected $to_user_name;
    protected $create_time;
    protected $msg_id;

    protected $message;
    //当前的type
    protected $msg_type;
    private $defaultType = 'text';
    private $_valid_type = [
        'event',
        'image',
        'link',
        'location',
        'text',
        'video',
        'voice',
    ];

    function setDefaultType($type)
    {
        $this->defaultType = $type;
        return $this;
    }

    function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    function setType($type)
    {
        if(in_array($type,$this->_valid_type)) {
            $this->msg_type = $type;
        } else {
            $this->msg_type = $this->defaultType;
        }
        return $this;
    }

    function run(string $method,array $params = [])
    {
        $className = 'App\Components\Wechat\WechatMessageType\Wechat' . ucfirst($this->msg_type);
        if(class_exists($className)) {
            $reflect    = new \ReflectionClass($className);
            $methodFunc = $reflect->getMethod($method);
            if($methodFunc->isPublic()) {
                $result = $reflect->newInstance();
                $result->setData($this->message);
                return call_user_func_array([$result, $method], $params);
            } else {
                throw new WechatException('method :' . $method . ' in your class :' . $className . ' is not a public method');
            }
        } else {
            throw new WechatException('do not find class :' . $className);
        }
    }

    function setData($message)
    {
        $this->to_user_name   = $message['ToUserName'];
        $this->from_user_name = $message['FromUserName'];
        $this->create_time    = $message['CreateTime'];
        $this->msg_id         = $message['MsgId'];
        $this->msg_type       = $message['MsgType'];
    }

    abstract function defaultResponse();
}