<?php
require_once 'OldInit.php';
header('Content-type: text/html; charset=utf8');
$db = getDB();
$db->query("set names utf8");
//商家验证
if(isset($_POST['username'])){
	$username = $_POST['username'];
}else{
	$str = json_encode ( array (
			'status' => '01',
			'msg' => '团购券号码不允许为空'
	) );
	$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
	exit($aa);
}
if(isset($_POST['password'])){
	$password = $_POST['password'];
}else{
	$str = json_encode ( array (
			'status' => '02',
			'msg' => '团购券密码不允许为空'
	) );
	$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
	exit($aa);
}
if(isset($_POST['sellerid'])){
	$sellerid = $_POST['sellerid'];
}else{
	$str = json_encode ( array (
			'status' => '02',
			'msg' => '查询失败'
	) );
	$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
	exit($aa);
}
$sql = "select a.ticketid,a.orderid,a.usetime,a.mutis,a.uid,a.number,a.password,a.status,b.id as pid,b.sellerid as sellerid,b.name as pname,b.perioddate as perioddate from ystttuangou_ticket a left join ystttuangou_product b on a.productid = b.id where a.number = '{$username}'";
$sth = $db->prepare($sql);
$sth->execute();
$rs = $sth->fetchAll();
if (count($rs) <= 0) {
	$str = json_encode ( array (
			'status' => '03',
			'name' => '',
			'msg' => '该团购券不存在'
	) );
	$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
	exit($aa);
}else{
	$t = $rs[0];
	if($t['sellerid'] != $sellerid){
		$str = json_encode ( array (
				'status' => '04',
				'name' => $t['pname'],
				'msg' => '不是您的团购券'
		) );
		$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
		exit($aa);
	}
	if ($t['perioddate'] < time ()) {
		$sql = "update ystttuangou_ticket set `status` = :sta where ticketid = ".$t['ticketid'];
		$stmt = $db->prepare($sql);
		$ary = array (
			':sta' => 2
		);
		$stmt->execute($ary);
		$str = json_encode ( array (
				'status' => '05',
				'name' => $t['pname'],
				'msg' => '团购券已过期'
		) );
		$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
		exit($aa);
	}
	if($t['password'] != $password){
		$str = json_encode ( array (
				'status' => '05',
				'name' => $t['pname'],
				'msg' => '团购券密码不真确'
		) );
		$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
		exit($aa);
	}
	if($t['status'] == 1){
		$str = json_encode ( array (
				'status' => '06',
				'name' => $t['pname'],
				'msg' => '团购券已经在'.$t['usetime'].'被使用'
		) );
		$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
		exit($aa);
	}
	if($t['status'] == 2){
		$str = json_encode ( array (
				'status' => '07',
				'name' => $t['pname'],
				'msg' => '团购券已经过期'
		) );
		$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
		exit($aa);
	}
	if($t['status'] == 3){
		$str = json_encode ( array (
				'status' => '08',
				'name' => $t['pname'],
				'msg' => '团购券已经作废'
		) );
		$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
		exit($aa);
	}
	$sql = "select ticketid,number from ystttuangou_ticket where productid = '".$t['pid']."' and uid = '".$t['uid']."' and `status` = 0 and number <> '{$username}'";
	$sth = $db->prepare($sql);
	$sth->execute();
	$rs1 = $sth->fetchAll();
	$sql = "select * from attrs_order where sign = '".$t['orderid']."'";
	$sth = $db->prepare($sql);
	$sth->execute();
	$rs2 = $sth->fetchAll();
	$attr = "";
	if(count($rs2) > 0){
		$data = unserialize($rs2[0]['data']);
		foreach ($data as $cat_id => $cat){
			$attr.=$cat['name'] . ':' . $cat['attr']['name'].";";
		}
	}
	$name = "";
	if($attr){
		$name.=$t['pname'].'x'.$t['mutis'].",".$attr;
	}else{
		$name.=$t['pname'].'x'.$t['mutis'];
	}
	$str = json_encode ( array (
			'status' => '00',
			'name' => $name,
			'msg' => '团购券可以使用',
			'lists' => $rs1
	) );
	$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
	exit($aa);
}
?>