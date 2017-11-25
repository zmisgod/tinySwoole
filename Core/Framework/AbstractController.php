<?php
namespace Core\Framework;

abstract class AbstractController
{
    protected $actionName = null;
    protected $callArgs = null;
    protected $swoole_request;
    protected $swoole_response;

    public function setRequest(Request $request)
    {
        $this->swoole_request = $request;
    }

    public function setResponse(Response $response)
    {
        $this->swoole_response = $response;
    }

    function actionName($actionName = null)
    {
        if($actionName === null) {
            return $this->actionName;
        }else{
            $this->actionName = $actionName;
        }
    }

    function request()
    {
        return $this->swoole_request;
    }

    function response()
    {
        return $this->swoole_response;
    }

    function __call($actionName, $arguments = null)
    {
        if (in_array($actionName, [
            'actionName', 'setRequest', 'setResponse',  'request', 'response', '__call'
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
            }
        }
    }
}