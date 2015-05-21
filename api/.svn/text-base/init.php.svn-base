<?php
/**
 * Created by PhpStorm.
 * User: lofzefjg
 * Date: 15/3/16
 * Time: 下午3:40
 */
error_reporting(0);
define ( 'ROOT_PATH', '../' );
define ( 'INCLUDE_PATH', ROOT_PATH . "include/" );
define ( 'DB_DRIVER_PATH', INCLUDE_PATH . "db/" );
define ( 'CACHE_PATH', ROOT_PATH . 'cache/' );

require_once '../setting/settings.php';
include_once "ezSQL/ez_sql_core.php";
include_once "ezSQL/ez_sql_mysql.php";
function myDBC()
{
    $config = $GLOBALS['config']['settings'];
    $db_user = $config['db_user'];
    $db_password = $config['db_pass'];
    $db_name = $config['db_name'];
    $db_host = $config['db_host'];
    $db_port = $config['db_port'];
    $db_host = $db_host.':'.$db_port;
    $db_charset = $config['charset'];
    $db = new ezSQL_mysql($db_user,$db_password,$db_name,$db_host,$db_charset);
    return $db;
}
function outputJson($arr)
{
    $result = json_encode($arr);
    echo($result);
    exit();
}
function lookPost()
{
    $str = '';
    foreach($_POST as $key=>$val)
    {
        $str.= $key.'='.$val.'&';
    }
    $arr = array('status'=>'02','msg'=>$str);
    outputJson($arr);
}