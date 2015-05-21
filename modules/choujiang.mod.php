<?php
class ModuleObject extends MasterObject {
	function ModuleObject($config) {
		$this->MasterObject ( $config );
		if (MEMBER_ID < 1) {
			if (get ( 'pid' )) {
				header ( 'Location: ' . rewrite ( '?view=' . get ( 'pid' ) ) );
				exit ();
			}
			$this->Messager ( __ ( '请先登录！' ), '?mod=myaccount&code=login' );
		}
		$runCode = Load::moduleCode ( $this );
		$this->$runCode ();
	}
	function Main() {
		header ( 'Location: .' );
	}
	
	/**
	 * 转盘结果处理
	 */
	function zhuanpan() {
		$pid = get ( 'pid', 'int' );
		$prizeType = get ( 'prizeType', 'int' );
		$product = logic ( 'choujiang' )->getOneProduct ( $pid );
		// 判断是否已经抽奖
		// 去order表查询
		$has = logic ( 'choujiang' )->hasChouJiang ( $pid, user ()->get ( 'id' ) );
		if ($has) {
			exit ( '不好意思，您已经抽奖' );
		}
		// 1检查这个奖项的数量是否还有，product表里边取出数量与ticket这个商品的总购物券数量对比
		$ok = true;
		if ($prizeType == 0) {
			$ok = true;
		} else {
			$ok = logic ( 'choujiang' )->checkCJ ( $pid, $prizeType );
		}
		if ($ok) {
			// 还有，则生成一个ticket并插入数据库
			$uid = user ()->get ( 'id' );
			$orderid = 0;
			// 2插入订单
			$ordCount = logic ( 'order' )->Count ( 'productid=' . $pid . ' AND userid=' . $uid );
			if (( int ) $ordCount == 0) {
				$order = logic ( 'order' )->GetFree ( $uid, $pid );
				$order ['productnum'] = 1;
				$order ['prize_type'] = $prizeType;
				$order ['productprice'] = 0;
				if ($prizeType == 1)
					$order ['extmsg'] = '抽奖用户IP地址：' . client_ip () . ';     恭喜你获得了' . $product ['zk1'] . TUANGOU_STR . '券';
				elseif ($prizeType == 2)
					$order ['extmsg'] = '抽奖用户IP地址：' . client_ip () . ';     恭喜你获得了' . $product ['zk2'] . TUANGOU_STR . '券';
				elseif ($prizeType == 3)
					$order ['extmsg'] = '抽奖用户IP地址：' . client_ip () . ';     恭喜你获得了' . $product ['zk3'] . TUANGOU_STR . '券';
				else
					$order ['extmsg'] = '抽奖用户IP地址：' . client_ip ();
				$order ['pay'] = ORD_PAID_Yes;
				$order ['process'] = 'TRADE_FINISHED';
				if ($prizeType != 0) {
					$order ['status'] = ORD_STA_Normal;
				} else {
					$order ['status'] = ORD_ZP_FAIL;
				}
				$order ['prize_type'] = $prizeType;
				logic ( 'order' )->Update ( $order ['orderid'], $order );
				$orderid = $order ['orderid'];
			}
			// 中奖了才发奖票
			if ($prizeType != 0) {
				// 3
				$order = logic ( 'order' )->GetOne ( $orderid );
				// 4
				logic ( 'choujiang' )->createTicket ( $order ['productid'], $order ['orderid'], $order ['userid'], $order ['productnum'], false, $prizeType );
// 				$prizePhone = logic ( 'choujiang' )->getPrizePhoneFromTicket ( $order ['userid'] );
// 				$ticket = logic ( 'choujiang' )->getTicket ( $order ['userid'], $order ['productid'] );
// 				$zk = '';
// 				if ($prizeType == 1) {
// 					$zk = $product ['zk1'];
// 				}
// 				if ($prizeType == 2) {
// 					$zk = $product ['zk2'];
// 				}
// 				if ($prizeType == 3) {
// 					$zk = $product ['zk3'];
// 				}
// 				$msg = user ()->get ( 'name' ) . '，恭喜您通过转盘抽奖获得' . $prizeType . '等奖，' . $product ['name'] . '产品的' . $zk . '。编号：' . $ticket ['number'] . '，密码：' . $ticket ['password'];
// 				logic ( 'push' )->addi ( 'sms', $prizePhone ['phone'], array (
// 						'content' => $msg 
// 				) );
			}
			exit ( 'ok' );
		} else {
			// 没有，则抽奖失败
			exit ( '未中奖！下次加油！' );
		}
	}
}
?>