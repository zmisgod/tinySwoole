<?php
namespace Core\Framework;

use Core\Uti\Tools\Di;
use Core\Uti\Tools\Register;

class Dispatch
{
    private static $instance;

    static function getInstance()
    {
        if(!isset(self::$instance)) {
            self::$instance = new Dispatch();
        }
        return self::$instance;
    }

    public function dispatch()
    {
        if(Response::getInstance()->isEndResponse()){
            return;
        }
        $url = Request::getInstance()->parseUrl();
        $request_uri = explode('/', trim($url->getPath(), '/'));
        //controller name
        $cName = strtolower($request_uri[0]);
        //method name
        $mName = '';
        if(isset($request_uri[1])) {
            $mName = $request_uri[1];
        }
        if($cName == '') {
            $cName = 'index';
        }
        if($mName == '') {
            $mName = 'index';
        }
        $className = 'App\Controller\\'.ucfirst($cName).'Controller';
        $result = Di::getInstance()->get($className);
        if(!$result) {
            if(file_exists(ROOT.'/'.str_replace('\\', '/', rtrim($className)).'.php')) {
                $instanceClass = new \ReflectionClass($className);
                $res = $instanceClass->getMethod($mName);
                if (!$res->isPublic()) {
                    throw new \Exception('method ['.$mName.'] is not a public function', 500);
                }
                $result = $instanceClass->newInstance();
                if($result instanceof AbstractController) {
                    Di::getInstance()->set($className, $result);
                }else{
                    throw new \Exception('class ['.$className.'] do not extends Core\Framework\AbstractController', 500);
                }
            }else{
                throw new \Exception('class ['.$className.'] not found', 500);
            }
        }
        if(!Response::getInstance()->isEndResponse()) {
            $result->controllerName($cName);
            $result->__call($mName);
        }
    }
}