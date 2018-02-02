<?php
namespace App\Controller;

use App\Components\Crh\CrhDraw;
use App\Components\Crh\DrawSvg;
use Core\Framework\AbstractController;

class CrhController extends AbstractController
{
    private $crh_station = 'crh_station_lists';
    private $table_list = 'crh_train_list';
    private $table_details = 'crh_train_list_details';

    public function index()
    {
        $data = $this->mysqli()->query("select * from {$this->crh_station} where longtitude != ''")->fetchAll();

        try{
            $crh = new CrhDraw();
            $crh->setData($data);
            $crh->setType('circle');
            $crh->importType('json');
            list($ok, $msg) = $crh->run();
            if($ok) {
                $status = 200;
            }else{
                $status = 500;
            }
            $this->response()->writeJson($status, "", $msg);
        }catch(\Exception $e) {
            $this->response()->writeJson(500, "", $e->getMessage());
        }
    }

    /**
     * 搜索
     */
    public function searchStation()
    {
        $station_name = $this->request()->input->get->getParam('station_name', '');
        if($station_name) {
            $station_name = addslashes($station_name);
            $data = $this->mysqli()->query("select * from {$this->crh_station} where train_name like '%".$station_name."%'")->fetchAll();
            if($data) {
                $draw = new CrhDraw();
                $draw->crh->setResize($draw->resize);
                foreach ($data as $k => $v) {
                    $data[$k]['longtitude'] = $draw->crh->recountLo($v['longtitude']);
                    $data[$k]['latitude'] = $draw->crh->recountLa($v['latitude']);
                }
                $this->response()->writeJson(200, $data, "ok");
            }else{
                $this->response()->writeJson(400, '', 'empty data');
            }
        }else{
            $this->response()->writeJson(500, '', 'error');
        }
    }

    public function addNewStation()
    {
        $station_name = $this->request()->input->post->getParam('station_name', '');
        if($station_name) {
            $station_name = addslashes(str_replace(" ", '', $station_name));
            if(mb_substr($station_name, -1) != '站') {
                $station_name .= '站';
            }
            if($this->checkStationExists($station_name)) {
                return $this->response()->writeJson(400, '', 'err');
            }else {
                $this->mysqli()->query("insert into {$this->crh_station} (train_name) value ('{$station_name}')");
                $data = [
                    'train_address' => null,
                    'longtitude' => null,
                    'latitude' => null,
                    'train_name' => $station_name,
                    'station_id' => $this->checkStationExists($station_name)
                ];
                $this->response()->writeJson(200,$data,"ok");
            }
        }else{
            $this->response()->writeJson(400, '', 'error');
        }
    }

    private function checkStationExists($station_name)
    {
        $result = $this->mysqli()->query("select id from {$this->crh_station} where train_name = ".$station_name)->fetchall();
        if($result) {
            return $result[0]['id'];
        }else{
            return false;
        }
    }

    public function saveData()
    {
        $station_name = $this->request()->input->post->getParam('station_name', '');
        if(empty($station_name)) {
            $this->response()->writeJson(403, '', 'empty station name');
        }
        $color = $this->request()->input->post->getParam('color', '#000');
        $ids = $this->request()->input->post->getParam('before', '');
        if(empty($ids)) {
            $this->response()->writeJson(403, '', 'empty ids');
        }
        $after = $this->request()->input->post->getParam('after', '');

        $saveRow = [];
        $ids_arr = explode(',', $ids);
        foreach ($ids_arr as $k => $v) {
            if(!empty($v)) {
                $saveRow[] = ['branch' => 0, 'id' => $v];
            }
        }
        if(empty($saveRow)) {
            $this->response()->writeJson(403, '', 'empty ids');
        }
        if($after) {
            $br_l = explode('__&&__', $after);
            $i = 1;
            foreach($br_l as $v) {
                $res_br = str_replace(' ', '', $v);
                $res_br = explode(',', $res_br);
                if($res_br) {
                    foreach($res_br as $tem) {
                        if($tem) {
                            //$branch_arr[$i][] = $tem;
                            $saveRow[] = ['branch' => $i, 'id' => $tem];
                        }
                    }
                    $i++;
                }
            }
        }
        $result = $this->mysqli()->query("select train_id from {$this->table_list} where train_name = '{$station_name}'")->fetchall();
        if($result) {
            $this->response()->writeJson(403, '', 'station name :'.$station_name.' is exits');
        }else {
            $this->mysqli()->query("insert into {$this->table_list} (train_name, train_color, train_weight, updated_at, `type`, disabled) VALUE 
('{$station_name}', '{$color}', '5', ".time().", 1, 0)");
            $result = $this->mysqli()->query("select train_id from {$this->table_list} where train_name = '{$station_name}'")->fetchall();
            $train_id = $result[0]['train_id'];
            $insert_value = '';
            $i = 1;
            foreach ($saveRow as $v) {
                $insert_value .= ",('{$train_id}', '{$v['id']}', '{$v['branch']}', '{$i}', '0')";
                $i++;
            }
            $insert_value = substr($insert_value, 1);
            $this->mysqli()->query("insert into {$this->table_details} (train_id, station_id, branch, sort_by, disabled) values ".$insert_value);
            $this->response()->writeJson(200, '',
                [
                    'station_name' => $station_name,
                    'color' => $color,
                    'ids' => $ids_arr
                ]);
        }
    }

    public function showData()
    {
        $way = $this->mysqli()->query("select * from {$this->table_list}")->fetchall();
        if($way) {
            $draw = new CrhDraw();
            $draw->crh->setResize($draw->resize);
            $result = [];
            foreach ($way as $v) {
                $detail = $this->mysqli()->query("select l.longtitude, l.latitude,l.id,l.train_name,d.branch,d.sort_by from {$this->crh_station} as l  left join {$this->table_details} as d  on d.station_id=l.id where d.train_id = ".$v['train_id']." order by sort_by asc")->fetchall();
                $filter_one = [];
                foreach ($detail as $val) {
                    if($val['longtitude']) {
                        $filter_one[$val['branch']][] = $val;
                    }
                }
                $rest = [];
                foreach($filter_one as $branch => $data) {
                    $rest[$branch] = '';
                    foreach($data as $tail) {
                        $rest[$branch] .=  ', '.$draw->crh->recountLo($tail['longtitude']).','.$draw->crh->recountLa($tail['latitude']);
                    }
                }
                if(isset($rest[0])){
                    $original = $rest[0];
                    unset($rest[0]);
                }else{
                    $original = ',';
                }
                $resData = [];
                if(!empty($rest) && is_array($rest)) {
                    foreach($rest as $sb => $road) {
                        $resData[] = substr($original,1) . ' ' . $road;
                    }
                }else{
                    $resData[] = substr($original, 1);
                }
                $result[] = [
                    'train_name' => $v['train_name'],
                    'train_color' => $v['train_color'],
                    'train_weight' => $v['train_weight'],
                    'data' => $resData
                ];
            }
            $this->response()->writeJson(200, $result, 'ok');
        }else{
            $this->response()->writeJson(403, '', 'empty');
        }
    }

    public function afterAction()
    {
        $this->response()->setHeader("Access-Control-Allow-Credentials", "true");
        $this->response()->setHeader("Access-Control-Allow-Headers", "Origin,Authorization,Access-Control-Allow-Origin");
        $this->response()->setHeader("Access-Control-Allow-Methods", "*");
        $this->response()->setHeader("Access-Control-Allow-Origin", "*");
        $this->response()->setHeader("Access-Control-Expose-Headers", "Content-Length,Access-Control-Allow-Origin");
    }
}