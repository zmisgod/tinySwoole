<?php
namespace Core\Framework;

abstract class ViewController extends AbstractController
{
    /**
     * @var HTMLTemplate
     */
    private $tpl;

    function display($data = null)
    {
        $this->tpl = new HTMLTemplate($this->controllerName, $this->actionName, $data);
        $this->response()->write($this->tpl->display());
    }
}