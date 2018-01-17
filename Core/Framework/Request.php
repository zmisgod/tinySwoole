<?php
namespace Core\Framework;

use Core\Framework\Request\GetRequest;
use Core\IO\Stream;

class Request extends BaseRequest
{
    private static $instance;
    private $swoole_http_request = null;

    private $cookie;
    private $header;
    private $get;
    private $post;
    private $method;
    private $body;
    private $uri;
    private $files;
    public $input;
    private $protocolVersion;

    static function getInstance(\swoole_http_request $swoole_request = null)
    {
        if($swoole_request !== null) {
            self::$instance = new Request($swoole_request);
        }
        return self::$instance;
    }

    function __construct(\swoole_http_request $http_request)
    {
        $this->swoole_http_request = $http_request;

        //获取url信息
        $this->uri = $this->parseUrl();

        //获取非urlencode-form表单的POST原始数据
        $this->body = new Stream($this->swoole_http_request->rawContent());

        //获取protocol version
        $this->protocolVersion = str_replace('HTTP/', '', $this->swoole_http_request->server['server_protocol']);

        $this->method = $this->swoole_http_request->server['request_method'];

        $this->header = $this->getHeaders();

        $this->files = $this->parseFiles();

        $this->cookie = $this->parseCookie();

        $this->get = $this->parseGet();

        $this->post = $this->parsePost();

        $this->input = new GetRequest($this->get, $this->post, $this->cookie);

        parent::__construct($this->header, $this->body, $this->protocolVersion);
    }

    public function parseHeader()
    {
        $headers = $this->swoole_http_request->header;
        foreach ($headers as $header => $val) {
            $this->appendHeader($header, $val);
        }
    }

    /**
     * 从swoole中获取请求的url信息
     *
     * @return Uri
     */
    public function parseUrl()
    {
        $uri = new Uri();
        //默认为http
        $uri->setScheme('http');
        $uri->setPath($this->swoole_http_request->server['path_info']);
        if(isset($this->swoole_http_request->server['query_string'])) {
            $uri->setQuery($this->swoole_http_request->server['query_string']);
        }
        $host = $this->swoole_http_request->header['host'];
        $host = explode(':', $host);
        $uri->setHost($host[0]);
        if(isset($host[1])) {
            $uri->setPort($host[1]);
        }
        return $uri;
    }

    /**
     * 从swoole中获取上传文件的信息
     */
    public function parseFiles()
    {
        $temp = [];
        if(isset($this->swoole_http_request->files)) {
            foreach ($this->swoole_http_request->files as $key => $value) {
                $temp[$key] = new UploadFile(
                    $value['temp_name'],
                    (int)$value['size'],
                    (int)$value['error'],
                    $value['name'],
                    $value['type']
                );
            }
        }
        return $temp;
    }

    /**
     * 从swoole获取get信息
     *
     * @return array|GetRequest
     */
    public function parseGet()
    {
        return isset($this->swoole_http_request->get) ? $this->swoole_http_request->get: [];
    }

    /**
     * 从swoole获取post信息
     *
     * @return array
     */
    public function parsePost()
    {
        return isset($this->swoole_http_request->post) ? $this->swoole_http_request->post : [];
    }

    /**
     * 从swoole获取cookie信息
     *
     * @return array
     */
    public function parseCookie()
    {
        return isset($this->swoole_http_request->cookie) ? $this->swoole_http_request->cookie : [];
    }
}