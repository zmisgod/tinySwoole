<?php
namespace App\Controller;

use App\Components\Crh\CrhDraw;
use Core\Framework\AbstractController;

class CrhController extends AbstractController
{
    public function index()
    {
        $data = $this->mysqli()->query("select * from crh_train_lists where longtitude != ''")->fetchAll();

        try{
            $crh = new CrhDraw();
            $crh->setData($data);
            $crh->setType('circle');
            $crh->importType('html');
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
}