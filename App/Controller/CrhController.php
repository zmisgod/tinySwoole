<?php
namespace App\Controller;

use App\Components\Crh\CrhDraw;
use App\Components\Crh\DrawSvg;
use Core\Framework\AbstractController;

class CrhController extends AbstractController
{
    public function index()
    {
        $data = $this->mysqli()->query("select * from crh_station_lists where longtitude != ''")->fetchAll();

        try{
            $crh = new CrhDraw();
            $crh->setData($data);
            $crh->setType('circle');
            $crh->importType('svg');
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
            $data = $this->mysqli()->query("select * from crh_station_lists where train_name like '%".$station_name."%'")->fetchAll();
            $this->response()->setHeader("Access-Control-Allow-Credentials", "true");
            $this->response()->setHeader("Access-Control-Allow-Headers", "Origin,Authorization,Access-Control-Allow-Origin");
            $this->response()->setHeader("Access-Control-Allow-Methods", "*");
            $this->response()->setHeader("Access-Control-Allow-Origin", "*");
            $this->response()->setHeader("Access-Control-Expose-Headers", "Content-Length,Access-Control-Allow-Origin");
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

    public function saveData()
    {
        $this->response()->setHeader("Access-Control-Allow-Credentials", "true");
        $this->response()->setHeader("Access-Control-Allow-Headers", "Origin,Authorization,Access-Control-Allow-Origin");
        $this->response()->setHeader("Access-Control-Allow-Methods", "*");
        $this->response()->setHeader("Access-Control-Allow-Origin", "*");
        $this->response()->setHeader("Access-Control-Expose-Headers", "Content-Length,Access-Control-Allow-Origin");
        $station_name = $this->request()->input->post->getParam('station_name', '');
        if(empty($station_name)) {
            $this->response()->writeJson(403, '', 'empty station name');
        }
        $color = $this->request()->input->post->getParam('color', '#000');
        $ids = $this->request()->input->post->getParam('ids', '');
        if(empty($ids)) {
            $this->response()->writeJson(403, '', 'empty ids');
        }
        $ids_arr = explode(',', $ids);
        foreach ($ids_arr as $k => $v) {
            if(empty($v)) {
                unset($ids_arr[$k]);
            }
        }
        if(empty($ids_arr)) {
            $this->response()->writeJson(403, '', 'empty ids');
        }
        $result = $this->mysqli()->query("select train_id from crh_train_group_bind where train_name = '{$station_name}'")->fetchall();
        if($result) {
            $this->response()->writeJson(403, '', 'station name :'.$station_name.' is exits');
        }else {
            $this->mysqli()->query("insert into crh_train_group_bind (train_name, train_color, train_weight, updated_at, `type`, disabled) VALUE 
('{$station_name}', '{$color}', '5', ".time().", 1, 0)");
            $result = $this->mysqli()->query("select train_id from crh_train_group_bind where train_name = '{$station_name}'")->fetchall();
            $train_id = $result[0]['train_id'];
            $insert_value = '';
            $i = 1;
            foreach ($ids_arr as $v) {
                $insert_value .= ",('{$train_id}', '{$v}', '{$i}', '0')";
                $i++;
            }
            $insert_value = substr($insert_value, 1);
            $this->mysqli()->query("insert into crh_train_group_bind_details (train_id, station_id, sort_by, disabled) values ".$insert_value);
            $this->response()->writeJson(200, '',
                [
                    'station_name' => $station_name,
                    'color' => $color,
                    'ids' => $ids_arr
                ]);
        }
    }
}