<?php
namespace App\Controller;

use Core\Framework\AbstractController;

class IndexController extends AbstractController
{
    public function over()
    {
        $this->response()->write('hello world');
    }
}