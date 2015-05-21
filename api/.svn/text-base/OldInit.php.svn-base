<?php
/**
 * Created by PhpStorm.
 * User: lofzefjg
 * Date: 15/3/23
 * Time: 上午10:46
 */
require_once '../setting/settings.php';
error_reporting(0);

function getDB()
{
    $config = $GLOBALS['config']['settings'];
    $db_user = $config['db_user'];
    $db_password = $config['db_pass'];
    $db_name = $config['db_name'];
    $db_host = $config['db_host'];
    $db_port = $config['db_port'];
    $db_host .= ':'.$db_port;
    $dsn = "mysql:host=".$db_host.";dbname=".$db_name;
    $db = new PDO($dsn, $db_user, $db_password);
    return $db;
}