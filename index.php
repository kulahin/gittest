<?php

    require_once ('config.php');
    require_once ('movies.php');
    require_once ('helper.php');
    require_once ('sql.php');

    global $sql;

    $sql = new sqlme($config['db_host'], $config['db_login'], $config['db_password'], $config['db_name']);

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
        <title>Kinopoisk.ru statistics</title>
        <link href="css/styles.css" type="text/css" rel="stylesheet">
        <link type="text/css" href="css/overcast/jquery-ui-1.8.17.custom.css" rel="stylesheet" />    
        <script src="js/jquery-1.7.1.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="js/jquery-ui-1.8.17.custom.min.js"></script>
        <script src="js/script.js" type="text/javascript"></script>
    </head>
    <body>
        <div class="parent">
            <div class="page_header"><h1>Kinopoisk.ru statistics</h1></div>
            <div id="date"><h2>2012-01-20</h2></div>
            <div id="picker">
                <a onclick="$('#datepicker').focus();"><img src="images/calendar-icon.png" /></a>
                <input id="datepicker" type="text" />
            </div>
            <div id="rating">
                <?php
                    $movies = new Movies;
                    $list = $movies->getListByDate();
                    $movies->showTable($list);
                ?>
            </div>
        </div>
    </body>
</html>
