<?php

namespace Core\Uti\DB;

class Mysqli
{
    static $instance;
    public $con;
    private $config;
    private $_debug;
    private $_debug_info = [];

    public static function getInstance($config = null)
    {
        if($config !== null) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public function __construct($config)
    {
        $this->config = $config;
        $this->connect();
    }

    public function connect()
    {
        $this->con = mysqli_connect($this->config['host'],$this->config['user'],$this->config['password'],$this->config['database'],$this->config['port']);
        if(!$this->con) {
            throw new \Exception('sql connection error:' . mysqli_error($this->con));
        }
        mysqli_query($this->con,'set names ' . $this->config['charset']);

        return true;
    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->con;
    }

    /**
     * 获取最后的插入id
     *
     * @return mixed
     */
    public function getLastInsertId()
    {
        return mysqli_insert_id($this->getConnection());
    }

    /**
     * 设置debug
     *
     * @param bool $debug
     *
     * @return $this
     */
    public function setDebug($debug = false)
    {
        $this->_debug      = $debug;
        $this->_debug_info = [];
        return $this;
    }

    public function printDebug()
    {
        $debug = $this->_debug_info;
        //清空debug_info
        $this->_debug_info = [];
        return $debug;
    }

    public function ping()
    {
        if(!mysqli_ping($this->getConnection())) {
            return false;
        }
        return true;
    }

    /**
     * 断线重连
     */
    public function reConnect()
    {
        if(!@$this->ping()) {
            //关闭之前的链接并建立新连接
            $this->close();
            return $this->connect();
        }
        return true;
    }

    public function close()
    {
        mysqli_close($this->getConnection());
    }

    public function getAffectedRows()
    {
        return mysqli_affected_rows($this->getConnection());
    }

    public function errorMessage($sql)
    {
        return mysqli_error($this->getConnection()) . "<hr />$sql<hr />MYSQL SERVER: {$this->config['host']}, port : {$this->config['port']}";
    }

    public function query($sql)
    {
        $res = false;
        $this->recordDebug($sql);
        for($i = 0; $i < 2; $i ++) {
            $res = mysqli_query($this->getConnection(), $sql);
            if($res === false) {
                if(in_array(mysqli_errno($this->getConnection()),[2006,2013])) {
                    $this->recordDebug('reconnect mysqli');
                    $r = $this->reConnect();
                    if($r === true) {
                        continue;
                    }
                }
                return false;
            }
            break;
        }
        if($res === false) {
            $err_log = 'sql error : ' . $this->errorMessage($sql);
            $this->recordDebug($err_log);
            throw new \Exception($err_log);
        }
        if(is_bool($res)) {
            return $res;
        }
        if($this->_debug){
            return $this;
        }
        return new MysqliRecord($res);
    }

    public function recordDebug($log)
    {
        $this->_debug_info[] = $log;
    }
}