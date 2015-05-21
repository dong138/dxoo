<?php
require_once 'OldInit.php';
header('Content-type: text/html; charset=utf8');
$db = getDB();
$db->query("set names utf8");
$op = $_GET['op'];

if($op == "sellerLogin"){
	//用户名
	if(isset($_POST['username'])){
		$username = $_POST['username'];
	}else{
		$str = json_encode ( array (
				'status' => '01',
				'msg' => '用户名不允许为空'
		) );
		$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
		exit($aa);
	}
	//密码
	if(isset($_POST['password'])){
		$password = $_POST['password'];
	}else{
		$str = json_encode ( array (
				'status' => '02',
				'msg' => '密码不允许为空'
		) );
		$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
		exit($aa);
	}
	//type:1商家2员工
	if(isset($_POST['type'])){
		$tp = $_POST['type'];
	}else{
		$str = json_encode ( array (
				'status' => '03',
				'msg' => '类型不允许为空'
		) );
		$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
		exit($aa);
	}
	if($username == null || $username == ''){
		$str = json_encode ( array (
				'status' => '04',
				'msg' => '用户名不允许为空'
		) );
		$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
		exit($aa);
	}
	if($password == null || $password == ''){
		$str = json_encode ( array (
				'status' => '05',
				'msg' => '密码不允许为空'
		) );
		$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
		exit($aa);
	}
	if($tp == 1){
		//商家
		$exp = "/^[a-z'0-9]+([._-][a-z'0-9]+)*@([a-z0-9]+([._-][a-z0-9]+))+$/i";
		if (preg_match ( $exp, $username )) {
			$type = 'email';
		}else if(preg_match("/^1[345678][0-9]{9}$/",$username)){
			$type = 'phone';
		}else{
			$type = 'username';
		}
		$sql = "SELECT * FROM yssystem_members WHERE {$type} = '{$username}'";
		$sth = $db->prepare($sql);
		$sth->execute();
		$rs = $sth->fetchAll();
		if (count($rs) <= 0) {
			$str = json_encode ( array (
					'status' => '05',
					'msg' => '账户不存在'
			) );
			$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
			exit($aa);
		} else {
			$row = $rs[0];
			if ($row ['password'] == md5($password)) {
				//登录成功，查找商户信息
				$sql1 = "SELECT * FROM ystttuangou_seller WHERE userid = {$row['uid']}";
				$sth1 = $db->prepare($sql1);
				$sth1->execute();
				$rs1 = $sth1->fetchAll();
				if(count($rs1) <= 0){
					$str = json_encode ( array (
							'status' => '05',
							'msg' => '商户不存在'
					) );
					$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
					exit($aa);
				}else{
					$seller = $rs1[0];
					$user = array();
					//用户表对应的id
					$user['uid'] = $row['uid'];
					$user['truename'] = $row['truename'];
					$user['sellerid'] = $seller['id'];
					$user['sellername'] = $seller['sellername'];
					$sql2 = "SELECT id,name FROM ystttuangou_clerka WHERE type = 1 and sellerid = ". $seller['id'];
					$sth2 = $db->prepare($sql2);
					$sth2->execute();
					$rs2 = $sth2->fetchAll();
					$str = json_encode ( array (
							'status' => '00',
							'msg' => $user,
							'clerka' => $rs2
					) );
					$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
					exit($aa);
				}
			}else{
				$str = json_encode ( array (
						'status' => '06',
						'msg' => '密码错误'
				) );
				$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
				exit($aa);
			}
		}
	}else{
		//员工
		$sql = "SELECT * FROM ystttuangou_clerka WHERE username = '{$username}'";
		$sth = $db->prepare($sql);
		$sth->execute();
		$rs = $sth->fetchAll();
		if (count($rs) <= 0) {
			$str = json_encode ( array (
					'status' => '06',
					'msg' => '账户不存在'
			) );
			$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
			exit($aa);
		} else {
			$row = $rs[0];
			if ($row ['type'] == 2) {
				$str = json_encode ( array (
						'status' => '06',
						'msg' => '您的账号已被老板禁用'
				) );
				$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
				exit($aa);
			}
			if ($row ['password'] == md5($password)) {
				//密码正确
				$sql1 = "SELECT * FROM ystttuangou_seller WHERE id = {$row['sellerid']}";
				$sth1 = $db->prepare($sql1);
				$sth1->execute();
				$rs1 = $sth1->fetchAll();
				if(count($rs1) <= 0){
					$str = json_encode ( array (
							'status' => '06',
							'msg' => '您没有所属商家'
					) );
					$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
					exit($aa);
				}else{
					$seller = $rs1[0];
					$user = array();
					//员工表对应的id
					$user['uid'] = $row['id'];
					$user['truename'] = $row['name'];
					$user['sellerid'] = $seller['id'];
					$user['sellername'] = $seller['sellername'];
					
					$str = json_encode ( array (
							'status' => '00',
							'msg' => $user
					) );
					$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
					exit($aa);
				}
			}else{
				$str = json_encode ( array (
						'status' => '06',
						'msg' => '密码错误'
				) );
				$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
				exit($aa);
			}
		}
	}
}
    //验证
//     if($op == "sellerCheck"){
//     	if(isset($_POST['username'])){
//     		$username = $_POST['username'];
//     	}else{
//     		exit ( json_encode ( array (
//     				'status' => '01',
//     				'msg' => '团购券号码不允许为空'
//     		) ) );
//     	}
//     	if(isset($_POST['password'])){
//     		$password = $_POST['password'];
//     	}else{
//     		exit ( json_encode ( array (
//     				'status' => '02',
//     				'msg' => '团购券密码不允许为空'
//     		) ) );
//     	}
//    		if(isset($_POST['type'])){
//     		$type = $_POST['type'];
//     	}else{
//     		exit ( json_encode ( array (
//     				'status' => '03',
//     				'msg' => '验证失败003'
//     		) ) );
//     	}
//     	if(isset($_POST['clerkaid'])){
//     		$clerkaid = $_POST['clerkaid'];
//     	}else{
//     		exit ( json_encode ( array (
//     				'status' => '04',
//     				'msg' => '验证失败004'
//     		) ) );
//     	}
//     	if(isset($_POST['sellerid'])){
//     		$sellerid = $_POST['sellerid'];
//     	}else{
//     		exit ( json_encode ( array (
//     				'status' => '05',
//     				'msg' => '验证失败005'
//     		) ) );
//     	}
// 		if($username == null || $username == ''){
// 			exit ( json_encode ( array (
// 					'status' => '04',
// 					'msg' => '账号不允许为空'
// 			) ) );
// 		}
// 		if($password == null || $password == ''){
// 			exit ( json_encode ( array (
// 					'status' => '05',
// 					'msg' => '密码不允许为空'
// 			) ) );
// 		}
// 		$sql  = "select a.ticketid as id,a.orderid as orderid,a.status as status,b.sellerid as sellerid, b.name as pname,c.productprice as price,c.productnum as num,c.totalprice as totalprice from ystttuangou_ticket a,ystttuangou_product b,ystttuangou_order c where a.productid = b.id and a.orderid = c.orderid and b.id = c.productid and a.number = '{$username}' and a.password = '{$password}'";
// 		$sth = $db->prepare($sql);
// 		$sth->execute();
// 		$rs = $sth->fetchAll();
// 		if (count($rs) <= 0) {
// 			exit ( json_encode ( array (
// 					'status' => '07',
// 					'msg' => '该团购券不存在'
// 			) ) );
// 		}else{
// 			$t = $rs[0];
// 			if($t['sellerid'] != $sellerid){
// 				exit ( json_encode ( array (
// 						'status' => '08',
// 						'msg' => '对不起，不是您的商品,'.$t['pname']
// 				) ) );
// 			}
// 			if($t['status'] == 1){
// 				exit ( json_encode ( array (
// 					'status' => '09',
// 					'msg' => '已经被使用,'.$t['pname']
// 				) ) );
// 			}
// 			if($t['status'] == 2){
// 				exit ( json_encode ( array (
// 						'status' => '09',
// 						'msg' => '已经过期,'.$t['pname']
// 				) ) );
// 			}
// 			if($t['status'] == 3){
// 				exit ( json_encode ( array (
// 						'status' => '09',
// 						'msg' => '已经作废,'.$t['pname']
// 				) ) );
// 			}
// 			if($type == 1){
// 				//商户
// 				$sql1  = "UPDATE ystttuangou_ticket SET status = 1 , sellerid = ".$sellerid." , usetime = '".date('Y-m-d H:i:s')."'  WHERE ticketid = {$t['id']}";
// 			}
// 			if($type == 2){
// 				//员工
// 				$sql1  = "UPDATE ystttuangou_ticket SET status = 1 , sellerid = ".$sellerid." , clerkid = ".$clerkaid." , usetime = '".date('Y-m-d H:i:s')."'  WHERE ticketid = {$t['id']}";
// 			}
// 			//一切审核结束，修改状态
// 			$stmt1 = $db->prepare($sql1);
// 			$stmt1->execute();
// 			$cc = $stmt1->rowCount();
// 			if($cc > 0){
// 				exit ( json_encode ( array (
// 						'status' => '00',
// 						'msg' => $t['pname']
// 				) ) );
// 			}else{
// 				exit ( json_encode ( array (
// 						'status' => '10',
// 						'msg' => $t['pname']
// 				) ) );
// 			}
// 		}
//     }
    
    //查询统计
    if($op == "findAndTJ"){
    	//分页
    	if(isset($_POST['page'])){
    		$page = $_POST['page'];
    	}else{
    		$page = 1;
    	}
    	//用户类型
    	if(isset($_POST['type'])){
    		$type = $_POST['type'];
    	}else{
    		$str = json_encode ( array (
    				'status' => '01',
    				'msg' => '查询失败'
    		) );
    		$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
    		exit($aa);
    	}
    	//id，商家id
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
    	//id，员工id
    	//-1自己，0全部，其他员工
    	if(isset($_POST['clerkaid'])){
    		$clerkaid = $_POST['clerkaid'];
    	}else{
    		$str = json_encode ( array (
    				'status' => '03',
    				'msg' => '查询失败'
    		) );
    		$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
    		exit($aa);
    	}
    	if(isset($_POST['starttime'])){
    		$starttime = $_POST['starttime'];
    	}
    	if(isset($_POST['endtime'])){
    		$endtime = $_POST['endtime'];
    	}
    	$where = " ";
    	if($type == 1 && $sellerid){
    		if($clerkaid == -1){
    			//自己
    			$where .= " and a.sellerid = {$sellerid} and (a.clerkaid is null or a.clerkaid = 0) ";
    		}else if($clerkaid == 0){
    			 //全部
    			$where .= " and a.sellerid = {$sellerid}";
    		}else{
    			//其他
    			$where .= " and a.clerkaid = {$clerkaid}";
    		}
    	}else if($type == 2 && $clerkaid && $sellerid){
    		//员工，只能查自己
    		$where .= " and a.clerkaid = {$clerkaid}";
    	}else{
    		$str = json_encode ( array (
    				'status' => '04',
    				'msg' => '查询失败'
    		) );
    		$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
    		exit($aa);
    	}
    	if(isset($_POST['starttime']) && $starttime && isset($_POST['endtime']) && $endtime){
    		if($starttime > $endtime){
    			$str = json_encode ( array (
    					'status' => '05',
    					'msg' => "起始时间应小于结束时间"
    			) );
    			$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
    			exit($aa);
    		}
    		$where .= " and a.usetime >= '".$starttime."' and a.usetime <= '".$endtime."' ";
    	}
    	$sql = "select count(*) as tnum,sum(a.mutis * c.productprice) as tprice from ystttuangou_ticket a,ystttuangou_product b,ystttuangou_order c where a.productid = b.id and a.orderid = c.orderid and b.id = c.productid and a.status = 1 and b.sellerid = {$sellerid} {$where}";
    	$sth = $db->prepare($sql);
    	$sth->execute();
    	$rs = $sth->fetchAll();
    	if($rs[0]['tnum'] <= 0){
    		$str = json_encode ( array (
    				'status' => '00',
    				'msg' => ""
    		) );
    		$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
    		exit($aa);
    	}
    	$sql1 = "select a.number as number,a.ticketid as id,a.usetime as verifyTime,a.orderid as orderId,b.name as name,c.productprice as price,a.mutis as num,a.mutis*c.productprice as totalPrice from ystttuangou_ticket a,ystttuangou_product b,ystttuangou_order c where a.productid = b.id and a.orderid = c.orderid and b.id = c.productid and a.status = 1 and b.sellerid = {$sellerid} {$where} order by a.usetime desc limit ".(($page - 1)*10).",10";
    	$sth1 = $db->prepare($sql1);
    	$sth1->execute();
    	$rs1 = $sth1->fetchAll();
    	$resData = array(
    		'tnum'=>$rs[0]['tnum'],
    		'tprice'=>$rs[0]['tprice'],
    		'list'=>$rs1
    	);
    	$str = json_encode ( array (
    			'status' => '00',
    			'msg' => $resData
    	) );
    	$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
    	exit($aa);
    }
?>