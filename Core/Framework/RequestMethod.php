<?php

namespace Core\Framework;

use Core\Framework\Request\GetRequest;
use Core\IO\Stream;

class RequestMethod extends HttpBase
{
    private $serverParams;
    private $uri;
    private $method;
    /**
     * @var GetRequest
     */
    public $input;

    public function __construct(
        $method = 'GET',Uri $uri = null,array $headers = null,Stream $body = null,$protocolVersion = '1.1')
    {
        if($uri !== null) {
            $this->uri = $uri;
        }
        $this->method = $method;
        parent::__construct($headers,$body,$protocolVersion);
    }

    public function setRequestData(array $get = [],array $post = [],array $cookies = [],array $server = [],array $uploadFile = [])
    {
        $this->input = new GetRequest($get,$post,$cookies,$server,$uploadFile);
        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getServerParams()
    {
        return $this->serverParams;
    }

    public function getServerParam($name = null)
    {
        if($name === null) {
            return $this->serverParams;
        } else {
            return isset($this->serverParams[$name]) ? $this->serverParams[$name] : false;
        }
    }
}