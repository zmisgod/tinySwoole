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
    /**
     * default controller
     */
    public function index()
    {
        $this->response()->writeJson(200,"this is IndexController index method (default method)");
    }

    /**
     * please do not delete me
     *
     * it used to `php index.php status` to check this framework running status
     */
    public function status()
    {
        $status = Server::getInstance()->getServer()->stats();
        $this->response()->writeJson(200, $status, 'ok');
    }

    /**
     * benchmark function
     */
    public function benchmark()
    {
        $this->response()->writeJson(200,"hello world");
    }
}