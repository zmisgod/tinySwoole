<?php
namespace App\Controller;

use App\Task\Test;
use Core\Framework\AbstractController;
use Core\Swoole\Server;
use Core\Swoole\Timer;

class IndexController extends AbstractController
{
    public function index()
    {
        $this->response()->write("this is IndexController index method (default method)");
    }

    public function benchmark()
    {
        $this->response()->write("hello world");
//        $this->response()->writeJson(200, 'hello world');
    }
}