<?php
namespace Core\Framework\Request;

class GetRequest
{
    public $type;

    public $get;

    public $post;

    public $cookie;

    public $uploadFile;

    /**
     * GetRequest constructor.
     *
     * @param $get
     * @param $post
     * @param $cookie
     * @param $server
     * @param $uploadFile
     */
    public function __construct($get, $post, $cookie, $server, $uploadFile)
    {
        $this->get = new RequestGet($get);
        $this->post = new RequestPost($post);
        $this->cookie = new RequestCookie($cookie);
        $this->server = new RequestServer($server);
        $this->upload = new RequestUpload($uploadFile);
        return $this;
    }
}