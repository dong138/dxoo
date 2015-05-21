<?php

/**
 * 支付方式：余额付款
 * @package payment
 * @name self.php
 * @version 1.1
 */
class selfPaymentDriver extends PaymentDriver {
	public function CreateLink($payment, $parameter) {
		$market_account = dbc ( DBCMax )->select ( 'members' )->where ( array (
				'uid' => MEMBER_ID 
		) )->limit ( 1 )->done ();
		if ($market_account ['email2']) {
			$url = rewrite ( '?mod=me&code=setting' );
			$market_note = '&nbsp;&nbsp;<a href="' . $url . '" target="_blank">点此修改登录密码</a>';
		}
		
		$html = '<form action="?mod=callback&pid=' . $payment ['id'] . '" method="post">';
		$html .= '<input type="hidden" name="sign" value="' . $parameter ['sign'] . '" /><br/>';
		$html .= '请输入您的登录密码：【<a href="?mod=me&code=setting">设置登录密码</a>】<br/><br/>
				<input type="password" name="password" class="input_h"/>';
		$html .= '<input type="submit" value=" 提交 " class="btn btn-primary" style="float:none;" />' . $market_note;
		$html .= '</form>';
		return $html;
	}
	public function CreateConfirmLink($payment, $order) {
		return '?mod=buy&code=tradeconfirm&id=' . $order ['orderid'];
	}
	public function CallbackVerify($payment) {
		if (user ()->get ( 'id' ) < 1) {
			return 'VERIFY_FAILED';
		}
		$password = post ( 'password', 'txt' );
		$market_account = dbc ( DBCMax )->select ( 'members' )->where ( array (
				'uid' => MEMBER_ID 
		) )->limit ( 1 )->done ();
		$fromisqq = explode ( '.', $market_account ['email2'] );
		if ($market_account ['email2'] == 'zuitu') {
			$validate = md5 ( $password . '@4!@#$%@' ) != user ()->get ( 'password' );
		} elseif ($fromisqq [1] == 'qq') {
			$validate = md5 ( md5 ( $password ) ) != user ()->get ( 'password' );
		} else {
			$validate = md5 ( $password ) != user ()->get ( 'password' );
		}
		
		//TODO 20150107新增测试
		$is_flish = get ( 'is_flish', 'int' );//is_flish直接完成交易
		$is_flish || $is_flish = post ( 'is_flish', 'int' );
		
		if ($validate && $is_flish!=1) {
			return 'VERIFY_FAILED';
		}
		$trade = $this->GetTradeData ();

		if ($trade ['__order__'] ['paytype'] != $payment ['id']) {
			return 'VERIFY_FAILED';
		}
		
		//TODO 20150107新增测试 金额大于0不支付
		if ($is_flish==1 && $trade ['price'] > 0) {
			return 'VERIFY_FAILED';
		}
		return $trade ['status'];
	}
	public function GetTradeData() {
		$sign = post ( 'sign', 'number' );
		$sign || $sign = get ( 'sign', 'number' );
		$order = logic ( 'callback' )->Bridge ( $sign )->SrcOne ( $sign );
		$order && $product = logic ( 'product' )->SrcOne ( $order ['productid'] );
		$trade = array ();
		$trade ['sign'] = $sign;
		$trade ['trade_no'] = time ();
		$trade ['price'] = $order ['paymoney'];
		$trade ['money'] = 0;
		$trade ['nmadd'] = true;
		$trade ['status'] = ($product ['type'] == 'ticket') ? 'TRADE_FINISHED' : 'WAIT_SELLER_SEND_GOODS';
		$trade ['__order__'] = $order;
		return $trade;
	}
	public function StatusProcesser($status) {
		return false;
	}
	public function GoodSender($payment, $express, $sign, $type) {
		if ($type == 'ticket') {
			logic ( 'callback' )->Bridge ( $sign )->Processed ( $sign, 'TRADE_FINISHED' );
		} else {
			logic ( 'callback' )->Bridge ( $sign )->Processed ( $sign, 'WAIT_BUYER_CONFIRM_GOODS' );
		}
	}
}

?>