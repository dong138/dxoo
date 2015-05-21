<?php

/**
 * 逻辑区：商家优惠券
* @package logic
* @name sellerTicket.logic.php
* @version 1.0
*/
class sellerTicketLogic {

	//多个获取
	function get_list($filter = null, $limit = 0) {
		$sql = 'SELECT n.*, s.sellername 
				FROM ystttuangou_seller_num n 
				LEFT JOIN 
					ystttuangou_seller s 
				ON 
					n.sellerid = s.id
				WHERE
					'.$filter.'
				ORDER BY n.`id` DESC';
// 		echo($sql);
		if (!$limit) $sql = page_moyo($sql);
		return dbc(DBCMax)->query($sql)->done();
	}

	//单个获取
	public function get_one($id) {
// 		return dbc(DBCMax)->select('seller_num')->where(array('id' => $id))->limit(1)->done();
		$sql = 'SELECT 
					n.*, s.sellername
				FROM 
					ystttuangou_seller_num n
				LEFT JOIN
					ystttuangou_seller s
				ON
					n.sellerid = s.id
				WHERE
					n.id = ' . $id;
// 		echo($sql);exit;
		$res = dbc(DBCMax)->query($sql)->limit(1)->done();
		return $res;
	}
	public function get_seller($filter = null){
		return dbc (DBCMax)->select ('seller_num')->where($filter)->limit(1)->done();
	}
	
	//获取劵分类
	public function get_project($filter = null){
		return dbc (DBCMax)->select ('ticket_project')->where($filter)->done();
	}

	//添加
	public function add($rebate = null){
		return dbc(DBCMax)->insert('seller_num')->data($rebate)->done();
	}

	//更新
	public function update($id, $rebate = null) {
		return dbc(DBCMax)->update('seller_num')->data($rebate)->where(array('id' => $id))->done();
	}

}

?>