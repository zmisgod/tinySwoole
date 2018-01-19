<?php
namespace Core\Framework;

use Core\Uti\DB\Mysqli;
use Core\Uti\Tools\Config;

abstract class AbstractController
{
    protected $actionName = null;
    protected $callArgs = null;
    protected $mysqli;

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

    /**
     * @return mixed
     */
    function mysqli()
    {
        return Mysqli::getInstance(Config::getInstance()->getConfig('config.mysqli'));
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
        if(method_exists($this, 'beforeAction')) {
            echo 1111111;
            $this->beforeAction();
        }
        if (!$this->response()->isEndResponse()) {
            if (method_exists($this, $actionName)) {
                $realName = $this->actionName;
                $this->$realName();
            }else{
                $this->response()->setStatus(Status::CODE_NOT_FOUND);
                return;
            }
        }
        echo 33333;
        if(method_exists($this, 'afterAction')) {
            echo 222222;
            $this->afterAction();
        }
    }
}