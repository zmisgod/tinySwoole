<?php
namespace Core\Framework;

class ResponseHeader extends BaseRequest
{
    private $statusCode = 200;

    private $statusMsg = 'OK';

    function getStatusCode()
    {
        return $this->statusCode;
    }

    function getResponseMsg()
    {
        return $this->statusMsg;
    }

    function setStatus($code, $statusMsg = '')
    {
        if($code === $this->statusCode) {
            return $this;
        }else{
            $this->statusCode = $code;
            if(empty($statusMsg)) {
                $this->statusMsg = Status::getReasonPhrase($code);
            }else{
                $this->statusMsg = $statusMsg;
            }
            return $this;
        }
    }
}