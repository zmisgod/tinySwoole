<?php
namespace Core\Uti\Tools;

class Config extends \DirectoryIterator
{
    static $instance;
    static $saved = [];
    public $config = [];

    public static function getInstance($path = null)
    {
        if($path !== null) {
            self::$instance = new static($path);
        }
        return self::$instance;
    }

    function __construct($configPath)
    {
        if(empty($configPath)){
            throw new \Exception('empty config path');
        }
        foreach (new \DirectoryIterator($configPath) as $fileInfo) {
            if(!in_array($fileInfo->getFilename(), ['.', '..'])) {
                $filename = $fileInfo->getFilename();
                $filePre = substr($filename, 0, -4);
                $this->config[$filePre] = require_once $fileInfo->getPathname();
            }
        }
    }

    /**
     * key可能是 config.server.HOST,则搜索 config.php文件夹下的server.HOST下的数据
     *
     * @param $key
     * @return array|null
     * @throws \Exception
     */
    public function getConfig($key)
    {
        if(empty($key)) return null;
        $value = explode('.', $key);
        $config = $this->config;
        foreach ($value as $v) {
            if(isset($config[$v])) {
                $config = $config[$v];
            }else{
                throw new \Exception("do not have this config => ".$key);
            }
        }
        return $config;
    }
}