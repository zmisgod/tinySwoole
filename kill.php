<?php
$pid = file_get_contents(__DIR__.'/Log/Server/pid.pid');
if(!empty($pid)) {
    try{
        swoole_process::kill($pid);
    }catch (\Exception $e) {
        echo $e->getMessage().PHP_EOL;
        echo 'swoole stop failed';
    }
    echo 'successful';
}else{
    echo 'are you start this framework?';
}