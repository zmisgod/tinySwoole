<?php
namespace App\Http\Controller;

use App\Http\Controller;
use Core\Swoole\Server;
use Core\Uti\Tools\Log;

class IndexController extends Controller
{
    /**
     * default controller
     */
    public function index()
    {
        Log::getInstance()->log('star');
        $this->response()->writeJson(200,"1this is IndexController index method (default method)");
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