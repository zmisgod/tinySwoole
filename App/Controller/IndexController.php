<?php
namespace App\Controller;

use Core\Framework\AbstractController;

class IndexController extends AbstractController
{
    public function benchmark()
    {
        $this->response()->write("hello world");
//        $this->response()->writeJson(200, 'hello world');
    }
}