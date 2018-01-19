<?php
namespace Core\Framework;


use Core\Framework\Request\GetRequest;
use Core\IO\Stream;

class HttpBase
{
    private $protocolVersion = '1.1';
    private $headers = [];
    private $body;

    function __construct(array $headers = null, Stream $body = null, $protocolVersion = '1.1')
    {
        if($headers != null) {
            $this->headers = $headers;
        }

        if($body != null) {
            $this->body = $body;
        }

        $this->protocolVersion = $protocolVersion;
    }

    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    public function setProtocolVersion($protocolVersion)
    {
        if($this->protocolVersion === $protocolVersion){
            return $this;
        }

        $this->protocolVersion = $protocolVersion;
        return $this;
    }

    function getHeader($name)
    {
        if(isset($this->headers[$name])) {
            return $this->headers[$name];
        }
        return [];
    }

    function checkHeaderExists($name)
    {
        return isset($this->headers[$name]) ? true : false;
    }

    function appendHeader($name, $value)
    {
        if(!is_array($value)) {
            $value = [$value];
        }
        if(isset($this->headers[$name])) {
            $this->headers[$name] = array_merge($this->headers[$name], $value);
        }else{
            $this->headers[$name] = $value;
        }
        return $this;
    }

    function setHeader($name, $value)
    {
        if(!is_array($value)) {
            $value = [$value];
        }
        if(isset($this->headers[$name]) && $this->headers[$name] === $value) {
            return $this;
        }
        $this->headers[$name] = $value;
        return $this;
    }

    function getHeaders()
    {
        return $this->headers;
    }

    function unsetHeader($name)
    {
        if(isset($this->headers[$name])){
            unset($this->headers[$name]);
        }
        return $this;
    }

    public function withoutHeader($name)
    {
        if(isset($this->headers[$name])){
            unset($this->headers[$name]);
            return $this;
        }else{
            return $this;
        }
    }

    public function getBody()
    {
        if($this->body == null){
            $this->body = new Stream('');
        }
        return $this->body;
    }

    public function setBody(Stream $body)
    {
        $this->body = $body;
        return $this;
    }
}