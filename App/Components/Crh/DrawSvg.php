<?php
namespace App\Components\Crh;

class DrawSvg
{
    protected $data = [];
    protected $resize;
    protected $lineColor;
    protected $lineWeight;
    protected $dotsWeight;

    public $maxWidth = 0;
    public $maxHeight = 0;

    public function setData($data)
    {
        $this->data = $data;
    }

    public function setParams($resize, $color, $lineWeight, $dotsWeight)
    {
        $this->resize = $resize;
        $this->lineColor = $color;
        $this->lineWeight = $lineWeight;
        $this->dotsWeight = $dotsWeight;
    }

    public function setResize($resize)
    {
        $this->resize = $resize;
    }

    public function createLine()
    {
        $svg = '<path d="';
        $i = 0;
        foreach ($this->data as $k => $v)
        {
            $v['longtitude'] = ($v['longtitude']-100) * $this->resize;
            $v['latitude'] = ($v['latitude']-20) * $this->resize;
            if($i == 0) {
                $svg .= ' M ' . $v['latitude'] . ',' . $v['longtitude'] . ' ';
            }else{
                $svg .= ' L '.$v['latitude'].','.$v['longtitude'].' ';
            }
            $i ++;
        }
        $svg .= '" fill="transparent" stroke="'.$this->lineColor.'" stroke-width="'.$this->lineWeight.'" />';
        return $svg;
    }

    public function recountLo($number)
    {
        return round(($number - 70) * $this->resize, 5);
    }

    public function recountLa($number)
    {
        return round(($number - 10) * $this->resize, 5);
    }

    private $la;
    private $lo;
    private $stroke = 'black';
    private $fill = '#fff';

    private function beforeCreate()
    {
        $this->la = $this->recountLo($this->data['longtitude']);
        $this->lo = $this->recountLa($this->data['latitude']);
        if($this->la > $this->lo) {
            if($this->la > $this->maxHeight) {
                $this->maxHeight = $this->la;
                $this->maxWidth = $this->la;
            }
        }else{
            if($this->lo > $this->maxHeight) {
                $this->maxHeight = $this->lo;
                $this->maxWidth = $this->lo;
            }
        }
    }

    public function createCircle()
    {
        $this->beforeCreate();
        return '<circle cx="'.$this->la.'" cy="'.$this->lo.'" r="'.$this->dotsWeight.'" aid="'.$this->data['id'].'" stroke="'.$this->stroke.'" fill="'.$this->fill.'" alt="'.$this->data['train_name'].'" />';
    }

    public function createJson()
    {
        $this->beforeCreate();
        return [
            'cx' => $this->la,
            'cy' => $this->lo,
            'r' => $this->dotsWeight,
            'aid' => $this->data['id'],
            'stroke' => $this->stroke,
            'fill' => $this->fill,
            'alt' => $this->data['train_name']
        ];
    }
}