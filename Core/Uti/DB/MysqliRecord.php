<?php
namespace Core\Uti\DB;

class MysqliRecord
{
    public $result;

    public function __construct($result)
    {
        $this->result = $result;
    }

    public function fetch()
    {
        return mysqli_fetch_assoc($this->result);
    }

    public function fetchall()
    {
        $data = [];
        while ($record = mysqli_fetch_assoc($this->result))
        {
            $data[] = $record;
        }
        return $data;
    }

    public function free()
    {
        mysqli_free_result($this->result);
    }
}