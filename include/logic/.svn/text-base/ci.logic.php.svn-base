<?php
/**
 * CommonInterfaceLogic
 * @author max
 *
 */
class CiLogic {
	/**
	 * 引流
	 */
	public function drainage($phone){
		$username = logic ( 'code' )->__createUsername();
		$data = array (
				'username' => $username,
				'truename' => $username,
				'phone' => (is_numeric ( $phone ) ? $phone : ''),
				'role_id' => '0',
				'role_type' => 'normal',
				'checked' => 1,
				'ucuid' => $extend ['ucuid'],
				'regip' => client_ip (),
				'lastip' => client_ip (),
				'regdate' => time (),
				'register_type' => 4
		);
		dbc ( DBCMax )->insert ( 'members' )->data ( $data )->done ();
	}
	
	public function ckPhone($phone){
		$sql = 'SELECT phone FROM ' . table ( 'members' ).' WHERE phone = "'.$phone.'"';
		$data = dbc ( DBCMax )->query ( $sql )->done ();
		if($data){
			return false;
		}
		return true;
	}
	
	
	/**
	 * 发送短信
	 * @param unknown get phone 手机号
	 * @param unknown get content 验证码
	 * @param unknown get type 类型
	 */
	public function sms_code($phone,$msg,$tp){
		//手机号
		if($phone == null || strlen($phone) <= 0){
			//手机号不能为空
// 			$ops = array (
// 					'status' => '11'
// 			);
// 			exit (jsonEncode ( $ops ));
			return "11";
		}
		if(!preg_match("/^1[3458][0-9]{9}$/",$phone)){
			//手机号格式不对
// 			$ops = array (
// 					'status' => '22'
// 			);
// 			exit (jsonEncode ( $ops ));
			return "22";
		}
		//验证码
		if($msg == null || strlen($msg) <= 0){;
			//验证码不允许为空
// 			$ops = array (
// 					'status' => '33'
// 			);
// 			exit (jsonEncode ( $ops ));
			return "33";
		}
		if(!preg_match("/^\d{5,6}$/",$msg)){
			//验证码格式不正确
// 			$ops = array (
// 					'status' => '44'
// 			);
// 			exit (jsonEncode ( $ops ));
			return "44";
		}
		//类型
		if($tp <= 0){
			//验证码格式不正确
// 			$ops = array (
// 					'status' => '55'
// 			);
// 			exit (jsonEncode ( $ops ));
			return "55";
		}
		//验证码 类型
		if($tp == "1"){
			$msg.="（登录验证码。）";
		}
		if($tp == "2"){
			$msg.="（注册验证码。）";
		}
		if($tp == "3"){
			$msg.="（免登陆购买验证码。）";
		}
		if($tp == "4"){
			$msg.="（换绑手机验证码）";
		}
		if($tp == "5"){
			$msg.="（换绑邮箱验证码）";
		}
		//发送
		$this->send($phone,$msg);
// 		$ops = array (
// 				'status' => '00'
// 		);
// 		exit (jsonEncode ( $ops ));
		return "00";
	}
	
	/**
	 * 发送信息
	 * @param unknown $phone
	 * @param unknown $msg
	 */
	public function send($phone,$msg){
		logic ( 'push' )->addi ( 'sms', $phone, array (
			'content' => $msg
		) );
	}
}
?>