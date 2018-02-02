<?php
namespace Core\Framework;

use Core\Framework\Request\Cookie;

class Response extends ResponseMethod
{
    private static $instance = null;
    private $end_response = 0;
    private $swoole_http_response = null;

    const STATUS_NOT_END = 0;
    const STATUS_LOGICAL_END = 1;
    const STATUS_REAL_END = 2;

    static function getInstance(\swoole_http_response $swoole_http_response = null)
    {
        if($swoole_http_response !== null) {
            self::$instance = new Response($swoole_http_response);
        }
        return self::$instance;
    }

    function __construct(\swoole_http_response $swoole_http_response)
    {
        $this->swoole_http_response = $swoole_http_response;
    }

    /**
     * 结束请求
     *
     * @param bool $is_real_end
     *
     * @return bool
     */
    function end($is_real_end = false)
    {
        if($this->end_response == self::STATUS_NOT_END){
            $this->end_response = self::STATUS_LOGICAL_END;
        }
        if($this->end_response !== self::STATUS_REAL_END && $is_real_end === true) {
            $this->end_response = self::STATUS_REAL_END;
            //获取当前请求框架的状态码
            $status = $this->getStatusCode();
            //设置swoole的状态码
            $this->swoole_http_response->status($status);
            //获取框架的header
            $headers = $this->getHeaders();
            //设置swoole的header
            foreach ($headers as $header => $val) {
                foreach ($val as $sub) {
                    $this->swoole_http_response->header($header, $sub);
                }
            }
            $cookies = $this->getCookies();
            foreach($cookies as $cookie) {
                $this->swoole_http_response->cookie($cookie->getName(), $cookie->getValue(), $cookie->getExpire(), $cookie->getPath(), $cookie->getDomain(), $cookie->getSecure(), $cookie->getHttpOnly());
            }
            //获取正文（Core\Framework\response中write/writeJson的数据）
            $write = $this->getBody()->__toString();
            if (!empty($write)) {
                $this->swoole_http_response->write($write);
            }
            //关闭文件资源（释放内存）
            $this->getBody()->close();
            //结束Http响应，发送HTML内容
            $this->swoole_http_response->end();
            return true;
        }else{
            return false;
        }
    }

    function isEndResponse()
    {
        return $this->end_response;
    }

    public function redirect($url)
    {
        if(!$this->isEndResponse()) {
            $this->setStatus(Status::CODE_MOVED_PERMANENTLY);
            $this->setHeader('Location', $url);
        }else{
            trigger_error('response has end');
        }
    }

    public function getSwooleResponse()
    {
        return $this->swoole_http_response;
    }

    public function setCookie($name, $value = null, $expire = null, $path = null, $domain = null, $secure = null, $httpOnly = null)
    {
        if(!$this->isEndResponse()) {
            $cookie = new Cookie();
            $cookie->setName($name);
            $cookie->setValue($value);
            $cookie->setExpire($expire);
            $cookie->setPath($path);
            $cookie->setDomain($domain);
            $cookie->setSecure($secure);
            $cookie->setHttpOnly($httpOnly);
            $this->setResponseCookie($cookie);
            return true;
        }else{
            trigger_error('response has end');
            return false;
        }
    }

    /**
     * 写入json到内存中
     *
     * @param int $statusCode
     * @param null $result
     * @param null $msg
     * @return bool
     */
    function writeJson($statusCode = 200, $result = null, $msg = null)
    {
        if(!$this->isEndResponse()) {
            $this->getBody()->rewind();
            $data = [
                'code' => $statusCode,
                'result' => $result,
                'msg' => $msg
            ];
            $this->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
            $this->setHeader('Content-Type', 'application/json;charset=utf-8');
            $this->setStatus(200);
            return true;
        }else{
            trigger_error('response has end');
            return false;
        }
    }

    function write($obj){
        if(!$this->isEndResponse()){
            if(is_object($obj)){
                if(method_exists($obj,"__toString")){
                    $obj = $obj->__toString();
                }else if(method_exists($obj,'jsonSerialize')){
                    $obj = json_encode($obj,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    $obj = var_export($obj,true);
                }
            }else if(is_array($obj)){
                $obj = json_encode($obj,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
            $this->getBody()->write($obj);
            return true;
        }else{
            trigger_error("response has end");
            return false;
        }
    }
}