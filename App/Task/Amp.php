<?php
namespace App\Task;

use App\Components\AmpApi\AmpApi;
use Core\Framework\AbstractTask;

class Amp extends AbstractTask
{
    private $crh_station = 'crh_station_lists';

    function handleTask(\swoole_server $server,$task_id,$from_worker_id)
    {
        $row = [];
        $data = $this->mysqli()->query("select train_name, id from {$this->crh_station} where longtitude is null and disabled !='1'")->fetchall();
        foreach($data as $v) {
            if(mb_substr($v['train_name'], -1) != '站') {
                $v['train_name'] .= '站';
                $this->mysqli()->query("update {$this->crh_station} set train_name = '{$v['train_name']}' where id = ".$v['id']);
            }
            $row[] = $v;
        }
        $amp = new AmpApi();
        foreach ($row as $v) {
            $data = $amp->getAddressInfo($v['train_name']);
            $decode = json_decode($data, true);
            if($decode['status'] == '0' && $decode['infocode'] == '10003') {
                echo $decode['info'].' NOW, this task stop!!!!!!';
                break;
            }
            if(isset($decode['status']) && $decode['status'] == '1' && !empty($decode['pois']) && $decode['pois'] !='0') {
                $result = $decode['pois'][0];
                if($result['name'] == $v['train_name']) {
                    $train_name = $result['name'];
                    if(is_array($result['address'])) {
                        $p_address = $result['pname'].$result['cityname'].$result['adname'];
                    }else{
                        $p_address = $result['pname'].$result['cityname'].$result['adname'].$result['address'];
                    }
                    $address    = $result['location'];
                    $res        = explode(',',$address);
                    $longtitude = $res[0];//经度
                    $latitude   = $res[1];//纬度
                    $pccode     = $result['pcode'];
                    $citycode   = $result['citycode'];
                    $adcode     = $result['adcode'];
                    $sql = "update crh_station_lists set train_address = '{$p_address}', longtitude = '{$longtitude}', latitude = '{$latitude}', citycode = '{$citycode}', adcode='{$adcode}', pccode='{$pccode}', train_name='{$train_name}' where id = '{$v['id']}' and longtitude is null";
                    $this->mysqli()->query($sql);
                }
            }else{
                $sql = "update crh_station_lists set disabled='1' where id = '{$v['id']}' and longtitude is null";
                $this->mysqli()->query($sql);
            }
        }
    }

    function finishTask(\swoole_server $server,$task_id,$dataForFinishCallBack)
    {
        // TODO: Implement finishTask() method.
    }
}