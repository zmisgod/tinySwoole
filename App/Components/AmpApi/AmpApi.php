<?php
namespace App\Components\AmpApi;


class AmpApi
{
    const AMP_KEY = '2df1febcbd099f0c3f02ca506169acab';

    public function getAddressInfo($train_name, $type = '火车站')
    {
        $url = 'http://restapi.amap.com/v3/place/text?key='.self::AMP_KEY.'&keywords='.$train_name.'&types='.$type.'&city=&children=1&offset=20&page=1&extensions=all';
        return $this->curlGet($url);
    }

    protected function curlGet($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}