<?php

/**
 * 逻辑区：抽奖相关
 * @package logic
 * @name prize.logic.php
 * @version 1.0
 */
class PrizeLogic {
	public function allCount($pid, $vird = true) {
		$r = dbc ( DBCMax )->select ( 'prize_ticket' )->in ( 'COUNT(1) AS PZSCOUNT' )->where ( 'pid=' . $pid )->limit ( 1 )->done ();
		if ($vird) {
			$pSrc = logic ( 'product' )->SrcOne ( $pid );
			$r ['PZSCOUNT'] += $pSrc ['virtualnum'];
		}
		return $r ['PZSCOUNT'];
	}
	public function sigCount($where) {
		$r = dbc ( DBCMax )->select ( 'prize_ticket' )->in ( 'COUNT(1) AS PZSCOUNT' )->where ( $where )->limit ( 1 )->done ();
		return $r ['PZSCOUNT'];
	}
	public function Query($where, $limit = 1) {
		return dbc ( DBCMax )->select ( 'prize_ticket' )->where ( $where )->limit ( $limit )->done ();
	}
	public function GetList($pid, $uid = false, $limit = false, $order = 'number.ASC', $pager = false) {
		$q = dbc ( DBCMax )->select ( 'prize_ticket' )->where ( 'pid=' . $pid );
		$uid && $q->where ( 'uid=' . $uid );
		$limit && $q->limit ( $limit );
		$order && $q->order ( $order );
		return $pager ? dbc ( DBCMax )->query ( page_moyo ( $q->sql () ) )->done () : $q->done ();
	}
	public function GetPhone($uid) {
		/*
		 * $q = dbc ( DBCMax )->select ( 'prize_phone' )->where ( array ( $key => $val ) ); $vfd && $q->where ( 'vftime>0' ); return $q->limit ( 1 )->done ();
		 */
		$sql = "SELECT * FROM " . table ( 'members' )." where uid=".$uid;
		return dbc ( DBCMax )->query ( $sql )->limit ( 1 )->done ();
	}
	public function InizTicket($pid, $uid) {
		$product = logic ( 'product' )->BuysCheck ( $pid );
		if (isset ( $product ['false'] )) {
			return $product ['false'];
		}
		$phone = $this->GetPhone ( 'uid', $uid, 'vfd' );
		if (! $phone) {
			return '您的手机号码还未验证，不能参与活动！';
		}
		$ordCount = logic ( 'order' )->Count ( 'productid=' . $pid . ' AND userid=' . $uid );
		
		if (( int ) $ordCount == 0) {
			$order = logic ( 'order' )->GetFree ( $uid, $pid );
			$order ['productnum'] = 1;
			$order ['productprice'] = 0;
			$order ['extmsg'] = '抽奖用户IP地址：' . client_ip ();
			$order ['pay'] = ORD_PAID_Yes;
			$order ['process'] = 'TRADE_FINISHED';
			$order ['status'] = ORD_STA_Normal;
			logic ( 'order' )->Update ( $order ['orderid'], $order );
		}
		$prizes = $this->GetList ( $pid, $uid );
		if (! $prizes) {
			$this->CreateTicket ( $pid, $uid );
			$this->__finder ( $pid, $uid );
		}
		return true;
	}
	public function CreateTicket($pid, $uid, $remark = '参与活动', $nums = 1) {
		$lastTic = $this->GetList ( $pid, false, 1, 'number.DESC' );
		if (! $lastTic) {
			$product = logic ( 'product' )->GetOne ( $pid );
			$numLast = $product ['succ_buyers'];
			$numLast -= 1;
		} else {
			$numLast = $lastTic ['number'];
		}
		$numNew = $numLast + 1;
		$data = array (
				'pid' => $pid,
				'uid' => $uid,
				'number' => $numNew,
				'remark' => $remark,
				'upstime' => time () 
		);
		$iid = dbc ( DBCMax )->insert ( 'prize_ticket' )->data ( $data )->done ();
		return $iid ? $data : false;
	}
	public function S2Phone($phone, $uid = 0) {
		$res = $this->GetPhone ( 'phone', $phone );
		$resID = 0;
		$vfCode = '';
		$uid === 0 && $uid = user ()->get ( 'id' );
		
		$userData = array (
				'uid' => $uid,
				'phone' => $phone 
		);
		if ($res) {
			if ($res ['vftime'] > 0) {
				if ($res ['uid'] != $uid) {
					return '此手机号码已经被其他用户绑定！';
				} else {
					return '此手机号码已经绑定，无需再次绑定！';
				}
			}
			if ($res ['uid'] == $uid && $res ['vfcode'] != '') {
				$vfCode = $res ['vfcode'];
			}
			$resID = $res ['id'];
		} else {
			$res = $this->GetPhone ( 'uid', $uid );
			if ($res) {
				if ($res ['vftime'] > 0) {
					return '您已经绑定手机号码：' . $res ['phone'] . '，无需再次绑定！';
				} else {
					dbc ( DBCMax )->delete ( 'prize_phone' )->where ( array (
							'id' => $res ['id'] 
					) )->done ();
				}
			}
			$resID = dbc ( DBCMax )->insert ( 'prize_phone' )->data ( array_merge ( $userData, array (
					'vftime' => 0 
			) ) )->done ();
		}
		if ($vfCode == '') {
			$vfCode = $this->__random_num ( 5 );
			dbc ( DBCMax )->update ( 'prize_phone' )->data ( array_merge ( $userData, array (
					'vfcode' => $vfCode 
			) ) )->where ( 'id=' . $resID )->done ();
		}
		$sms = '欢迎参加活动，请先绑定手机号。本次验证码为： ' . $vfCode . '';
		logic ( 'push' )->addi ( 'sms', $phone, array (
				'content' => $sms 
		) );
		return true;
	}
	public function Vfcode($phone, $vcode, $uid = 0) {
		$res = $this->GetPhone ( 'phone', $phone );
		$uid === 0 && $uid = user ()->get ( 'id' );
		if (! $res) {
			return '此手机号码还未发送过验证码！';
		}
		if ($res ['uid'] != $uid) {
			return '此手机号码已经被其他用户使用！';
		}
		if ($res ['vftime'] > 0) {
			return '此手机号码已经验证过了！';
		}
		if ($res ['vfcode'] != $vcode) {
			return '无效的验证码！';
		}
		dbc ( DBCMax )->update ( 'prize_phone' )->data ( 'vftime=' . time () )->where ( 'id=' . $res ['id'] )->done ();
		return true;
	}
	private function __random_num($length = 12) {
		$length = ( int ) $length;
		$loops = ceil ( $length / 3 );
		$string = '';
		for($i = 0; $i < $loops; $i ++) {
			$string .= ( string ) mt_rand ( 100, 999 );
		}
		$string = substr ( $string, 0, $length );
		return $string;
	}
	public function __finder($pid, $uid) {
		$finder = user ( $uid )->get ( 'finder' );
		if (! $finder)
			return;
		$ordCount = logic ( 'order' )->Count ( 'productid<>' . $pid . ' AND userid=' . $uid . ' AND pay=' . ORD_PAID_Yes );
		if ($ordCount)
			return;
		$remark = '邀请用户：' . user ( $uid )->get ( 'name' );
		$this->CreateTicket ( $pid, $finder, $remark );
	}
	/**
	 * 查询中奖记录
	 *
	 * @param unknown $pid        	
	 */
	public function PrizeWIN($pid) {
		return dbc ( DBCMax )->select ( 'prize_ticket_win' )->where ( 'pid=' . $pid )->limit ( 1 )->done ();
	}
	/**
	 * 公开中奖号码
	 * 中奖信息写入prize_ticket_win表
	 *
	 * 这个number已经是；隔开的形式，只需要phone再用；隔开即可
	 *
	 * @param unknown $pid        	
	 * @param
	 *        	$number
	 * @return Ambigous <boolean, string>
	 */
	public function PrizePUB($pid, $number) {
		
		// 截取中奖号码，组织成in格式
		$arr = explode ( ";", $number );
		$a1 = "(";
		$in = "";
		foreach ( $arr as $a ) {
			if (is_numeric ( $a )) {
				$a1 .= $a . ",";
			}
		}
		$in .= substr ( $a1, 0, strlen ( $a1 ) - 1 );
		$in .= ")";
		
		$sql = 'SELECT * FROM ' . table ( 'prize_ticket' ) . ' WHERE pid = ' . $pid . ' AND number in ' . $in;
		$prizes = dbc ( DBCMax )->query ( $sql )->done ();
		
		$data = array ();
		$last_uid = "";
		$last_phone = "";
		if (! $prizes) {
			$data ['uid'] = 0;
			$data ['phone'] = '';
		} else {
			$phone = "";
			$uid = "";
			foreach ( $prizes as $prize ) {
				$uid .= $prize ['uid'] . ";";
				$phoneDATA = logic ( 'prize' )->GetPhone ( 'uid', $prize ['uid'] );
				$phone .= $phoneDATA ['phone'] . ";";
			}
			if (strlen ( $phone ) > 0) {
				$last_phone .= substr ( $phone, 0, strlen ( $phone ) - 1 );
			}
			if (strlen ( $uid ) > 0) {
				$last_uid .= substr ( $uid, 0, strlen ( $uid ) - 1 );
			}
		}
		
		$data ['uid'] = $last_uid;
		$data ['phone'] = $last_phone;
		$data ['pid'] = $pid;
		$data ['number'] = $number;
		$data ['upstime'] = time ();
		$iid = dbc ( DBCMax )->insert ( 'prize_ticket_win' )->data ( $data )->done ();
		return $iid ? true : '公开中奖号码失败！（数据库错误）';
	}
	public function GetPhoneList($pid, $packChar = false, $exceptSQL = false) {
		$uidsQuery = dbc ( DBCMax )->select ( 'prize_ticket' )->where ( 'pid=' . $pid )->group ( 'uid' );
		$exceptSQL && $uidsQuery->where ( $exceptSQL );
		$uidsList = $uidsQuery->done ();
		
		$phoneArray = array ();
		$phoneString = '';
		foreach ( $uidsList as $i => $user ) {
			$phone = dbc ( DBCMax )->select ( 'prize_phone' )->in ( 'phone' )->where ( 'uid=' . $user ['uid'] )->limit ( 1 )->done ();
			if (! $phone)
				continue;
			if ($packChar) {
				$phoneString .= $phone ['phone'] . $packChar;
			} else {
				$phoneArray [] = $phone ['phone'];
			}
		}
		return $packChar ? substr ( $phoneString, 0, - 1 ) : $phoneArray;
	}
	/**
	 * 修改后的
	 *
	 * @param unknown $pid        	
	 * @param string $packChar        	
	 * @param string $in        	
	 * @return Ambigous <string, multitype:unknown >
	 */
	public function GetPhoneListMy($pid, $packChar = false, $in = false) {
		// $uidsQuery = dbc ( DBCMax )->select ( 'prize_ticket' )->where ( 'pid=' . $pid )->group ( 'uid' );
		$sql = 'SELECT * FROM ' . table ( 'prize_ticket' ) . ' WHERE pid = ' . $pid . " AND uid not in " . $in;
		$uidsList = dbc ( DBCMax )->query ( $sql )->done ();
		
		$phoneArray = array ();
		$phoneString = '';
		foreach ( $uidsList as $i => $user ) {
			$phone = dbc ( DBCMax )->select ( 'prize_phone' )->in ( 'phone' )->where ( 'uid=' . $user ['uid'] )->limit ( 1 )->done ();
			if (! $phone)
				continue;
			if ($packChar) {
				$phoneString .= $phone ['phone'] . $packChar;
			} else {
				$phoneArray [] = $phone ['phone'];
			}
		}
		return $packChar ? substr ( $phoneString, 0, - 1 ) : $phoneArray;
	}
	public function zhongjiang($uids, $mid) {
		$arr = explode ( ";", $uids );
		foreach ( $arr as $a ) {
			if ($a == $mid) {
				return true;
			}
		}
		return false;
	}
}

?>
