<?php
namespace App\Controller;

use Core\Framework\AbstractController;

class WechatController extends AbstractController
{
    public function index()
    {
        // TODO: Implement index() method.
    }

    public function server()
    {
        $data = $this->request()->input->get->getParam('get');
        $this->response()->writeJson(200, $data, 'ok');
    }
}