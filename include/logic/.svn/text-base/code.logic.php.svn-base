<?php
/**
 * 登录和注册动态码
 * 登录日志
 * 发送短信
 * @author max
 * code.logic.php
 */
class CodeLogic {
	/**
	 * 生成动态码：使用6位
	 * @param number $length
	 * @return string
	 */
	public function __random_num($length = 6) {
		$length = ( int ) $length;
		$loops = ceil ( $length / 3 );
		$string = '';
		for($i = 0; $i < $loops; $i ++) {
			$string .= ( string ) mt_rand ( 100, 999 );
		}
		$string = substr ( $string, 0, $length );
		return $string;
	}
	
	/**
	 * 创建一条动态码记录
	 * @param $phone
	 * @param $type 动态码类型1:登录2:注册
	 */
	public function __createCode($phone,$type){
		$c = $this->getOne($phone, $type);
		if($c){
			dbc ( DBCMax )->update ( 'code' )->data ( 'stime = '.time())->data ( 'etime = '.strtotime('+1 day'))->data ( 'pcode = '.($this->__random_num(6)) )->data ( 'verified = 2')->data ( 'vtime = 0')->where ( 'phone = '.$phone)->where ( 'type = '.$type)->done ();
		}else{
			$data = array (
					'phone' => $phone,
					'pcode' => $this->__random_num(6),
					'stime' => time(),//开始时间
					'etime' => strtotime('+1 day'),//过期时间
					'vtime' => '0',//验证时间
					'verified' => '2',//1:已经验证2:未验证
					'type' => $type//1:登录2:注册
			);
			dbc ( DBCMax )->insert ( 'code' )->data ( $data )->done ();
		}
	}
	
	/**
	 * 验证动态码
	 * @param unknown $phone
	 * @param unknown $code
	 * @param unknown $type 动态码类型1:登录2:注册
	 */
	public function __verifyCode($phone,$code,$type){
		$c = $this->getOne($phone, $type);
		if($c){
			if($c['pcode'] == $code){
				dbc ( DBCMax )->update ( 'code' )->data ( 'verified = 1')->data ( 'vtime = '.time())->where ( 'phone = '.$phone)->where ( 'type = '.$type)->done ();
				return true;
			}
			return false;
		}else{
			return false;
		}
	}
	
	/**
	 * 获取一条code
	 * @param unknown $phone
	 * @param unknown $type
	 * @return unknown
	 */
	public function getOne($phone,$type){
		$sql = 'SELECT * FROM ' . table ( 'code' ).' WHERE phone = '.$phone.' and type = '.$type;
		$data = dbc ( DBCMax )->query ( $sql )->limit ( 1 )->done ();
		return $data;
	}
	
	/*******************************************/
	/**
	 * 创建登录记录
	 * @param unknown $ip 客户端IP global.func.php里面有client_ip()方法获取IP
	 * @param unknown $loginType 登录类型1:手机2:邮箱3:用户名4:快速
	 * @param unknown $loginAccount 登录账号
	 * @param string $code 登录动态码
	 */
	public function __createLoginLog($ip,$loginType,$loginAccount,$code = ''){
		$data = array (
				'login_ip' => $ip,
				'login_type' => $loginType,//1:手机2:邮箱3:用户名4:快速
				'login_account' => $loginAccount,
				'login_code' => $code,//验证码
				'login_time' => time()//登录时间
		);
		dbc ( DBCMax )->insert ( 'login_log' )->data ( $data )->done ();
	}
	
	/*******************************************/
	/**
	 * 生成用户名
	 * 三个字母＋10个数字
	 * @return string
	 */
	public function __createUsername(){
		$arr = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","P","Q","R","S","T","U","V","W","X","Y","Z","a","b","c","d","e","f","g","h","i","j","k","m","n","p","r","s","t","u","v","w","x","y","z");
		$username = $arr[mt_rand(0,47)].$arr[mt_rand(0,47)].$arr[mt_rand(0,47)].time();
		$sql = 'SELECT username FROM ' . table ( 'members' ).' WHERE username = "'.$username.'"';
		$data = dbc ( DBCMax )->query ( $sql )->done ();
		if($data){
			//已经存在
			$this->__randomUsername();
		}
		return $username;
	}
	
	/********************************************/
	/**
	 * 发送短信
	 * @param unknown $phone 手机号
	 * @param unknown $msg 信息内容
	 */
	public function __send($phone,$msg){
		logic ( 'push' )->addi ( 'sms', $phone, array (
			'content' => $msg
		) );
	}
}
?>