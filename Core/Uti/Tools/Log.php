<?php
namespace Core\Uti\Tools;

class Log
{
    private static $instance;

    static function getInstance(){
        if(!isset(self::$instance)){
            //这样做纯属为了IDE提示
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function log($content, $outputFile = 'debug')
    {
        $logFolder = Config::getInstance()->getConfig('config.framework.log_folder');
        if(!$logFolder) {
            file_put_contents(ROOT. 'Log/Debug/debug.log', $this->output('请配置Config文件夹下的config.php下面的framework.log_folder'));
        }else{
            if(!file_exists($logFolder)) {
                mkdir($logFolder, 0755, true);
            }
            file_put_contents($logFolder.$outputFile.'.log', $this->output($content), FILE_APPEND);
        }
    }

    private function output($content)
    {
        return '[time : '.date('Y-m-d H:i:s').'] '.$this->objectToString($content).PHP_EOL;
    }

    private function objectToString($obj){
        if(is_object($obj)){
            if(method_exists($obj,"__toString")){
                $obj = $obj->__toString();
            }else if(method_exists($obj,'jsonSerialize')){
                $obj = json_encode($obj,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }else{
                $obj = var_export($obj,true);
            }
        }else if(is_array($obj)){
            $obj = json_encode($obj,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
        return $obj;
    }
}