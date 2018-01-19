<?php
namespace Core\Framework\Request;

class Cookie
{
    private $name;
    private $value;
    private $expire = 0;
    private $path = '/';
    private $domain = '';
    private $secure = false;
    private $httpOnly = false;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getExpire()
    {
        return $this->expire;
    }

    public function setExpire($expire)
    {
        $this->expire = $expire;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    public function getSecure()
    {
        return $this->secure;
    }

    public function setSecure($secure)
    {
        $this->secure = $secure;
    }

    public function getHttpOnly()
    {
        return $this->httpOnly;
    }

    public function setHttpOnly($httpOnly)
    {
        $this->httpOnly = $httpOnly;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return "{$this->name}={$this->value};";
    }
}