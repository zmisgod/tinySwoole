<?php
namespace App\Controller;

use App\Task\Test;
use Core\Framework\AbstractController;
use Core\Swoole\Server;
use Core\Swoole\Timer;
use Core\Uti\Tools\Tools;
use Phpml\Classification\KNearestNeighbors;
use Phpml\Regression\LeastSquares;

class IndexController extends AbstractController
{
    public function index()
    {
        $this->response()->writeJson(200,"this is IndexController index method (default method)");
    }

    public function ml()
    {
        $data = file_get_contents(PUBLIC_DIR.'/lottrery_result.txt');
        $data = json_decode($data, true);
        $date = [];
        $number = [];
        foreach ($data as $v) {
            $first = array_unique(array_slice($v['num'], 0, count($v['num'])-2));
            if(count($first) <= 5) {
                sort($first);
                $end = array_slice($v['num'], -2, 2);
                $date[] = $v['date'];
                $number[] = array_values(array_merge($first, $end));
            }
        }
        $i = [];
        $classifier = new LeastSquares();
        foreach ($number as $k => $v) {
            foreach ($v as $index => $value) {
                $i[$index][] = $value;
            }
        }
        foreach ($i as $k => $v) {
            $classifier->train([$date], $v);
            $data[$k] = $classifier->predict(["2017141"]);
        }
        $this->response()->writeJson(200, $data);
//        $samples = [[1, 3], [1, 4], [2, 4], [3, 1], [4, 1], [4, 2]];
//        $labels = ['a', 'a', 'a', 'b', 'b', 'b'];
//
//        $classifier = new KNearestNeighbors();
//        $classifier->train($samples, $labels);
//
//        $data = $classifier->predict([3, 2]);
//        $this->response()->writeJson(200, $data);
    }

    public function getData()
    {
        $data = Tools::getInstance()->curl_get('http://chart.lottery.gov.cn//dltBasicKaiJiangHaoMa.do?typ=1&issueTop=20000000');
        file_put_contents(PUBLIC_DIR.'/lottrery.txt', $data);
        $data = preg_replace('/<td class="M_*.*?">(.*?)<\/td>/ism', '0', $data);
        preg_match_all('/<tr class="DatRow".*?>(.*?)<\/tr>/ism', $data, $matchall);
        $result = [];
        if(isset($matchall[0])) {
            foreach ($matchall[0] as $val) {
                preg_match('/<td class="Issue">(.*?)<\/td>/ism', $val, $date);
                preg_match_all('/<td class="B_*.*?">(.*?)<\/td>/ism', $val, $numbers);
                if(isset($date[1]) && isset($numbers[1])) {
                    $result[] = [
                        'date' => $date[1],
                        'num' => $numbers[1]
                    ];
                }
            }
        }
        file_put_contents(PUBLIC_DIR.'/lottrery_result.txt', json_encode($result));
        $this->response()->writeJson(200, '', 'successful');
    }

    public function combinaData()
    {
        $data = Tools::getInstance()->curl_get('http://chart.lottery.gov.cn//dltBasicKaiJiangHaoMa.do?typ=2&issueFrom=2016155&issueTo=2017222');
        file_put_contents(PUBLIC_DIR.'/lottrery2.txt', $data);
        $data = preg_replace('/<td class="M_*.*?">(.*?)<\/td>/ism', '0', $data);
        preg_match_all('/<tr class="DatRow".*?>(.*?)<\/tr>/ism', $data, $matchall);
        $result = [];
        if(isset($matchall[0])) {
            foreach ($matchall[0] as $val) {
                preg_match('/<td class="Issue">(.*?)<\/td>/ism', $val, $date);
                preg_match_all('/<td class="B_*.*?">(.*?)<\/td>/ism', $val, $numbers);
                if(isset($date[1]) && isset($numbers[1])) {
                    $result[] = [
                        'date' => $date[1],
                        'num' => $numbers[1]
                    ];
                }
            }
        }
        $oldData = file_get_contents(PUBLIC_DIR.'/lottrery_result.txt');
        $od = json_decode($oldData, true);
        $od = array_merge($od, $result);
        file_put_contents(PUBLIC_DIR.'/lottrery_result.txt', json_encode($od));
        $this->response()->writeJson(200, '', 'successful');
    }

    public function benchmark()
    {
        $this->response()->writeJson(200,"hello world");
//        $this->response()->writeJson(200, 'hello world');
    }
}