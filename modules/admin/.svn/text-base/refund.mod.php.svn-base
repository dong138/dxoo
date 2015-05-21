<?php

/**
 * 模块：退款申请管理
 * @package module
 * @name refund.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}

	function Main()
	{
		$this->CheckAdminPrivs('refund');
		$status = get('status', 'int');
		$list = logic('refund')->GetList($status);
		include handler('template')->file('@admin/refund_list');
	}

	function Process()
	{
		$this->CheckAdminPrivs('refund');
		$id = get('id', 'number');
		$order = logic('order')->GetOne($id);
		if (!$order)
		{
			$this->Messager(__('找不到相关订单！'), '?mod=order');
		}
		$user 	 = user($order['userid'])->get();
		$payment = logic('pay')->SrcOne($order['paytype']);
		$paylog  = logic('pay')->GetLog($order['orderid'], $order['userid']);
		$coupons = logic('coupon')->SrcList($order['userid'], $order['orderid'], TICK_STA_ANY);
		$express = logic('express')->SrcOne($order['expresstype']);
		$address = logic('address')->GetOne($order['addressid']);
		$refund  = logic('refund')->GetOne($order['orderid']);
		$order['ypaymoney'] = ($order['totalprice'] > $order['paymoney']) ? number_format(($order['totalprice'] - $order['paymoney']),2) : 0;
		$order['tpaymoney'] = $order['totalprice'];
		if($order['product']['type'] == 'ticket'){
			$coupo = logic('coupon')->SrcList($order['userid'], $id);
			if($order['productnum'] != count($coupo) && $coupo[0]['mutis'] == 1){
				$order['tpaymoney'] = count($coupo)*$order['productprice'];
				$order['tmsg'] = array(
					'money' => $order['paymoney'],
					'tnum' => $order['productnum'],
					'num' => $order['productnum']-count($coupo)
				);
			}
		}
		include handler('template')->file('@admin/refund_process');
	}
		function apply()
	{
		$this->CheckAdminPrivs('refund');
		$id = post('oid', 'number');
		$rfm = post('money', 'float');
		$reason = post('reason', 'string');
		if ($rfm <= 0) {
			$this->Messager('退款金额输入错误');
		}

		if (is_numeric($rfm))
		{
			$remark .= '；退款金额：'.$rfm;
		}
		else
		{
			$rfm = null;
		}
		$order = logic('order')->GetOne($id);
		if($id && $order){
			if($order['product']['type'] == 'ticket'){
				$coupo = logic('coupon')->SrcList($order['userid'], $id);
				if($order['productnum'] != count($coupo) && $coupo[0]['mutis'] == 1){
					$order['totalprice'] = count($coupo)*$order['productprice'];
				}
			}
			if($rfm > $order['totalprice']){
				$this->Messager('退款金额不能大于'.$order['totalprice']);
			}
			logic('refund')->agree($id, $order['userid'], $rfm, $reason);
			logic('order')->clog($id)->add('refund', $remark);
			logic('order')->Refund($id, $rfm);
			$this->Messager('同意退款成功!', 'admin.php?mod=refund');
		}else{
			$this->Messager('操作错误');
		}
	}

		function refuse()
	{
		$this->CheckAdminPrivs('refund');
		$id = post('oid', 'number');
		$reason = post('reason', 'string');
		if (empty($reason)) {
			$this->Messager('请填写拒绝原因');
		}

		$order = logic('order')->GetOne($id);
		if($id && $order){
			logic('refund')->refuse($id, $order['userid'], $reason);
			$this->Messager('拒绝退款成功!', 'admin.php?mod=refund');
		}else{
			$this->Messager('操作错误');
		}
	}
}


?>