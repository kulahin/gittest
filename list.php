<?php
    require_once ('config.php');
    require_once ('movies.php');
    require_once ('helper.php');
    require_once ('sql.php');

    global $sql;

    $sql = new sqlme($config['db_host'], $config['db_login'], $config['db_password'], $config['db_name']);

    $date = isset($_GET["date"]) ? $_GET["date"] : date("Y-m-d");
    $movies = new Movies;
    $list = $movies->getListByDate($date);
    $movies->showTable($list);
?>