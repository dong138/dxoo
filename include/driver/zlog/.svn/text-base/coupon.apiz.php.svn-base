<?php

/**
 * ZLOG-APIZ：团购券相关
 * @package zlog
 * @name coupon.apiz.php
 * @version 1.0
 */

class couponZLOG extends iMasterZLOG
{
	protected $zlogType = 'coupon';
	public function create($id, $data)
	{
		$name = TUANGOU_STR . '券已生成，号码：'.$data['number'];
		$extra  = '用户ID：'.$data['uid'].'，';
		$extra .= '产品编号：'.$data['productid'].'，';
		$extra .= '订单编号：'.$data['orderid'].'，';
		$extra .= '包含份数：'.$data['mutis'].'<br/>';
		$extra .= '通过 <b>'.(mocod() == 'coupon.add' ? '管理员手动生成' : '系统自动生成').'</b>，';
		$extra .= '生成 <b>'.($id > 0 ? '成功' : '失败').'</b>';
		$this->zlogCreate($data['number'], $name, $extra);
	}
	public function delete($id)
	{
		$data = logic('coupon')->SrcOne($id);
		$this->zlogCreate($data['number'], TUANGOU_STR . '券已被删除，号码：'.$data['number']);
	}
	public function used($data)
	{
		$this->zlogCreate($data['number'], TUANGOU_STR . '券已被消费，号码：'.$data['number']);
	}
}

?>