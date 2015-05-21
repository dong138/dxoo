<?php

/**
 * 支付方式：网银在线
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package payment
 * @name chinabank.php
 * @version 1.0
 */
class guofubaoPaymentDriver extends PaymentDriver {
	private $gfbreturn = array ();
	private $is_notify = null;
	public function CreateLink($payment, $parameter) {
		require_once ("HttpClient.class.php");
		$version = "2.1";
		$charset = "2"; // utf-8
		$language = "1"; // 中文
		$signType = "1"; // md5
		$tranCode = "8888"; // 交易代码
		$merchantID = "0000003358"; // 商户代码
		
		$merOrderNum = $parameter ['sign'];
		$tranAmt = $parameter ['price'];
		$feeAmt = "0"; // 佣金
		$currencyType = "156"; // 币种
		$backgroundMerUrl = $parameter ['notify_url']; // 后台回调地址
		$frontMerUrl = $parameter ['notify_url'];
		$tranDateTime = date ( "YmdHis" );
		$virCardNoIn = "0000000001000000584"; // 转入账户
		$tranIP = client_ip ();
		$isRepeatSubmit = "1"; // 不允许重复提交订单
		
		$goodsName = preg_replace ( '/\&[a-z]{2,4}\;/i', '', $parameter ['name'] );
		$goodsDetail = str_replace ( array (
				'"',
				"'",
				'\\',
				'&' 
		), '', $parameter ['detail'] );
		$buyerName = $parameter ['userid'];
		$buyerContact = "";
		$merRemark1 = $parameter ['userid'];
		$merRemark2 = "dianshang";
		// $bankCode = $_POST["bankCode"];
		// $userType = $_POST["userType"];
		$gopayServerTime = HttpClient::getGopayServerTime ();
		
		$bankCode = $parameter ['bankCode'];
		
		$signStr = 'version=[' . $version . ']tranCode=[' . $tranCode . ']merchantID=[' . $merchantID . ']merOrderNum=[' . $merOrderNum . ']tranAmt=[' . $tranAmt . ']feeAmt=[' . $feeAmt . ']tranDateTime=[' . $tranDateTime . ']frontMerUrl=[' . $frontMerUrl . ']backgroundMerUrl=[' . $backgroundMerUrl . ']orderId=[]gopayOutOrderId=[]tranIP=[' . $tranIP . ']respCode=[]gopayServerTime=[' . $gopayServerTime . ']VerficationCode=[12345678]';
		// VerficationCode是商户识别码为用户重要信息请妥善保存
		// 注意调试生产环境时需要修改这个值为生产参数
		
		$signValue = md5 ( $signStr );
		
		$def_url = '<br/><b>点击下面的 “银行卡支付” 后可以在新打开窗口中选择您的银行卡类型进行支付！</b>';
		$def_url .= '<form id="pay_submit" name="guofubaosubmit" action="https://mertest.gopay.com.cn/PGServer/Trans/WebClientAction.do" method=post target="_blank">';
		$def_url .= "<input type='hidden' id='version' name='version' value='" . $version . "' />";
		$def_url .= "<input type='hidden' id='charset' name='charset' value='" . $charset . "'  />";
		$def_url .= "<input type='hidden' id='language' name='language' value='" . $language . "'  />";
		$def_url .= "<input type='hidden' id='signType' name='signType' value='" . $signType . "'  />";
		$def_url .= "<input type='hidden' id='tranCode' name='tranCode' value='" . $tranCode . "'  />";
		$def_url .= "<input type='hidden' id='merchantID' name='merchantID' value='" . $merchantID . "'  />";
		$def_url .= "<input type='hidden' id='merOrderNum' name='merOrderNum' value='" . $merOrderNum . "'   />";
		$def_url .= "<input type='hidden' id='tranAmt' name='tranAmt' value='" . $tranAmt . "'  />";
		$def_url .= "<input type='hidden' id='feeAmt' name='feeAmt' value='" . $feeAmt . "'  />";
		$def_url .= "<input type='hidden' id='currencyType' name='currencyType' value='" . $currencyType . "'  />";
		$def_url .= "<input type='hidden' id='frontMerUrl' name='frontMerUrl' value='" . $frontMerUrl . "'  />";
		$def_url .= "<input type='hidden' id='backgroundMerUrl' name='backgroundMerUrl' value='" . $backgroundMerUrl . "'  />";
		$def_url .= "<input type='hidden' id='tranDateTime' name='tranDateTime' value='" . $tranDateTime . "'  />";
		$def_url .= "<input type='hidden' id='virCardNoIn' name='virCardNoIn' value='" . $virCardNoIn . "'  />";
		$def_url .= "<input type='hidden' id='tranIP' name='tranIP' value='" . $tranIP . "'  />";
		$def_url .= "<input type='hidden' id='isRepeatSubmit' name='isRepeatSubmit' value='" . $isRepeatSubmit . "'  />";
		$def_url .= "<input type='hidden' id='goodsName' name='goodsName' value='" . $goodsName . "'  />";
		$def_url .= "<input type='hidden' id='goodsDetail' name='goodsDetail' value='" . $goodsDetail . "'  />";
		$def_url .= "<input type='hidden' id='buyerName' name='buyerName' value='" . $buyerName . "'  />";
		$def_url .= "<input type='hidden' id='buyerContact' name='buyerContact' value='" . $buyerContact . "'  />";
		$def_url .= "<input type='hidden' id='merRemark1' name='merRemark1' value='" . $merRemark1 . "'  />";
		$def_url .= "<input type='hidden' id='merRemark2' name='merRemark2' value='" . $merRemark2 . "'  />";
		$def_url .= "<input type='hidden' id='signValue' name='signValue' value='" . $signValue . "'  />";
		// 下面两个用于银行直连
		$def_url .= "<input type='hidden' id='bankCode' name='bankCode' value='".$bankCode ."' />";
		$def_url .= "<input type='hidden' id='userType' name='userType' value='1' />";
		// 下面一个用于防钓鱼
		$def_url .= "<input type='hidden' id='gopayServerTime' name='gopayServerTime' value='" . $gopayServerTime . "'  />";
		$def_url .= '<input type=submit value="银行卡支付" class="formbutton formbutton_ask" onclick="javascript:$.hook.call(\'pay.button.click\');">';
		$def_url .= "</form>";
		
		return $def_url;
	}
	public function CreateConfirmLink($payment, $order) {
		return '?mod=buy&code=tradeconfirm&id=' . $order ['orderid'];
	}
	private function __Is_Nofity() {
// 		if (handler ( 'cookie' )->GetVar('gfb') != null && handler ( 'cookie' )->GetVar('gfb') != '') {
		if (!is_null($this->is_notify)) {
			//后台处理
			return true;
		}else{
			//前台处理
			$this->is_notify = false;
			return $this->is_notify;
		}
	}
	public function CallbackVerify($payment) {
		$version = $_POST ["version"];
		$charset = $_POST ["charset"];
		$language = $_POST ["language"];
		$signType = $_POST ["signType"];
		$this->gfbreturn ['tranCode'] = $tranCode = $_POST ["tranCode"];
		$merchantID = $_POST ["merchantID"];
		$this->gfbreturn ['merOrderNum'] = $merOrderNum = $_POST ["merOrderNum"];
		$this->gfbreturn ['tranAmt'] = $tranAmt = $_POST ["tranAmt"];
		$feeAmt = $_POST ["feeAmt"];
		$frontMerUrl = $_POST ["frontMerUrl"];
		$backgroundMerUrl = $_POST ["backgroundMerUrl"];
		$tranDateTime = $_POST ["tranDateTime"];
		$tranIP = $_POST ["tranIP"];
		$respCode = $_POST ["respCode"];
		$msgExt = $_POST ["msgExt"];
		$orderId = $_POST ["orderId"];
		$gopayOutOrderId = $_POST ["gopayOutOrderId"];
		$bankCode = $_POST ["bankCode"];
		$tranFinishTime = $_POST ["tranFinishTime"];
		$this->gfbreturn ['userid'] = $merRemark1 = $_POST ["merRemark1"];
		$merRemark2 = $_POST ["merRemark2"];
		$signValue = $_POST ["signValue"];
			
		// 注意md5加密串需要重新拼装加密后，与获取到的密文串进行验签
		$signValue2 = 'version=[' . $version . ']tranCode=[' . $tranCode . ']merchantID=[' . $merchantID . ']merOrderNum=[' . $merOrderNum . ']tranAmt=[' . $tranAmt . ']feeAmt=[' . $feeAmt . ']tranDateTime=[' . $tranDateTime . ']frontMerUrl=[' . $frontMerUrl . ']backgroundMerUrl=[' . $backgroundMerUrl . ']orderId=[' . $orderId . ']gopayOutOrderId=[' . $gopayOutOrderId . ']tranIP=[' . $tranIP . ']respCode=[' . $respCode . ']gopayServerTime=[]VerficationCode=[12345678]';
		// VerficationCode是商户识别码为用户重要信息请妥善保存
		// 注意调试生产环境时需要修改这个值为生产参数
			
		$signValue2 = md5 ( $signValue2 );
		//前台
		if ($respCode == '0000') {
			$order = logic ( 'order' )->GetOne ( $merOrderNum );
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
		} else {
			return 'VERIFY_FAILED';
		}
		// }
	}
	public function GetTradeData() {
		$trade = array ();
		$return = $this->gfbreturn;
		if ($return && is_array ( $return )) {
			$order = logic ( 'order' )->GetOne ( $return ['merOrderNum'] );
			$trade ['sign'] = $return ['merOrderNum'];
			$trade ['trade_no'] = $return ['merOrderNum'];
			$trade ['price'] = $return ['tranAmt'];
			$trade ['money'] = $return ['tranAmt'];
			$trade ['status'] = ($order ['product'] ['type'] == 'ticket') ? 'TRADE_FINISHED' : 'WAIT_SELLER_SEND_GOODS';
			$trade['uid'] =$return['userid'];
		}
		return $trade;
	}
	public function StatusProcesser($status) {
		if (!$this->__Is_Nofity ()) {
			return false;
		}
		if ($status != 'VERIFY_FAILED') {
// 			handler ( 'cookie' )->SetVar ( 'gfb', '', false );
			$is_notify = true;
			echo 'RespCode=0000|JumpURL=';
		} else {
			$is_notify = false;
			echo 'RespCode=9999|JumpURL=';
		}
		return true;
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