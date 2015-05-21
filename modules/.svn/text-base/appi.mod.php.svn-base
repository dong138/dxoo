<?php
/**
 * 手机接口
 * @author max
 *
 */
class ModuleObject extends MasterObject {
	function ModuleObject($config) {
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	
	function sellerLogin(){
		echo ("000");
// 		$username = post('username','txt');
// 		$password = post('password','txt');
// 		if($username == null || $username == ''){
// 			exit ( jsonEncode ( array (
// 					'status' => '01',
// 					'msg' => '用户名不允许为空'
// 			) ) );
// 		}
// 		if($password == null || $password == ''){
// 			exit ( jsonEncode ( array (
// 					'status' => '02',
// 					'msg' => '密码不允许为空'
// 			) ) );
// 		}
// 		if(check_email ( $username )){
// 			$type = 'email';
// 		}else if(check_phone ( $username )){
// 			$type = 'phone';
// 		}else{
// 			$type = 'username';
// 		}
// 		$sql = "Select * FROM " . TABLE_PREFIX . 'system_members' . " WHERE {$type}='{$username}'";
// 		$user = dbc ( DBCMax )->query ( $sql )->limit ( 1 )->done ();
// 		if($user){
// 			if ($user ['password'] == md5($password)) {
// 				exit ( jsonEncode ( array (
// 						'status' => '00',
// 						'msg' => $user
// 				) ) );
// 			}else{
// 				exit ( jsonEncode ( array (
// 						'status' => '03',
// 						'msg' => '密码错误'
// 				) ) );
// 			}
// 		}else{
// 			exit ( jsonEncode ( array (
// 					'status' => '04',
// 					'msg' => '账户不存在'
// 			) ) );
// 		}
		
		
	}
	
	function sellerVerify(){
	
	}
}
?>