<?php
require_once 'OldInit.php';
header('Content-type: text/html; charset=utf8');
$db = getDB();
$db->query("set names utf8");
//团购券号码
if(isset($_POST['username'])){
	$username = $_POST['username'];
}else{
	$str = json_encode ( array (
			'status' => '01',
			'msg' => '团购券号码为空'
	) );
	$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
	exit($aa);
}
//团购券密码
if(isset($_POST['password'])){
	$password = $_POST['password'];
}else{
	$str = json_encode ( array (
			'status' => '02',
			'msg' => '团购券密码为空'
	) );
	$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
	exit($aa);
}
//更多团购券
if(isset($_POST['moreticket'])){
	$moreticket = $_POST['moreticket'];
}else{
	$moreticket = "";
}
//商户id
if(isset($_POST['sellerid'])){
	$sellerid = $_POST['sellerid'];
}else{
	$str = json_encode ( array (
			'status' => '02',
			'msg' => '商户id为空'
	) );
	$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
	exit($aa);
}
//验证员id
if(isset($_POST['clerkaid'])){
	$clerkaid = $_POST['clerkaid'];
}else{
	$str = json_encode ( array (
			'status' => '02',
			'msg' => '员工类型为空'
	) );
	$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
	exit($aa);
}
$config = $GLOBALS['config']['settings'];
$site_url = $config['site_url'];
$url=$site_url.'/?mod=list&code=appCK&number='.$username.'&password='.$password.'&morecoupon='.$moreticket.'&sellerid='.$sellerid.'&clerkaid='.$clerkaid;
$ret=file_get_contents($url);
echo $ret;
?>