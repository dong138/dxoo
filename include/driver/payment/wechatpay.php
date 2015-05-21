<?php
/**
 * 支付方式：支付宝手机wap
 * @package payment
 * @name aliwap.php
 * @version 1.0
 */

class wechatpayPaymentDriver extends PaymentDriver
{
	private $wechatreturn = array ();
	/**
	 * JS_API支付demo
	 * ====================================================
	 * 在微信浏览器里面打开H5网页中执行JS调起支付。接口输入输出数据格式为JSON。
	 * 成功调起支付需要三个步骤：
	 * 步骤1：网页授权获取用户openid
	 * 步骤2：使用统一支付接口，获取prepay_id
	 * 步骤3：使用jsapi调起支付
	 */
	public function CreateLink($payment, $parameter)
	{
		include_once("wechat/WxPayPubHelper/WxPayPubHelper.php");
		
		//使用jsapi接口
		$jsApi = new JsApi_pub();
	
		//=========步骤1：网页授权获取用户openid============
		//通过code获得openid
		//if (!isset($_GET['code']))
		//{
			//触发微信返回code码
			//$url = $jsApi->createOauthUrlForCode(WxPayConf_pub::JS_API_CALL_URL);
			//Header("Location: $url"); 
		//}else
		//{
			//获取code码，以获取openid
		  //  $code = $_GET['code'];
			//$jsApi->setCode($code);
			//$openid = $jsApi->getOpenId();
		//}
		
		//=========步骤2：使用统一支付接口，获取prepay_id============
		//使用统一支付接口
		$unifiedOrder = new UnifiedOrder_pub();
		
		//设置统一支付接口参数
		//设置必填参数
		//appid已填,商户无需重复填写
		//mch_id已填,商户无需重复填写
		//noncestr已填,商户无需重复填写
		//spbill_create_ip已填,商户无需重复填写
		//sign已填,商户无需重复填写
		//微信openid
		$openid = $parameter['openid'];
		//商户订单号
		$out_trade_no =  $parameter['sign'];
		//订单名称
		$body = $parameter['name'];
		//付款金额
		$total_fee = $parameter['price'] * 100;
		
		$unifiedOrder->setParameter("openid","$openid");//商品描述
		$unifiedOrder->setParameter("body","$body");//商品描述
		//自定义订单号，此处仅作举例
		//$timeStamp = time();
		//$out_trade_no = WxPayConf_pub::APPID."$timeStamp";
		$unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号 
		$unifiedOrder->setParameter("total_fee","$total_fee");//总金额
		$unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址 
		$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
		//非必填参数，商户可根据实际情况选填
		//$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号  
		//$unifiedOrder->setParameter("device_info","XXXX");//设备号 
		//$unifiedOrder->setParameter("attach","XXXX");//附加数据 
		//$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
		//$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间 
		//$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记 
		//$unifiedOrder->setParameter("openid","XXXX");//用户标识
		//$unifiedOrder->setParameter("product_id","XXXX");//商品ID
		
		$prepay_id = $unifiedOrder->getPrepayId();
		//=========步骤3：使用jsapi调起支付============
		$jsApi->setPrepayId($prepay_id);
		return $jsApi->getParameters();
		//$jsApiParameters = $jsApi->getParameters();
	}
	
	public function CallbackVerify($payment){
		$trade_status = $this->__Notify_Verify($payment);
		return $trade_status;
	}
	
	private function __Notify_Verify($payment)
	{
		include_once("wechat/log_.php");
		include_once("wechat/WxPayPubHelper/WxPayPubHelper.php");
		
		//使用通用通知接口
		$notify = new Notify_pub();
		
		//存储微信的回调
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		$notify->saveData($xml);
		
		//验证签名，并回应微信。
		//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
		//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
		//尽可能提高通知的成功率，但微信不保证通知最终能成功。
		if($notify->checkSign() == FALSE){
			$notify->setReturnParameter("return_code","FAIL");//返回状态码
			$notify->setReturnParameter("return_msg","签名失败");//返回信息
		}else{
			$notify->setReturnParameter("return_code","SUCCESS");//设置返回码
		}
		$returnXml = $notify->returnXml();
		echo $returnXml;
		//==商户根据实际情况设置相应的处理流程，此处仅作举例=======
		
		//以log文件形式记录回调信息
		$log_ = new Log_();
		$log_name="log/notify_url.log";//log文件路径
		$log_->log_result($log_name,"【接收到的notify通知】:\n".$xml."\n");
		
		if($notify->checkSign() == TRUE)
		{
			$this->wechatreturn ['sign'] = $notify->data["out_trade_no"];
			$this->wechatreturn ['trade_no'] = $notify->data["transaction_id"];
			$this->wechatreturn ['trade_status'] = $notify->data["result_code"];
			$this->wechatreturn ['total_fee'] = $notify->data["total_fee"];
			
			if ($notify->data["return_code"] == "FAIL") {
				//此处应该更新一下订单状态，商户自行增删操作
				$log_->log_result($log_name,"【通信出错】:\n".$xml."\n");
				logger('通信出错');
				return "VERIFY_FAILED";
			}
			elseif($notify->data["result_code"] == "FAIL"){
				//此处应该更新一下订单状态，商户自行增删操作
				$log_->log_result($log_name,"【业务出错】:\n".$xml."\n");
				logger('业务出错');
				return "VERIFY_FAILED";
			}
			else{
				//此处应该更新一下订单状态，商户自行增删操作
				$log_->log_result($log_name,"【支付成功】:\n".$xml."\n");
				logger($notify->data["out_trade_no"]);
				$order = logic ( 'order' )->GetOne ( $notify->data["out_trade_no"] );
				if ($order && $order ['paytype'] == $payment ['id']) {
					if ($order ['product'] ['type'] == 'ticket') {
						//这个返回后得到的是已经付款
						return 'TRADE_FINISHED';
					} else {
						return 'WAIT_SELLER_SEND_GOODS';
					}
				} else {
					logger('paytype error paymentid='.$payment ['id'].'orderpay:'.$order ['paytype']);
					return 'VERIFY_FAILED';
				}
			}
		}else{
			logger('checkSign error');
			return "VERIFY_FAILED";
		}
		//商户自行增加处理流程,
		//例如：更新订单状态
		//例如：数据库操作
		//例如：推送支付完成信息
	}
	
	public function GetTradeData()
	{
		$trade = array();
		$trade['sign'] = $this->wechatreturn ['sign'];
		$trade['trade_no'] = $this->wechatreturn ['trade_no'];
		$trade['price'] = $this->wechatreturn ['total_fee'];
		$trade['money'] = $this->wechatreturn ['total_fee'];
		$trade['status'] = $this->wechatreturn ['trade_status'];
		return $trade;
	}
}

?>