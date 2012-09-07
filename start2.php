<?php

require_once ('config.php');
require_once ('helper.php');
require_once ('parser.php');
require_once ('sql.php');

global $sql;

set_time_limit($config['time_limit']);

$sql = new sqlme($config['db_host'], $config['db_login'], $config['db_password'], $config['db_name']);

$date = isset($_GET["date"]) ? $_GET["date"] : false;

if (!$date) $date = isset($argv[1]) ? $argv[1] : false;

$parser = new Parser;

if ($parser->start($date)){
    echo "Success";
}else{
    echo "Failed";
}

$sql->Close();

?>