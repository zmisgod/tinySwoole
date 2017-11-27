<?php
namespace Core\Framework;

abstract class AbstractController
{
    protected $actionName = null;
    protected $callArgs = null;

    function actionName($actionName = null)
    {
        if($actionName === null) {
            return $this->actionName;
        }else{
            $this->actionName = $actionName;
        }
    }

    /**
     * 默认的方法
     *
     * @return mixed
     */
    abstract function index();

    function request()
    {
        return Request::getInstance();
    }

    function response()
    {
        return Response::getInstance();
    }

    function __call($actionName, $arguments = null)
    {
        if (in_array($actionName, [
            'actionName', 'request', 'response', '__call'
        ])) {
            $this->response()->setStatus(Status::CODE_INTERNAL_SERVER_ERROR);
            return;
        }
        $this->actionName($actionName);
        if (!$this->response()->isEndResponse()) {
            if (method_exists($this, $actionName)) {
                $realName = $this->actionName;
                $this->$realName();
            }else{
                $this->response()->setStatus(Status::CODE_NOT_FOUND);
                return;
            }
        }
    }
}