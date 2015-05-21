<?php

/**
 * 模块：购买流程操作
 * @package module
 * @name buy.mod.php
 * @version 1.0
 */
class ModuleObject extends MasterObject {
	function ModuleObject($config) {
		$this->MasterObject ( $config );
		if (MEMBER_ID < 1) {
			$this->Messager ( __ ( '请先登录！' ), '?mod=myaccount&code=login' );
		}
		$runCode = Load::moduleCode ( $this );
		$this->$runCode ();
	}
	function Main() {
		header ( 'Location: .' );
	}
	function Checkout() {
		$this->Title = __ ( '提交订单' );
		//必须先绑定手机
		if(user()->get("phone")){
			$phone = user()->get("phone");
		}else{
			//未绑定手机号
			$this->Messager ("请先绑定手机号","?mod=me&code=setting");
		}
		$id = get ( 'id', 'int' );
// 		//针对活动的处理20150109
// 		//返回：-1：用户错误，-2产品错误，0：无权限，1：有权限
// 		$buyCheckRes = logic('check')->buyCheck(MEMBER_ID,$id);
// 		if($buyCheckRes == 0){
// 			$this->Messager ("亲，你已经有一个不错的大奖了，这个就留个其他小伙伴吧","?view=".$id);
// 		}
		
// 		//判断产品是否已经购买
// 		$buyCheckCountDownRes = logic ( 'check' )->buyCheckCountDown(MEMBER_ID,$id);
// 		if ($buyCheckCountDownRes != "ok") {
// 			$this->Messager ($buyCheckCountDownRes,"?mod=me&code=order");
// 		}
		
// 		//当日购买数量判断20150109
// 		//返回-1：pid错误，-2：不限制，-3：已经卖完，其他则为剩余数量
// 		$dayProductLeftRes = logic('check')->dayProductLeft($id);
// 		if($dayProductLeftRes == -1 || $dayProductLeftRes == -3){
// 			$this->Messager ("过会还有，等你来抢","?view=".$id);
// 		}
		
		//每日限制+特权产品
		$commonBuyCheckRes = logic('check')->commonBuyCheck($id);
		if($commonBuyCheckRes != "00"){
			if($commonBuyCheckRes == "01"){
				//特权产品
				$this->Messager ("对不起您没有权限购买该产品","?mod=index");
			}else{
				//非特权产品
				$this->Messager ($commonBuyCheckRes,"?view=".$id);
			}
		}
		
		//判断产品是否已经购买
		$product = logic ( 'product' )->BuysCheck ( $id );
		if (isset ( $product ['false'] )) {
			$this->Messager ( $product ['false'] );
		}
		//抽奖
		if ($product ['type'] == 'prize') {
			header ( 'Location: ' . rewrite ( '?mod=prize&code=sign&pid=' . $product ['id'] ) );
			exit ();
		}
		include handler ( 'template' )->file ( 'buy_checkout' );
	}
	function Checkout_save() {
		$product_id = post ( 'product_id', 'int' );
		
		//每日限制+特权产品
		$commonBuyCheckRes = logic('check')->commonBuyCheck($product_id);
		if($commonBuyCheckRes != "00"){
			if($commonBuyCheckRes == "01"){
				//特权产品
				$this->Messager ("对不起您没有权限购买该产品","?mod=index");
			}else{
				//非特权产品
				$this->Messager ($commonBuyCheckRes,"?view=".$product_id);
			}
		}
		
		$product = logic ( 'product' )->BuysCheck ( $product_id );
		if (isset ( $product ['false'] )) {
			return $this->__ajax_save_failed ( $product ['false'] );
		}
		$num_buys = post ( 'num_buys', 'int' );
		if (! $num_buys || ($product ['oncemax'] > 0 && $num_buys > $product ['oncemax']) || $num_buys < $product ['oncemin']) {
			return $this->__ajax_save_failed ( __ ( '请填写正确的购买数量！' ) );
		}
		$order = logic ( 'order' )->GetFree ( user ()->get ( 'id' ), $product_id );
		$order ['productnum'] = $num_buys;
		$order ['productprice'] = $product ['nowprice'];
		$order ['extmsg'] = post ( 'extmsg', 'txt' );
		if ($product ['type'] == 'stuff') {
			logic ( 'address' )->Accessed ( 'order.save', $order );
			logic ( 'express' )->Accessed ( 'order.save', $order );
		}
		logic ( 'notify' )->Accessed ( 'order.save', $order );
		if (! logic ( 'attrs' )->Accessed ( 'order.save', $order )) {
			return $this->__ajax_save_failed ( __ ( '请选择正确的产品属性规格！' ) );
		}
		$price_total = $order ['productprice'] * $order ['productnum'] + $order ['expressprice'];
		logic ( 'attrs' )->order_calc ( $order ['orderid'], $price_total );
		if (( float ) $price_total < 0) {
			return $this->__ajax_save_failed ( __ ( '订单总价不正确，请重新下单！' ) );
		}
		$order ['totalprice'] = $price_total;
		$order ['process'] = '__CREATE__';
		$order ['status'] = ORD_STA_Normal;
		
		if($price_total==0){//20150111 0元支付方式 余额支付 ID=1
			$order ['paytype'] = ORD_STA_Normal;
		}
		
		if ($product ['is_countdown'] == 1) {
			$order ['is_countdown'] = 1;
			//sells_count用于限购模式，只要产生订单就加，FreeCountDownOrder的时候会减去
			dbc ( DBCMax )->update ( 'product' )->data ( 'sells_count=sells_count+' . ( int ) $num_buys )->where ( 'id=' . $product_id )->done ();
		}
		logic ( 'order' )->Update ( $order ['orderid'], $order );
		
		$ops = array (
				'status' => 'ok',
				'id' => $order ['orderid'] 
		);
		
		//0元直接支付完成20140111
		if($price_total==0){
			$ops = array (
				'status' => 'ok',
				'id' => $order ['orderid'],
				'url' => '?mod=callback&pid=1&is_flish=1&sign=' . $order ['orderid']
			);
		}
		
		if (! X_IS_AJAX) {
			header ( 'Location: ' . rewrite ( '?mod=buy&code=order&id=' . $order ['orderid'] ) );
			exit ();
		}
		echo jsonEncode ( $ops );
	}
	function Checkout_rank_save() {
		$product_id = post ( 'product_id', 'int' );
		$product = logic ( 'product' )->BuysCheck ( $product_id );
		if (isset ( $product ['false'] )) {
			return $this->__ajax_save_failed ( $product ['false'] );
		}
		$num_buys = post ( 'num_buys', 'int' );
		if (! $num_buys || ($product ['oncemax'] > 0 && $num_buys > $product ['oncemax']) || $num_buys < $product ['oncemin']) {
			return $this->__ajax_save_failed ( __ ( '请填写正确的购买数量！' ) );
		}
		$userid = post ( 'userid', 'int' );
		$order = logic ( 'order' )->GetFree ( $userid , $product_id );
		$order ['productnum'] = $num_buys;
		$order ['productprice'] = $product ['nowprice'];
		$order ['extmsg'] = post ( 'extmsg', 'txt' );
		if ($product ['type'] == 'stuff') {
			logic ( 'address' )->Accessed ( 'order.save', $order );
			logic ( 'express' )->Accessed ( 'order.save', $order );
		}
		logic ( 'notify' )->Accessed ( 'order.save', $order );
		if (! logic ( 'attrs' )->Accessed ( 'order.save', $order )) {
			return $this->__ajax_save_failed ( __ ( '请选择正确的产品属性规格！' ) );
		}
		$price_total = $order ['productprice'] * $order ['productnum'] + $order ['expressprice'];
		logic ( 'attrs' )->order_calc ( $order ['orderid'], $price_total );
		if (( float ) $price_total < 0) {
			return $this->__ajax_save_failed ( __ ( '订单总价不正确，请重新下单！' ) );
		}
		$order ['totalprice'] = $price_total;
		$order ['process'] = '__CREATE__';
		$order ['status'] = ORD_STA_Normal;
		if ($product ['is_countdown'] == 1) {
			$order ['is_countdown'] = 1;
			dbc ( DBCMax )->update ( 'product' )->data ( 'sells_count=sells_count+' . ( int ) $num_buys )->where ( 'id=' . $product_id )->done ();
		}
		logic ( 'order' )->Update ( $order ['orderid'], $order );
		$ops = array (
				'status' => 'ok',
				'id' => $order ['orderid']
		);
		if (! X_IS_AJAX) {
			header ( 'Location: ' . rewrite ( '?mod=buy&code=order&id=' . $order ['orderid'] ) );
			exit ();
		}
		echo jsonEncode ( $ops );
	}
	private function __ajax_save_failed($msg) {
		$ops = array (
				'status' => 'failed',
				'msg' => $msg 
		);
		if (! X_IS_AJAX) {
			$this->Messager ( $msg, - 1 );
		}
		echo jsonEncode ( $ops );
		return false;
	}
	function Order() {
		$this->Title = __ ( '确认订单' );
		//必须先绑定手机
		if(user()->get("phone")){
			$phone = user()->get("phone");
		}else{
			//未绑定手机号
			$this->Messager ("请先绑定手机号","?mod=me&code=setting");
		}
		$id = get ( 'id', 'number' );
		$order = logic ( 'order' )->GetOne ( $id );
		
// 		//针对活动的处理20150109
// 		//返回：-1：用户错误，-2产品错误，0：无权限，1：有权限
// 		$buyCheckRes = logic('check')->buyCheck(MEMBER_ID,$order['productid'],$id);
// 		if($buyCheckRes == 0){
// 			$this->Messager ("亲，你已经有一个不错的大奖了，这个就留个其他小伙伴吧","?view=".$id);
// 		}
		
// 		//判断产品是否已经购买
// 		$buyCheckCountDownRes = logic ( 'check' )->buyCheckCountDown(MEMBER_ID,$id);
// 		if ($buyCheckCountDownRes != "ok") {
// 			$this->Messager ($buyCheckCountDownRes,"?mod=me&code=order");
// 		}
		
// 		//当日购买数量判断20150109
// 		//返回-1：pid错误，-2：不限制，-3：已经卖完，其他则为剩余数量
// 		$dayProductLeftRes = logic('check')->dayProductLeft($order['productid'],$id);
// 		if($dayProductLeftRes == -1 || $dayProductLeftRes == -3){
// 			$this->Messager ("过会还有，等你来抢","?view=".$id);
// 		}

		//每日限制+特权产品
		$commonBuyCheckRes = logic('check')->commonBuyCheck($order['productid'],$id);
		if($commonBuyCheckRes != "00"){
			if($commonBuyCheckRes == "01"){
				//特权产品
				$this->Messager ("对不起您没有权限购买该产品","?mod=index");
			}else{
				//非特权产品
				$this->Messager ($commonBuyCheckRes,"?view=".$order['productid']);
			}
		}
		
		if (user ()->get ( 'id' ) != $order ['userid']) {
			$this->Messager ( '对不起，您没有权限操作此订单！', '?mod=me&code=order' );
		}
		if ($order ['product'] ['type'] == 'stuff' && $order ['addressid'] == 0) {
			logic ( 'order' )->Delete ( $id );
			$this->Messager ( __ ( '该订单无效，请重新下单！' ), '?mod=buy&code=checkout&id=' . $order ['productid'] );
		}
		if ($order ['is_countdown'] == 1) {
			$countdown_p = logic('product')->SrcOne($order ['productid']);
			$timeLimit_free = 0;
			if($countdown_p['order_time']){
				//设置了时间
				$timeLimit_free = $countdown_p['order_time'];
			}else{
				//没有设置时间
				$timeLimit_free = ini ( 'settings.free_time' );
			}
			$order ['timelimit'] = ($order ['buytime'] + 60 * $timeLimit_free) - time ();
			if($order ['timelimit'] <= 0){
				logic ( 'order' )->FreeCountDownOrderNew($order ['productid']);
			}
		}
		$order ['price_of_product'] = $order ['productprice'] * $order ['productnum'];
		$order ['price_of_total'] = $order ['price_of_product'];
		logic ( 'address' )->Accessed ( 'order.show', $order );
		logic ( 'express' )->Accessed ( 'order.show', $order );
		logic ( 'notify' )->Accessed ( 'order.show', $order );
		logic ( 'attrs' )->Accessed ( 'order.show', $order );
		include handler ( 'template' )->file ( 'buy_order' );
	}
	function Order_save() {
		$order_id = post ( 'order_id', 'number' );
		//$ibank = post ( 'ibank', 'txt' );
		$ibank =  post ( 'gopay_bankSelected', 'txt' );//xjf添加，获取国付宝支付银行代码
		$order = logic ( 'order' )->GetOne ( $order_id );
		if (user ()->get ( 'id' ) != $order ['userid']) {
			return $this->__ajax_save_failed ( __ ( '您没有权限操作此订单！' ) );
		}
		if ($order ['status'] != ORD_STA_Normal || $order ['pay'] == ORD_PAID_Yes) {
			return $this->__ajax_save_failed ( __ ( '此订单已经不能支付！' ) );
		}
		$product = logic ( 'product' )->BuysCheck ( $order ['productid'] );
		if (isset ( $product ['false'] )) {
			return $this->__ajax_save_failed ( $product ['false'] );
		}
		$payment_id = post ( 'payment_id', 'int' );
		if ($order ['product'] ['type'] == 'stuff' && $order ['addressid'] == 0) {
			logic ( 'order' )->Delete ( $order_id );
			$this->Messager ( __ ( '该订单无效，请重新下单！' ), '?mod=buy&code=checkout&id=' . $order ['productid'] );
		}
		if ($order ['product'] ['type'] == 'stuff') {
			logic ( 'express' )->Accessed ( 'order.save', $order );
		}
		
		$price_total = $order ['productprice'] * $order ['productnum'] + $order ['expressprice'];
		logic ( 'attrs' )->order_calc ( $order ['orderid'], $price_total );
		$pay_money = $price_total;
		
		$pay = logic ( 'pay' )->GetOne ( $payment_id );
		if ($pay_money == 0 && $pay ['code'] != 'self') {
			return $this->__ajax_save_failed ( __ ( '请选择余额支付' ) );
		}
		
		$me_money = user ()->get ( 'money' );
		if ($payment_id == 1) {
			$me_money = 0;
		}
		$use_surplus = post ( 'payment_use_surplus', 'txt' );
		if ($use_surplus == 'true' && $me_money > 0) {
			$pay_money = $price_total - $me_money;
		}
		$array = array (
				'totalprice' => $price_total,
				'paytype' => $payment_id,
				'paymoney' => $pay_money 
		);
		if ($order ['product'] ['type'] == 'stuff') {
			$array ['expresstype'] = $order ['expresstype'];
			$array ['expressprice'] = $order ['expressprice'];
		}
		logic ( 'order' )->Update ( $order_id, $array );
		$ops = array (
				'status' => 'ok',
				'tourl' => rewrite ( "?mod=buy&code=pay&id=" . $order ["orderid"] . "&ibank=" . $ibank ) 
		);
		if (logic ( 'pay' )->plugin_has_ext_html ( $payment_id ) === true) {
			header ( 'Location: ' . rewrite ( '?mod=buy&code=pay&id=' . $order_id . '&ibank=' . $ibank ) );
			exit ();
		}
		if (! X_IS_AJAX) {
			header ( 'Location: ' . rewrite ( '?mod=buy&code=pay&id=' . $order_id . '&ibank=' . $ibank ) );
			exit ();
		}
		echo jsonEncode ( $ops );
	}
	function Pay() {
		$this->Title = __ ( '订单支付' );
		$id = get ( 'id', 'number' );
		$order = logic ( 'order' )->GetOne ( $id );
		if (user ()->get ( 'id' ) != $order ['userid']) {
			$this->Messager ( '对不起，您没有权限支付此订单！', '?mod=me&code=order' );
		}
		if ($order ['process'] == "_TimeLimit_") {
			$this->Messager ( '对不起，该订单已经失效！', '?mod=me&code=order' );
		}
		if ($order ['status'] != ORD_STA_Normal) {
			$this->Messager ( __ ( '关于此订单：' ) . logic ( 'order' )->STA_Name ( $order ['status'] ), '?mod=me&code=order' );
		}
		if ($order ['paytype'] == 0) {
			header ( 'Location: ' . rewrite ( '?mod=buy&code=order&id=' . $id ) );
		}
		if ($order ['pay'] == 1 && $order ['paytime'] > 0) {
			$this->Messager ( __ ( '此订单已经支付过了！' ), '?mod=me&code=order' );
		}
		if ($order ['product'] ['type'] == 'stuff' && $order ['addressid'] == 0) {
			logic ( 'order' )->Delete ( $id );
			$this->Messager ( __ ( '该订单无效，请重新下单！' ), '?mod=buy&code=checkout&id=' . $order ['productid'] );
		}
		$product = logic ( 'product' )->BuysCheck ( $order ['productid'] );
		if (isset ( $product ['false'] )) {
			return $this->Messager ( $product ['false'] );
		}
		
		$payment_id = get ( 'p' );
		if (is_numeric ( $payment_id )) {
			logic ( 'order' )->Update ( $id, array (
					'paytype' => $payment_id 
			) );
		}
		
		$pay = logic ( 'pay' )->GetOne ( $order ['paytype'] );
		if(get('ibank','txt')!='')//选择银行卡时，修改支付名称：xjf
		{
			$pay['name']="银行卡支付";
		}
		$pay ['code'] == 'yeepay' && $pay ['site'] = 'pc_web';
		
		$rewrite_me = false;
		include (CONFIG_PATH . 'rewrite.php');
		if ($_rewrite ['mode'] != '') {
			$me_uname = isset ( $_rewrite ['value_replace_list'] ['mod'] ['me'] ) === false ? 'me' : $_rewrite ['value_replace_list'] ['mod'] ['me'];
			$rewrite_me = strpos ( $_SERVER ['HTTP_REFERER'], $me_uname . '/order' );
		}
		if ($pay ['code'] == 'bankdirect' && ($rewrite_me || strpos ( $_SERVER ['HTTP_REFERER'], 'mod=me&code=order' ))) {
			header ( 'Location: ' . rewrite ( '?mod=buy&code=order&id=' . $id ) );
		}
		
		$parameter = array (
				'userid' => $order ['userid'],
				'name' => $order ['product'] ['flag'],
				'detail' => $order ['product'] ['intro'],
				'price' => $order ['paymoney'],
				'sign' => $order ['orderid'],
				'notify_url' => ini ( 'settings.site_url' ) . '/index.php?mod=callback&pid=' . $pay ['id'],
				'product_url' => ini ( 'settings.site_url' ) . '/index.php?view=' . $order ['productid'],
				'bankCode' => get('ibank','txt')
		);
		if ($order ['product'] ['type'] == 'stuff') {
			$address = logic ( 'address' )->GetOne ( $order ['addressid'] );
			$parameter ['addr_name'] = $address ['name'];
			$parameter ['addr_address'] = $address ['region'] . $address ['address'];
			$parameter ['addr_zip'] = $address ['zip'];
			$parameter ['addr_phone'] = $address ['phone'];
		}
		if (logic ( 'pay' )->plugin_has_ext_html ( $pay ['code'] ) === true && get ( 'ibank', 'txt' ) != '') {
			$log_data = array (
					'type' => $pay ['id'],
					'sign' => $parameter ['sign'],
					'money' => $parameter ['price'] 
			);
			logic ( 'pay' )->__LogCreate ( $log_data ) && logic ( 'order' )->Processed ( $parameter ['sign'], 'WAIT_BUYER_PAY' );
			$link = logic ( 'pay' )->apiz ( $pay ['code'] )->CreatForm ( $pay, $parameter );
			echo $link;
			exit ();
		}
		if (method_exists ( logic ( 'pay' )->apiz ( $pay ['code'] ), 'inner_disabled' ) && logic ( 'pay' )->apiz ( $pay ['code'] )->inner_disabled ()) {
			$payment_linker = '<input type="button" value="请用手机付款">';
		} else {
			$payment_linker = logic ( 'pay' )->Linker ( $pay, $parameter );
		}
		include handler ( 'template' )->file ( 'buy_pay' );
	}
	function TradeConfirm() {
		$id = get ( 'id', 'number' );
		if (! $id) {
			$this->Messager ( __ ( '订单号无效！' ) );
		}
		$order = logic ( 'order' )->GetOne ( $id );
		if (user ()->get ( 'id' ) != $order ['userid']) {
			$this->Messager ( '对不起，您没有权限操作此订单！', '?mod=me&code=order' );
		}
		logic ( 'order' )->Processed ( $id, 'TRADE_FINISHED' );
		logic ( 'rebate' )->Add_Rebate_For_Item ( $order );
		
		$this->Messager ( __ ( '本次交易已经完成！' ), '?mod=me&code=order' );
	}
	public function order_process() {
		$sign = get ( 'sign', 'number' );
		include handler ( 'template' )->file ( 'buy_order_process' );
	}
	public function order_url() {
		$sign = get ( 'sign', 'number' );
		if ($sign) {
			$order = logic ( 'order' )->GetOne ( $sign );
			if (! $order) {
				exit ( rewrite ( '?mod=me&code=order' ) );
			}
		} else {
			exit ( rewrite ( '?mod=me&code=order' ) );
		}
		if ($order ['process'] == 'TRADE_FINISHED') {
			$url = rewrite ( '?mod=me&code=order' );
		} elseif ($order ['process'] == 'WAIT_BUYER_CONFIRM_GOODS') {
			if ($order ['product'] ['type'] == 'ticket') {
				$url = logic ( 'pay' )->ConfirmLinker ( $order );
			} else {
				$url = rewrite ( '?mod=me&code=order' );
			}
		} else {
			$url = 'wait';
		}
		exit ( $url );
	}
	//众筹产生订单
	public function raiseOrder(){
		$userid = get('userid','int');
		$product_id = get('pid','int');
		if(!$userid || $userid <= 0){
			$returnArr = array(
					"code"=>"01",
					"msg"=>"用户id不正确"
			);
			exit(jsonEncode ( $returnArr ));
		}
		if(!$product_id || $product_id <= 0){
			$returnArr = array(
					"code"=>"02",
					"msg"=>"产品id不正确"
			);
			exit(jsonEncode ( $returnArr ));
		}
		$sql = 'select * from ' . TABLE_PREFIX . 'system_members where uid = '.$userid;
		$query = $this->DatabaseHandler->Query ( $sql );
		$user = $query->GetRow ();
		if($user){
			//用户不存在
			$returnArr = array(
					"code"=>"03",
					"msg"=>"用户不存在"
			);
			exit(jsonEncode ( $returnArr ));
		}else if($user['phone']){
			//手机号没有没绑定
			$returnArr = array(
				"code"=>"04",
				"msg"=>"用户手机号未绑定"
			);
			exit(jsonEncode ( $returnArr ));
		}
		$order = logic ( 'order' )->GetFree ( $userid , $product_id );
		$order = logic ( 'order' )->raiseMakeSuccessed($order['orderid']);
		if (isset ( $product ['false'] )) {
			$returnArr = array(
				"code"=>"05",
				"msg"=>$product ['false']
			);
		exit(jsonEncode ( $returnArr ));
		}
		$returnArr = array(
			"code"=>"00",
			"msg"=>"订单发送成功"	
		);
		exit(jsonEncode ( $returnArr ));
	}
}

?>