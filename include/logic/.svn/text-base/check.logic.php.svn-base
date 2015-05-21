<?php

/**
 * 购买权限控制
 * @package logic
 * @name check.logic.php
 * @version 1.1
 */
class CheckLogic {
	
	/**
	 * 特殊产品权限
	 * @param unknown $userid 用户id
	 * @param unknown $pid 产品id
	 * 返回：-1：用户错误，-2产品错误，0：无权限，1：有权限
	 */
	public function buyCheck($userid,$pid,$order){
		$where = "";
		if($order){
			$where.=" and orderid != '".$order."' ";
		}
		if(!$userid){
			//用户id错误
			return -1;
		}
		if(!$pid){
			//产品id错误
			return -2;
		}
		$sql = 'select count(*) as ct from ystttuangou_product_activity where productid = '.$pid;
		$result = dbc ()->Query ( $sql )->GetRow ();
		//不在在控制里面
		if($result['ct'] <= 0){
			//有权限
			return 1;
		}
		//订单数大于0则没权限
		$sql = "select count(*) as ct from ystttuangou_order where productid in 
				(select productid from ystttuangou_product_activity) and totalprice > 0 and process in ('TRADE_FINISHED','WAIT_SELLER_SEND_GOODS','WAIT_BUYER_CONFIRM_GOODS','__PAY_YET__','__CREATE__','WAIT_BUYER_PAY') and userid = ".$userid.$where;
		$result = dbc ()->Query ( $sql )->GetRow ();
		//每日限制
		$total = $result['ct'];
		if($total > 0){
			//无权限
			return 0;
		}else{
			//有权限
			return 1;
		}
	}
	
	public function buyCheckCountDown($userid,$pid,$order){
		$where = "";
		if($order){
			$where.=" and a.orderid != '".$order."' ";
		}
		//订单数大于0则没权限
		$sql = "select count(*) as ct from ystttuangou_order a left join ystttuangou_product b on a.productid = b.id
			where b.is_countdown = 1 and a.productid = ".$pid." and a.totalprice > 0 and a.process in ('TRADE_FINISHED','WAIT_SELLER_SEND_GOODS','WAIT_BUYER_CONFIRM_GOODS','__PAY_YET__','__CREATE__','WAIT_BUYER_PAY') and a.userid = ".$userid.$where;
		$result = dbc ()->Query ( $sql )->GetRow ();
		//每日限制
		$total = $result['ct'];
		if($total > 0){
			return "您已经购买此产品，请到个人中心查看";
		}else{
			return "ok";
		}
	}
	
	/**
	 * 根据产品ID判断还剩下多少的每日购买名额
	 * @param unknown $pid
	 * 返回-1：pid错误，-2：不限制，-3：已经卖完，其他则为剩余数量
	 */
	public function dayProductLeft($pid,$order){
		//释放过期的订单
		logic('order')->FreeCountDownOrderNew();
		$where = "";
		if($order){
			$where.=" and orderid != '".$order."' ";
		}
		//产品每日限量
		if($pid){
			$sql = 'select daylimit from ystttuangou_product where id = '.$pid;
			$result = dbc ()->Query ( $sql )->GetRow ();
			//每日限制
			$total = $result['daylimit'];
			if($total > 0){
				$year = date('Y');
				$month = date('m');
				$day = date('d');
				$start = mktime(0,0,0,$month,$day,$year);
				$end = mktime(23,59,59,$month,$day,$year);
				//除了__TIME_LIMIT__都要计算
				//已付款的这个订单的今日购买数量
				$sql = "select count(*) as ct from ystttuangou_order where productid = ".$pid." and ((buytime > '".$start."' and buytime < '".$end."') or (paytime > '".$start."' and paytime < '".$end."')) 
						and process in ('TRADE_FINISHED','WAIT_SELLER_SEND_GOODS','WAIT_BUYER_CONFIRM_GOODS','__PAY_YET__','__CREATE__','WAIT_BUYER_PAY') ".$where;
				$result = dbc ()->Query ( $sql )->GetRow ();
				//产品当日购买总量
				//已经购买数量
				$buyed = $result['ct'];
				if($total > $buyed){
					//其他则为剩余数量
					return $total- $buyed;
				}else{
					//-3：已经卖完
					return -3;
				}
			}else{
				//-2：不限制
				return -2;
			}
		}else{
			//-1：pid错误
			return -1;
		}
	}
	
	/*************************************************************************************************************/
	/**
	 * 
	 * @param unknown $spid 特权ID
	 * @param unknown $uid 用户ID
	 * @return number
	 */
	public function createActivityOrder($spid,$uid){
		if(!$spid){
			//特权产品ID
			return -1;
		}
		if(!$uid){
			//用户ID
			return -2;
		}
		//1、查询这条特权记录2、查询对应的产品
		$sql = 'select a.pid as pid,b.nowprice as price from ystttuangou_activity_special a left join ystttuangou_product b on a.pid = b.id where a.id = '.$spid;
		$result = dbc ()->Query ( $sql )->GetRow ();
		if(!$result){
			return 0;
		}
		//3、0元则直接产生已支付订单，否则产生一个未支付的订单
		$buyTime = time();
		if($result['price'] > 0){
			$array = array (
					'orderid' => $this->__GetFreeID (),
					'productid' => $result['pid'],
					'productnum' => 1,
					'productprice' => $result['price'],
					'totalprice' => $result['price'],
					'paymoney' => $result['price'],
					'userid' => $uid,
					'buytime' => $buyTime,
					'remark' => '特权产品',
					'status' => 1,
					'process' => 'WAIT_BUYER_PAY',
					'is_countdown' => 1
			);
			dbc ()->SetTable ( table ( 'order' ) );
			dbc ()->Insert ( $array );
			//设置订单号
			dbc ( DBCMax )->update ( 'activity_special' )->data ( array (
				'orderid' => $array['orderid']
			) )->where ( array (
				'id' => $spid
			) )->done ();
			return 1;
		}else{
			//产生已支付订单
			$array = array (
					'orderid' => $this->__GetFreeID (),
					'productid' => $result['pid'],
					'productnum' => 1,
					'productprice' => 0,
					'totalprice' => 0,
					'userid' => $uid,
					'buytime' => $buyTime,
					'paytime' => $buyTime,
					'paytype' => 1,
					'paytmoney' => 0,
					'remark' => '特权产品',
					'pay' => 1,
					'status' => 1,
					'process' => 'TRADE_FINISHED',
					'is_countdown' => 1
			);
			dbc ()->SetTable ( table ( 'order' ) );
			dbc ()->Insert ( $array );
			//使用特权
			dbc ( DBCMax )->update ( 'activity_special' )->data ( array (
				'status' => 1,
				'usedtime' => time(),
				'orderid' => $array['orderid']
			) )->where ( array (
				'id' => $spid
			) )->done ();
			//产生团购券
			//20150127用于特权产品，只产生团购券，不发送短信
			logic ( 'coupon' )->Create( $result['pid'], $array['orderid'], $uid, 1, false, false, 0 );
			return 2;
		}
	}
	private function __GetFreeID() {
		$id = date ( 'Ymd', time () ) . str_pad ( rand ( '1', '99999' ), 5, '0', STR_PAD_LEFT );
		$sql = '
		SELECT
			*
		FROM
			' . table ( 'order' ) . '
		WHERE
			orderid = ' . $id;
		$order = dbc ()->Query ( $sql )->GetRow ();
		if (empty ( $order )) {
			return $id;
		} else {
			return $this->__GetFreeID ();
		}
	}
	/**
	 * 特权使用
	 * @param unknown $pid
	 * @param unknown $uid
	 */
	public function makeSpecialUsed($pid,$uid,$orderid){
		$sql = "select * from ystttuangou_activity_special where pid = ".$pid." and userid = ".$uid." and status = 0 and orderid = '".$orderid."'";
		$res = dbc ( DBCMax )->query ( $sql )->done ();
		if(count($res)>0){
			$ps = $res[0];
			dbc ( DBCMax )->update ( 'activity_special' )->data ( array (
					'status' => 1,
					'usedtime' => time(), 
					'orderid' => $orderid
			) )->where ( array (
					'id' => $ps['id']
			) )->done ();
		}
	}
	/**
	 * 检查是否有特权
	 * @param unknown $pid
	 * @param unknown $uid
	 * @return boolean
	 */
	public function specialCK($pid,$uid){
		$sql = "select count(*) as ct from ystttuangou_activity_special where userid = ".$uid." and pid = ".$pid;
		$result = dbc ()->Query ( $sql )->GetRow ();
		if($result['ct'] > 0){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 购买控制
	 * 1、每日购买数量
	 * 2、特权产品
	 * @param unknown $pid
	 * @param unknown $orderid
	 * @return string
	 */
	public function commonBuyCheck($pid,$orderid){
		$where = "";
		if($orderid){
			$where.=" and orderid != '".$orderid."' ";
		}
		if($pid){
			//1、每日购买数量
			$sql = 'select * from ystttuangou_product where id = '.$pid;
			$result = dbc ()->Query ( $sql )->GetRow ();
			//每日限制
			$total = $result['daylimit'];
			if($total > 0){
				$year = date('Y');
				$month = date('m');
				$day = date('d');
				$start = mktime(0,0,0,$month,$day,$year);
				$end = mktime(23,59,59,$month,$day,$year);
				//除了__TIME_LIMIT__都要计算
				//已付款的这个订单的今日购买数量
				$sql = "select count(*) as ct from ystttuangou_order where productid = ".$pid." and ((buytime > '".$start."' and buytime < '".$end."') or (paytime > '".$start."' and paytime < '".$end."'))
						and process in ('TRADE_FINISHED','WAIT_SELLER_SEND_GOODS','WAIT_BUYER_CONFIRM_GOODS','__PAY_YET__','__CREATE__','WAIT_BUYER_PAY') ".$where;
				$result1 = dbc ()->Query ( $sql )->GetRow ();
				//产品当日购买总量
				//已经购买数量
				$buyed = $result1['ct'];
				if($total <= $buyed){
					//本日已经售完
					return "今日已售馨，明日再来";
				}
			}
			//2、特权产品
			if($result['is_special']){
				//a、必须已经登录，特权产品要么直接产生订单，要么在订单列表里面直接购买
				$uid = user ()->get ( 'id' );
				if(!$uid){
					return "01";
				}
				//b、在特权表里面有
				$sql = "select count(*) as ct from ystttuangou_activity_special where pid = ".$pid." and userid = ".$uid." and status = 0";
				$result2 = dbc ()->Query ( $sql )->GetRow ();
				if($result2['ct'] <= 0){
					return "01";
				}
			}
			return "00";
		}else{
			return "产品不存在";
		}
	}
	
	/**
	 * 构建详情页的按钮
	 * @param unknown $pid 产品ID
	 * @param unknown $cd true：全部订单（包括未支付）false：已经支付的订单（已支付）
	 * @return string
	 */
	public function buildDayLeft($pid){
		$sql = "select * from ystttuangou_product where id = ".$pid;
		$product = dbc( DBCMax )->query ( $sql )->limit ( 1 )->done ();
		if(!$product){
			echo '';
			return;
		}
		$now = time ();
		//未时间
		if ($product ['begintime'] > $now){
			echo '';
			return;
		}
		//已结束
		if ($product ['overtime'] < $now){
			echo '';
			return;
		}
		//判断总数量；maxnum大于0时才有限制
		if ($product ['maxnum'] > 0) {
			//总数量
			$maxnum = $product ['maxnum'];
			//已售数量
			$sells = $this->SellsCount ($pid);
			//剩余数量
			$surplus = $maxnum - $sells;
			if($surplus <= 0){
				//全部已经卖完
				echo '';
				return;
			}
		}
		//判断每日数量；daylimit大于0时才有限制
		if($product ['daylimit'] > 0){
			$year = date('Y');
			$month = date('m');
			$day = date('d');
			$start = mktime(0,0,0,$month,$day,$year);
			$end = mktime(23,59,59,$month,$day,$year);
			//1：全部订单0：已经支付的订单
			$in = "";
			//if($product ['is_countdown']){
				$in = "('TRADE_FINISHED','WAIT_SELLER_SEND_GOODS','WAIT_BUYER_CONFIRM_GOODS','__PAY_YET__','__CREATE__','WAIT_BUYER_PAY') ";
			//}else{
				//$in = "('TRADE_FINISHED','WAIT_SELLER_SEND_GOODS','WAIT_BUYER_CONFIRM_GOODS') ";
			//}
			//已付款的这个订单的今日购买数量
			$sql = "select count(*) as ct from ystttuangou_order where productid = ".$pid." and ((buytime > '".$start."' and buytime < '".$end."') or (paytime > '".$start."' and paytime < '".$end."'))
						and process in ".$in;
			$result = dbc ()->Query ( $sql )->GetRow ();
			//产品当日购买总量
			$buyed = $result['ct'];
			if($product ['daylimit'] > $buyed){
				$left = $product ['daylimit'] - $buyed;
				//有每日限制，且没卖完
				echo '<li class="col-xs-4 surplus"><i></i>今日剩余<strong>'.$left.'</strong>件</li>';
				return;
			}else{
				//有每日限制，且卖完
				echo '<li class="col-xs-4 surplus"><i></i><strong>过会还有,等你来抢</strong></li>';
				return;
			}
		}
		echo '';
		return;
	}
	
	/**
	 * 构建秒杀的按钮
	 * 限购模式：全部订单、限购模式（包括未支付）；否则：已经支付的订单（已支付）
	 * @param unknown $pid 产品ID
	 * @param unknown $type 1:秒杀的按钮0：详情的按钮
	 * @return string
	 */
	public function buildCountDownButton($pid,$type=1){
		$sql = "select * from ystttuangou_product where id = ".$pid;
		$product = dbc( DBCMax )->query ( $sql )->limit ( 1 )->done ();
		if(!$product){
			if($type == 1){
				echo '<a href="javascript:void(0);" class="btn btn-default pull-right">已经结束</a>';
			}else{
				echo '<a href="javascript:void(0);" class="btn btn-default">已经结束</a>';
			}
			return;
		}
		if($product['is_special']){
			return;
		}
		$now = time ();
		//开始时间
		if ($product ['begintime'] > $now){
			if($type == 1){
				echo '<a href="javascript:void(0);" class="btn btn-default pull-right">即将开始</a>';
			}else{
				echo '<a href="javascript:void(0);" class="btn btn-default">即将开始</a>';
			}
			return;
		}
		//结束时间
		if ($product ['overtime'] < $now){
			if($type == 1){
				echo '<a href="javascript:void(0);" class="btn btn-default pull-right">已经结束</a>';
			}else{
				echo '<a href="javascript:void(0);" class="btn btn-default">已经结束</a>';
			}
			return;
		}
		//判断总数量；maxnum大于0时才有限制
		if ($product ['maxnum'] > 0) {
			//总数量
			$maxnum = $product ['maxnum'];
			//已售数量
			$sells = $this->SellsCount ($pid);
			//剩余数量
			$surplus = $maxnum - $sells;
			if($surplus <= 0){
				if($type == 1){
					echo '<a href="javascript:void(0);" class="btn btn-default pull-right">已经售馨</a>';
				}else{
					echo '<a href="javascript:void(0);" class="btn btn-default">已经售馨</a>';
				}
				//全部已经卖完
				return;
			}
		}
		//判断每日数量；daylimit大于0时才有限制
		if($product ['daylimit'] > 0){
			$year = date('Y');
			$month = date('m');
			$day = date('d');
			$start = mktime(0,0,0,$month,$day,$year);
			$end = mktime(23,59,59,$month,$day,$year);
			//1：全部订单0：已经支付的订单
			$in = "";
			if($product ['is_countdown']){
				$in = "('TRADE_FINISHED','WAIT_SELLER_SEND_GOODS','WAIT_BUYER_CONFIRM_GOODS','__PAY_YET__','__CREATE__','WAIT_BUYER_PAY') ";
			}else{
				$in = "('TRADE_FINISHED','WAIT_SELLER_SEND_GOODS','WAIT_BUYER_CONFIRM_GOODS') ";
			}
			//已付款的这个订单的今日购买数量
			$sql = "select count(*) as ct from ystttuangou_order where productid = ".$pid." and ((buytime > '".$start."' and buytime < '".$end."') or (paytime > '".$start."' and paytime < '".$end."'))
						and process in ".$in;
			$result = dbc ()->Query ( $sql )->GetRow ();
			//产品当日购买总量
			$buyed = $result['ct'];
			if($product ['daylimit'] > $buyed){
				$left = $product ['daylimit'] - $buyed;
				if($type == 1){
					echo '<a class="xs_buy" href="?mod=buy&code=checkout&id='.$pid.'" class="btn btn-primary xs_buy pull-right"><span>剩余<br/>'.$left.'件</span>抢枪抢</a>';
				}else{
					echo '<a class="xs_buy" href="?mod=buy&code=checkout&id='.$pid.'" class="btn btn-primary xs_buy"><span>剩余<br/>'.$left.'件</span>抢枪抢</a>';
				}
				//有每日限制，且没卖完
				return;
				return;
			}else{
				if($type == 1){
					echo '<a id="buyEnd" href="javascript:void(0);" class="btn btn-default xs_buy pull-right"><span>过会<br/>还有</span>等你来抢</a>';
				}else{
					echo '<a id="buyEnd" href="javascript:void(0);" class="btn btn-default xs_buy"><span>过会<br/>还有</span>等你来抢</a>';
				}
				//有每日限制，且卖完
				return;
			}
		}
		if($product ['is_countdown']){
			if($type == 1){
				echo '<a href="?mod=buy&code=checkout&id='.$pid.'" class="btn btn-primary pull-right">抢枪抢</a>';
			}else{
				echo '<a href="?mod=buy&code=checkout&id='.$pid.'" class="btn btn-primary">抢枪抢</a>';
			}
		}else{
			if($type == 1){
				echo '<a href="?mod=buy&code=checkout&id='.$pid.'" class="btn btn-primary pull-right">立即购买</a>';
			}else{
				echo '<a href="?mod=buy&code=checkout&id='.$pid.'" class="btn btn-primary">立即购买</a>';
			}
		}
		return;
	}
	/**
	 * 产品已售数量
	 * @param unknown $id
	 * @return number
	 */
	function SellsCount($id) {
		$sql = 'SELECT SUM(productnum) AS sums
		FROM
			' . table ( 'order' ) . '
		WHERE
			productid=' . intval ( $id ) . '
		AND
			' . logic ( 'pay' )->OrderPaidSQL () . '
		AND
			status = ' . ORD_STA_Normal;
		$result = dbc ( DBCMax )->query ( $sql )->limit ( 1 )->done ();
		return ( int ) $result ['sums'];
	}
	
}
?>