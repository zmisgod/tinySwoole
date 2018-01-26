<?php
namespace App\Components\Crh;

class DrawSvg
{
    protected $data = [];
    protected $resize;
    protected $lineColor;
    protected $lineWeight;
    protected $dotsWeight;

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

    public function createCircle()
    {
        $la = ($this->data['longtitude']-90) * $this->resize;
        $lo = ($this->data['latitude']-10) * $this->resize;
        return '<circle cx="'.$la.'" cy="'.$lo.'" r="'.$this->dotsWeight.'" stroke="black" fill="#fff" alt="'.$this->data['train_name'].'" />';
    }
}