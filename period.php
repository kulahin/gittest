<?php
    require_once ('config.php');
    list($y,$m,$dy) = explode("-", $config['period_start']);
    $time_start = mktime(0,0,0,$m,$d,$y);
    list($y,$m,$d) = explode("-", $config['period_finish']);
    $time_finish = mktime(0,0,0,$m,$d,$y);
    for ($i = $time_start; $i <= $time_finish; $i+=24*60*60) {
        $date = date("Y-m-d", $i);
        system('/usr/bin/php-cgi /home/symfonyt/www/start.php '.$date.' &');
        sleep(20);
    }
?>
