<?php
namespace App\Controller;

use Core\Framework\AbstractController;
use Core\Swoole\Server;

class IndexController extends AbstractController
{
    public function index()
    {
        Server::getInstance()->getServer()->task('1');
        $this->response()->write("this is IndexController index method (default method)");
    }

    public function benchmark()
    {
        $this->response()->write("hello world");
//        $this->response()->writeJson(200, 'hello world');
    }
}