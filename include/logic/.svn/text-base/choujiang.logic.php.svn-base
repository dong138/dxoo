<?php
class ChouJiangLogic {
	/**
	 * 检查抽奖是否有效
	 * @param 产品ID $pid
	 * @param 奖项类型1，2，3 $type
	 */
	function checkCJ($pid,$type){
		//1检查这个奖项的数量是否还有，product表里边取出数量与ticket这个商品的总购物券数量对比
		$p = $this->getOneProduct($pid);
		$num = 0;
		//一等奖
		if($type == 1){
			$num = $p['zk1num'];
		}
		//二等奖
		if($type == 2){
			$num = $p['zk2num'];
		}
		//三等奖
		if($type == 3){
			$num = $p['zk3num'];
		}
		$count = $this->getTicketTotalNunmByPidAndPrizeType($pid,$type);
		if($num - $count >= 1){
			return true;
		}
		return false;
	}
	
	/**
	 * 根据产品ID和奖项类型统计该类型的已中奖数量
	 * @param unknown $pid
	 * @param unknown $prizeType
	 * @return unknown
	 */
	function getTicketTotalNunmByPidAndPrizeType($pid,$prizeType){
		$sql = 'SELECT count(*) as ct FROM ' . table ( 'ticket' ).' WHERE productid = '.$pid .' AND prize_type = '.$prizeType;
// 		$data = dbc ( DBCMax )->query ( $sql )->done ();
		$query = mysql_query($sql);
		$row = mysql_fetch_array($query);
// 		echo ($sql."--".$row['ct']);
		return $row['ct'];
	}
	
	/**
	 * 根据产品ID获取产品
	 * @param unknown $id
	 * @param string $cached
	 * @return Ambigous <boolean, string>|Ambigous <string, boolean>
	 */
	function  getOneProduct($id,$cached = true){
		$ckey = 'choujiang.getone.' . $id;
		$p = $cached ? cached ( $ckey ) : false;
		if ($p)
			return $p;
		$sql = 'SELECT * FROM ' . table ( 'product' ).' WHERE id = '.$id;
		$data = dbc ( DBCMax )->query ( $sql )->limit ( 1 )->done ();
		return cached ( $ckey, $data );
	}
	
	
	/**
	 * 生成购物券并插入数据库
	 * @param 产品ID $pid
	 * @param 订单ID $oid
	 * @param 用户ID $uid
	 * @param 购买数量 $mutis
	 * @param string $idx
	 * @param 详细 $detail
	 */
	function createTicket($pid, $oid, $uid, $mutis = 1,$idx = false,$prize_type){
		$number = $this->__freeNumber ( $number );
		$password = $password ? $password : $this->__random_num ( 3 );
		$guid = is_numeric ( $idx ) ? ($oid . '-' . $idx) : ('USR-' . md5 ( ( string ) microtime ( true ) ));
		$data = array (
				'uid' => $uid,
				'productid' => $pid,
				'orderid' => $oid,
				'guid' => $guid,
				'number' => $number,
				'password' => $password,
				'mutis' => $mutis,
				'status' => TICK_STA_Unused,
				'prize_type' => $prize_type
		);
		dbc ()->SetTable ( table ( 'ticket' ) );
		$id = dbc ()->Insert ( $data );
	}
	/**
	 * 生成购物券账号
	 * @param string $number
	 * @return unknown
	 */
	public function __freeNumber($number = false) {
		$number = $number ? $number : $this->__random_num ( 9 );
		$exists = dbc ( DBCMax )->select ( 'ticket' )->where ( 'number=' . $number )->limit ( 1 )->done ();
		return $exists ? $this->__freeNumber () : $number;
	}
	/**
	 * 生成购物券密码
	 * @param number $length
	 * @return string
	 */
	public function __random_num($length = 12) {
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
	 * 判断是否已经抽奖
	 * @param unknown $pid
	 * @param unknown $uid
	 */
	public function hasChouJiang($pid,$uid){
		$sql = 'SELECT count(*) as ct FROM ' . table ( 'order' ).' WHERE prize_type > 0 and productid = ' . $pid . ' AND userid = ' . $uid;
		$query = mysql_query($sql);
		$row = mysql_fetch_array($query);
		if($row['ct'] == 0){
			return false;
		}
		return true;
	}
	
	/**
	 * 判断是否已经抽奖，返回抽奖信息
	 * @param unknown $pid
	 * @param unknown $uid
	 */
	public function hasChouJiangRecord($pid,$uid){
		$sql = 'SELECT * FROM ' . table ( 'order' ).' WHERE prize_type > 0 and productid = ' . $pid . ' AND userid = ' . $uid;
		$data = dbc ( DBCMax )->query ( $sql )->done ();
		return $data;
	}
	
	public function getPrizePhoneFromTicket($uid){
		$sql = 'SELECT * FROM ' . table ( 'prize_phone' ).' WHERE uid = ' . $uid;
		$data = dbc ( DBCMax )->query ( $sql )->limit ( 1 )->done ();
		return $data;
	}
	
	public function getTicket($uid,$pid){
		$sql = 'SELECT * FROM ' . table ( 'ticket' ).' WHERE uid = ' . $uid.' AND productid = '.$pid;
		$data = dbc ( DBCMax )->query ( $sql )->limit ( 1 )->done ();
		return $data;
	}
}
?>