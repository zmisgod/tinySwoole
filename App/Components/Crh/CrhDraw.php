<?php

namespace App\Components\Crh;

class CrhDraw
{
    private $data;
    private $type = [];
    private $_valid_type = ['circle','line'];
    public $resize = 180;
    private $_valid_import_type = ['svg', 'html', 'json'];
    private $import_type = 'svg';
    private $result_svg;
    private $import_path;

    /**
     * @var DrawSvg
     */
    public $crh;

    function __construct()
    {
        $this->crh = new DrawSvg();
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function setType($type)
    {
        if(!in_array($type,$this->_valid_type)) {
            throw new \Exception("Invalid type :" . $type);
        }
        $this->type[$type] = $type;
    }

    public function importType($import_type)
    {
        if(!in_array($import_type, $this->_valid_import_type)) {
            throw new \Exception("Invalid import type :" . $import_type);
        }
        $this->import_type = $import_type;
    }

    public function setImportPath($path)
    {
        $this->import_path = $path;
    }

    public function run()
    {
        try {
            $this->beforeRun();
            foreach ($this->data as $k => $v) {
                $this->crh->setData($v);
                $this->crh->setParams($this->resize, 'red', 7, 3);
                foreach ($this->type as $type) {
                    if($this->import_type == 'json') {
                        $result[] = call_user_func_array([$this->crh, 'create'.ucfirst($this->import_type)], []);
                    }else{
                        $result[] = call_user_func_array([$this->crh, 'create' . ucfirst($type)], []);
                    }
                }
            }
            $this->result_svg = $result;
            return $this->afterRun();
        }catch (\Exception $e) {
            return false;
        }
    }

    private function beforeRun()
    {
        if(empty($this->type)) {
            throw new \Exception("empty type");
        }
        if(empty($this->data)) {
            throw new \Exception("empty data");
        }
    }

    private function afterRun()
    {
        if($this->import_type == 'html') {

        }elseif($this->import_type == 'json'){
            if(empty($this->import_path)) {
                $path = __DIR__.'/';
            }else{
                $path = $this->import_path;
            }
            file_put_contents($path.'CRH.json', json_encode($this->result_svg));
            return [true,'生成成功'];
        }else{
            $parse = implode('', $this->result_svg);
            $output = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1"  width="'.$this->crh->maxWidth.'" height="'.$this->crh->maxWidth.'">'.$parse.'</svg>';
            file_put_contents(__DIR__ . '/CRH.svg',$output);
            return [true,'生成成功'];
        }
    }
}