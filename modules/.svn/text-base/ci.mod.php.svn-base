<?php
/**
 * 公共接口
 * CommonInterface
 * @author max
 *
 */
class ModuleObject extends MasterObject {
	function ModuleObject($config) {
		$this->MasterObject ( $config );
		$runCode = Load::moduleCode ( $this );
		$this->$runCode ();
	}
	/**
	 * 发送短信
	 * 斐讯
	 * @param unknown $phone 手机号
	 * @param unknown $msg 信息内容
	 */
	function sms(){
		$phone = get ( 'phone', 'txt' );
		if($phone == null || strlen($phone) <= 0){
			//手机号不能为空
			$ops = array (
				'status' => '11'
			);
			exit (jsonEncode ( $ops ));
		}
		if(!preg_match("/^1[3458][0-9]{9}$/",$phone)){
			//手机号格式不对
			$ops = array (
				'status' => '22'
			);
			exit (jsonEncode ( $ops ));
		}
		$msg = get ( 'content', 'txt' );
		if($msg == null || strlen($msg) <= 0){
			//验证码不允许为空
			$ops = array (
				'status' => '33'
			);
			exit (jsonEncode ( $ops ));
		}
		if(!preg_match("/^\d{5,6}$/",$msg)){
			//验证码格式不正确
			$ops = array (
				'status' => '44'
			);
			exit (jsonEncode ( $ops ));
		}
		//发送
		logic ( 'ci' )->send($phone,$msg.'（WiFi登陆密码,可永久使用,请妥善保存）。');
		$ops = array (
				'status' => '00'
			);
		exit (jsonEncode ( $ops ));
	}
	
	/**
	 * 引流
	 * 斐讯
	 * @param unknown $phone 手机号
	 */
	function drainage(){
		$phone = get ( 'phone', 'txt' );
		if($phone == null || strlen($phone) <= 0){
			//手机号不能为空
			$ops = array (
				'status' => '11'
			);
			exit (jsonEncode ( $ops ));
		}
		if(!preg_match("/^1[3458][0-9]{9}$/",$phone)){
			//手机号格式不对
			$ops = array (
				'status' => '22'
			);
			exit (jsonEncode ( $ops ));
		}
		if(!logic ( 'ci' )->ckPhone($phone)){
			//手机号已经注册
			$ops = array (
				'status' => '00'
			);
			exit (jsonEncode ( $ops ));
		}
		logic ( 'ci' )->drainage($phone);
		$ops = array (
				'status' => '00'
			);
		exit (jsonEncode ( $ops ));
	}

	function Qrcode(){
		$ticket_id = get ( 'ticketid', 'int' );
		if(!$ticket_id){
			return false;
		}
		$img_path=qrcode($ticket_id,1);
		ob_clean();  //关键代码，防止出现'图像因其本身有错无法显示'的问题。
		Header("Content-type: image/jpg");
		$filename = $img_path;
		$handle = fopen($filename, "rb");
		$filesize = filesize($filename);
		$contents = fread($handle, $filesize);
		echo $contents;
		fclose($handle);
	}
	
}
?>