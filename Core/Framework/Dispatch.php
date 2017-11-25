<?php
namespace Core\Framework;

class Dispatch
{
    static $instance;

    static function getInstance()
    {
        if(!isset(self::$instance)) {
            self::$instance = new Dispatch();
        }
        return self::$instance;
    }

    public function dispatch(Request $request,Response $response)
    {
        if($response->isEndResponse()){
            return;
        }
        $url = $request->parseUrl();
        $request_uri = explode('/', trim($url->getPath(), '/'));
        //controller name
        $cName = $request_uri[0];
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
        if(file_exists(ROOT.'/'.str_replace('\\', '/', rtrim($className)).'.php')) {
            $instanceClass = new \ReflectionClass($className);
            $res = $instanceClass->getMethod($mName);
            if (!$res->isPublic()) {
                throw new \Exception('method is not a public function');
            }
            $classObj = $instanceClass->newInstance();
            $classObj->setRequest($request);
            $classObj->setResponse($response);
            $classObj->__call($mName);
        }
    }
}