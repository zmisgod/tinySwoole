<?php
$pid = file_get_contents(__DIR__.'/Log/Server/pid.pid');
if(!empty($pid)) {
    swoole_process::kill($pid);
    echo 'successful';
}else{
    echo 'are you start this framework?';
}