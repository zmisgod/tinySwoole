<?php

namespace App\Components\Crh;

class CrhDraw
{
    private $startTag = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1"  width="13000" height="13000">';
    private $endTag = '</svg>';
    private $data;
    private $type = [];
    private $_valid_type = ['circle','line'];
    private $resize = 180;
    private $_valid_import_type = ['svg', 'html'];
    private $import_type = 'svg';
    private $result_svg;

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
        if(!in_array($import_type, [$this->_valid_import_type])) {
            throw new \Exception("Invalid import type :" . $import_type);
        }
        $this->import_type = $import_type;
    }

    public function run()
    {
        $crh = new DrawSvg();
        $this->beforeRun();
        foreach($this->data as $k => $v) {
            $crh->setData($v);
            $crh->setParams($this->resize, 'red',7,3);
            foreach($this->type as $type) {
                $result[] = call_user_func_array([$crh, 'create'. ucfirst($type)], []);
            }
        }
        $this->result_svg = $this->startTag . implode('',$result) . $this->endTag;
        return $this->afterRun();
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

        }else{
            file_put_contents(__DIR__ . '/CRH.svg',$this->result_svg);
            return [true,'生成成功'];
        }
    }
}