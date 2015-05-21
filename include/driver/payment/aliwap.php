<?php

/**
 * 支付方式：支付宝手机wap
 * @package payment
 * @name aliwap.php
 * @version 1.0
 */

class aliwapPaymentDriver extends PaymentDriver
{
	private $is_notify = null;
	private $alireturn = array ();
	public function CreateLink($payment, $parameter)
	{
		require_once("alipay.config.php");
		require_once("lib/alipay_submit.class.php");
		//返回格式
		$format = "xml";
		//必填，不需要修改
		
		//返回格式
		$v = "2.0";
		//必填，不需要修改
		
		//请求号
		$req_id = date('Ymdhis');
		//必填，须保证每次请求都是唯一
		
		//**req_data详细信息**
		
		//服务器异步通知页面路径
		$notify_url = $parameter['notify_url'];
		//需http://格式的完整路径，不允许加?id=123这类自定义参数
		
		//页面跳转同步通知页面路径
		$call_back_url =  $parameter['notify_url'];
		//需http://格式的完整路径，不允许加?id=123这类自定义参数
		
		//操作中断返回地址
		$merchant_url =  $parameter['notify_url'];
		//用户付款中途退出返回商户的地址。需http://格式的完整路径，不允许加?id=123这类自定义参数
		
		//卖家支付宝帐户
		$seller_email = "710024120@qq.com";
		//必填
		
		//商户订单号
		$out_trade_no =  $parameter['sign'];
		//商户网站订单系统中唯一订单号，必填
		
		//订单名称
		$subject = $parameter['name'];
		//必填
		
		//付款金额
		$total_fee = $parameter['price'];
		//必填
		
		//请求业务参数详细
		$req_data = '<direct_trade_create_req><notify_url>' . $notify_url . '</notify_url><call_back_url>' . $call_back_url . '</call_back_url><seller_account_name>' . $seller_email . '</seller_account_name><out_trade_no>' . $out_trade_no . '</out_trade_no><subject>' . $subject . '</subject><total_fee>' . $total_fee . '</total_fee><merchant_url>' . $merchant_url . '</merchant_url></direct_trade_create_req>';
		//必填
		
		/************************************************************/
		
		//构造要请求的参数数组，无需改动
		$para_token = array(
				"service" => "alipay.wap.trade.create.direct",
				"partner" => trim($alipay_config['partner']),
				"sec_id" => trim($alipay_config['sign_type']),
				"format"	=> $format,
				"v"	=> $v,
				"req_id"	=> $req_id,
				"req_data"	=> $req_data,
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
		
		//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestHttp($para_token);
		
		//URLDECODE返回的信息
		$html_text = urldecode($html_text);
		
		//解析远程模拟提交后返回的信息
		$para_html_text = $alipaySubmit->parseResponse($html_text);
		
		//获取request_token
		$request_token = $para_html_text['request_token'];
		
		
		/**************************根据授权码token调用交易接口alipay.wap.auth.authAndExecute**************************/
		
		//业务详细
		$req_data = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';
		//必填
		
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service" => "alipay.wap.auth.authAndExecute",
				"partner" => trim($alipay_config['partner']),
				"sec_id" => trim($alipay_config['sign_type']),
				"format"	=> $format,
				"v"	=> $v,
				"req_id"	=> $req_id,
				"req_data"	=> $req_data,
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
		
		//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);
		return $alipaySubmit->buildRequestForm($parameter, 'get', '确认');

	}
	
	public function CreateConfirmLink($payment, $order){
		return '?mod=buy&code=tradeconfirm&id=' . $order ['orderid'];
	}
	
	private function __Is_Nofity()
	{
		if (is_null($this->is_notify))
		{
			if (post('trade_status'))
			{
				$this->is_notify = true;
			}
			else
			{
				$this->is_notify = false;
			}
		}
		return $this->is_notify;
	}
	
	public function CallbackVerify($payment){
		if ($this->__Is_Nofity())
		{
			//后台
			sleep(rand(1, 9));
			$trade_status = $this->__Notify_Verify($payment);
		}
		else
		{
			//前台
			$trade_status = $this->__Return_Verify($payment);
		}
		return $trade_status;
	}
	
	private function __Return_Verify($payment)
	{
		require_once("alipay.config.php");
		require_once("lib/alipay_notify.class.php");
		$alipayNotify = new AlipayNotify($alipay_config);
// 		var_dump($alipayNotify);
		$verify_result = $alipayNotify->verifyReturn();
		if($verify_result) {
			//验证成功
			//商户订单号
			$out_trade_no = $_GET['out_trade_no'];
			
			//支付宝交易号
			$trade_no = $_GET['trade_no'];
			
			//交易状态
			$result = $_GET['result'];
			
			$this->alireturn ['sign'] = $out_trade_no;
			$this->alireturn ['trade_no'] = $trade_no;
			
			$order = logic ( 'order' )->GetOne ( $out_trade_no );
			if ($order && $order ['paytype'] == $payment ['id']) {
				if ($order ['product'] ['type'] == 'ticket') {
					//这个返回后得到的是已经付款
					return 'TRADE_FINISHED';
				} else {
					return 'WAIT_SELLER_SEND_GOODS';
				}
			} else {
				return 'VERIFY_FAILED';
			}
		}
		else {
			return "VERIFY_FAILED";
		}
	}
	
	private function __Notify_Verify($payment)
	{
		require_once("alipay.config.php");
		require_once("lib/alipay_notify.class.php");
		//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();
		
		if($verify_result) {//验证成功
			$doc = new DOMDocument();
			if ($alipay_config['sign_type'] == 'MD5') {
				$doc->loadXML($_POST['notify_data']);
			}
		
			if ($alipay_config['sign_type'] == '0001') {
				$doc->loadXML($alipayNotify->decrypt($_POST['notify_data']));
			}
		
			if( ! empty($doc->getElementsByTagName( "notify" )->item(0)->nodeValue) ) {
				//商户订单号
				$out_trade_no = $doc->getElementsByTagName( "out_trade_no" )->item(0)->nodeValue;
				//支付宝交易号
				$trade_no = $doc->getElementsByTagName( "trade_no" )->item(0)->nodeValue;
				//交易状态
				$trade_status = $doc->getElementsByTagName( "trade_status" )->item(0)->nodeValue;
				
				$total_fee = $doc->getElementsByTagName( "total_fee" )->item(0)->nodeValue;
				
				$this->alireturn ['sign'] = $out_trade_no;
				$this->alireturn ['trade_no'] = $trade_no;
				$this->alireturn ['trade_status'] = $trade_status;
				$this->alireturn ['total_fee'] = $total_fee;
				
		
				if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
					$order = logic ( 'order' )->GetOne ( $out_trade_no );
					if ($order && $order ['paytype'] == $payment ['id']) {
						if ($order ['product'] ['type'] == 'ticket') {
							//这个返回后得到的是已经付款
							return 'TRADE_FINISHED';
						} else {
							return 'WAIT_SELLER_SEND_GOODS';
						}
					} else {
						return 'VERIFY_FAILED';
					}
				}
			}else{
				return "VERIFY_FAILED";
			}
		}
		else {
			return "VERIFY_FAILED";
		}
	}
	
	public function GetTradeData()
	{
		$trade = array();
		$trade['sign'] = $this->alireturn ['sign'];
		$trade['trade_no'] = $this->alireturn ['trade_no'];
		$trade['price'] = $this->alireturn ['total_fee'];
		$trade['money'] = $this->alireturn ['total_fee'];
		$trade['status'] = $this->alireturn ['trade_status'];
		return $trade;
	}
	
	public function StatusProcesser($status)
	{
		if (!$this->__Is_Nofity())
		{
			return false;
		}
		if ($status != 'VERIFY_FAILED')
		{
			echo 'success';
		}
		else
		{
			echo 'failed';
		}
		return true;
	}
	
	public function GoodSender($payment, $express, $sign, $type){
		if ($type == 'ticket') {
			logic ( 'callback' )->Bridge ( $sign )->Processed ( $sign, 'TRADE_FINISHED' );
		} else {
			logic ( 'callback' )->Bridge ( $sign )->Processed ( $sign, 'WAIT_BUYER_CONFIRM_GOODS' );
		}
	}
}

?>