<?php
namespace Core\Framework;

class Header
{
    public $header;

    function getHeader($name)
    {
        if(isset($this->header[$name])) {
            return $this->header[$name];
        }
        return [];
    }

    function checkHeaderExists($name)
    {
        return isset($this->header[$name]) ? true : false;
    }

    function appendHeader($name, $value)
    {
        if(!is_array($value)) {
            $value = [$value];
        }
        if(isset($this->header[$name])) {
            $this->header[$name] = array_merge($this->header[$name], $value);
        }else{
            $this->header[$name] = $value;
        }
        return $this;
    }

    function setHeader($name, $value)
    {
        if(!is_array($value)) {
            $value = [$value];
        }
        if(isset($this->header[$name]) && $this->header[$name] === $value) {
            return $this;
        }
        $this->header[$name] = $value;
        return $this;
    }


    function getHeaders()
    {
        return $this->header;
    }

    function unsetHeader($name)
    {
        if(isset($this->header[$name])){
            unset($this->header[$name]);
        }
        return $this;
    }
}