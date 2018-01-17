<?php
namespace Core\Framework\Request;

class GetRequest
{
    public $type;

    public $get;

    public $post;

    public $cookie;

    public function __construct($get, $post, $cookie)
    {
        $this->get = new RequestGet($get);
        $this->post = new RequestPost($post);
        $this->cookie = new RequestCookie($cookie);
    }
}