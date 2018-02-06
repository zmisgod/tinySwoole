<?php
namespace Core\Framework;

use Core\Uti\DB\Mysqli;
use Core\Uti\Tools\Config;

abstract class AbstractController
{
    protected $actionName = null;
    protected $callArgs = null;
    protected $controllerName = null;
    protected $mysqli;

    function actionName($actionName = null)
    {
        if($actionName !== null) {
            $this->actionName = $actionName;
        }else{
            return $this->actionName;
        }
    }

    function controllerName($controllerName = null)
    {
        if($controllerName !== null) {
            $this->controllerName = $controllerName;
        }
    }

    function displayNotFound()
    {
        $this->response()->writeJson(400, 'ok', 'sds');
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
        try{
            return Mysqli::getInstance(Config::getInstance()->getConfig('config.mysqli'));
        }catch (\Exception $e) {
            $this->response()->writeJson(500, '', $e->getMessage());
            return;
        }
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
        if(method_exists($this, 'afterAction')) {
            $this->afterAction();
        }
    }
}