<?php
namespace Core\Framework\Request;

class RequestPost
{
    public $params;

    public $contentType;

    public function __construct($data = [], $contentType = '')
    {
        $this->params = $data;
        $this->contentType = '';
    }

    public function getParam($name, $default = false, $func = false)
    {
        if(isset($this->params[$name])) {
            if($func !== false && function_exists($func)) {
                return call_user_func($func, [$this->params[$name]]);
            }else{
                return $this->params[$name];
            }
        }else{
            if($default) {
                return $default;
            }else{
                return false;
            }
        }
    }

    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        if($contentType == 'application/json' && count($this->params) == 1) {
            $key = array_keys($this->params);
            var_dump($key[0]);
            $data = json_decode($key[0], true);
            if($data !== null) {
                $this->params = $data;
            }
        }
        return $this;
    }

    public function getParams(array $name = [], $default = false, $func = [])
    {
        $row = [];
        if(count($func) >1 && count($name) != count($func)) {
            throw new \Exception('getParams的$func应该与$name的值的个数相同');
        }
        if($name[0] = '*') {
            foreach($this->params as $k => $v){
                if(!empty($func[0]) && function_exists($func[0])) {
                    $row[$k] = call_user_func($func[0],[$v]);
                } else {
                    $row[$k] = $v;
                }
            }
            return $row;
        } else {
            foreach($name as $k => $v) {
                if(isset($this->params[$v])) {
                    if(count($func) > 1) {
                        if(!empty($func[$k]) && function_exists($func[$k])) {
                            $row[$k] = call_user_func($func[$k],[$this->params[$v]]);
                        } else {
                            $row[$k] = $this->params[$v];
                        }
                    } else {
                        if(!empty($func[0]) && function_exists($func[0])) {
                            $row[$k] = call_user_func($func[0],[$this->params[$v]]);
                        } else {
                            $row[$k] = $this->params[$v];
                        }
                    }
                } else {
                    if($default) {
                        $row[$k] = $default;
                    } else {
                        $row[$k] = false;
                    }
                }
            }
        }
        return $row;
    }
}