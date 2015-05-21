<?php

/**
 * 逻辑区：优惠券分类相关
 * @package logic
 * @name ticket.logic.php
 * @version 1.0
 */
class TicketLogic {
	
	//多个获取
	function get_list($filter = null) {
		$sql = 'SELECT * 
				FROM ystttuangou_ticket_project 
				WHERE 
					'.$filter.' 
				ORDER BY id DESC';
		return dbc(DBCMax)->query(page_moyo($sql))->done();
	}
	
	//单个获取
	public function get_one($id = null) {
		return dbc(DBCMax)->select('ticket_project')->where(array('id' => $id))->limit(1)->done();
	}
	
	//获取劵分类
	public function get_category($filter = null){
		return dbc (DBCMax)->select ('ticket_category')->where($filter)->done();
	}
	
	//添加
	public function add($rebate = null){
		return dbc(DBCMax)->insert('ticket_project')->data($rebate)->done();
	}
	
	//更新
	public function update($id, $rebate = null) {
		return dbc(DBCMax)->update('ticket_project')->data($rebate)->where(array('id' => $id))->done();
	}

	//删除
	public function delete ( $id = null ) {
		$rels = logic('ticket')->GetRels($id);
		//var_dump($rels);exit;
		if($rels){
			foreach($rels as $v){
				logic('ticket')->DeleteRel($v['projectid'], $v['productid']);
			}
		}
		return dbc ( DBCMax )->delete ( 'ticket_project' )->where ( array ( 'id' => $id ) )->done ( );
	}

	//有关键字查询
	public function product_list ( $key ,$extend = '1' , $limit = 0 ){
		$sql_key = $sql_dsp = $sql_time = '1';
		$now = time();
		$sql_dsp = ' p.display <> '.PRO_DSP_None;
		$sql_time = ' p.begintime < ' . $now . ' AND p.overtime > ' . $now;
		if($key){
			$sql_key ='(p.name like "%'.$key.'%" OR p.id like "%'.$key.'%" OR p.flag like "%'.$key.'%")';
		}
		$sql = 'SELECT p.id ,p.sellerid ,p.category ,p.flag ,s.sellername 
				FROM ystttuangou_product p 
				LEFT JOIN ystttuangou_seller s 
				ON p.sellerid = s.id 
				WHERE 
					'. $sql_key .' AND '.$sql_dsp.' AND '.$sql_time.' AND '.$extend.'
				ORDER BY p.`id` asc ';
		if (!$limit) $sql = page_moyo($sql);
		return dbc ( DBCMax )->query ( $sql )->done ( );
	}
	
	//无关键字查询
	public function ticket_list($projectid ,$limit = 0){
		$sql = 'SELECT r.projectid,p.id ,p.sellerid ,p.category ,p.flag,s.sellername 
				FROM ystttuangou_ticket_relevance r,ystttuangou_product p,ystttuangou_seller s 
				WHERE r.productid =p.id and p.sellerid = s.id and r.projectid = '.$projectid.' 
				ORDER BY p.`id` desc';
		if (!$limit) $sql = page_moyo($sql);
		return dbc ( DBCMax )->query ( $sql )->done ();
	}

// 	public function pullSellerId($projectid){
// 		$sql = 'SELECT DISTINCT 
// 					p.sellerid 
// 				FROM 
// 					ystttuangou_product p 
// 				LEFT JOIN 
// 					ystttuangou_ticket_relevance r 
// 				ON 
// 					p.id = r.productid 
// 				WHERE 
// 					r.projectid='.$projectid
// 				.' ORDER BY p.sellerid';
// 		return dbc ( DBCMax )->query ( $sql )->done ();
// 	}
	public function sellers($projectid){
		$product = logic ( 'ticket' )->ticket_list ( $projectid ,10 );
		foreach($product as $val){
			$seid[$val['sellername']] = $val['sellerid'];
		}
		$seid = array_unique($seid);
		$seid = array_flip($seid);
		foreach($seid as $k => $v){
			$sellers[$k] = logic('sellerTicket')->get_seller('sellerid= '.$k);
			if($sellers[$k]){
				$sellers[$k]['sellername'] = $v;
			}else{
				unset($sellers[$k]);
			}
		}
// 		var_dump($sellers);
		return $sellers;
	}
	//判断关联状态
	public function Exists($projectid,$productid) {
		$result = $this->GetOne ( $projectid,$productid );
		return $result ? true : false;
	}
	
	public function GetOne( $projectid,$productid ) {
		return dbc ( DBCMax )->select ( 'ticket_relevance' )->where ( 'projectid='.$projectid . ' and productid=' . $productid )->limit ( 1 )->done ();
	}
	
	public function GetRels( $projectid ) {
		return dbc ( DBCMax )->select ( 'ticket_relevance' )->where ( 'projectid='.$projectid )->done ();
	}
	
	public function addRels( $data ) {
		return dbc ( DBCMax )->insert ( 'ticket_relevance' )->data ( $data )->done ();
	}
	
	//进行关联
	public function addRel( $projectid,$productid ) {
		return dbc ( DBCMax )->insert ( 'ticket_relevance' )->data ( array (
				'projectid' => $projectid,
				'productid' => $productid,
				'upstime' => time ()
		) )->done ();
	}
	
	//取消关联
	public function DeleteRel( $projectid,$productid ) {
		return dbc ( DBCMax )->delete ( 'ticket_relevance' )->where ( ( 'projectid='.$projectid . ' and productid=' . $productid ) )->done ();
	}
	
	public function Filter($cityid) {
		$list =logic ( 'city' )->get_places($cityid);
		$sIDS = $cityid > 0 ? $cityid . ',' : '';
		foreach ($list as $i => $value){
			$sIDS.= $value['id'].',';
		}
		$sIDS = substr ( $sIDS, 0, - 1 );
		if ($sIDS) {
			return 'p.city IN(' . $sIDS . ')';
		} else {
			return '1';
		}
	}
	
	public function add_seller_num($data){
		return dbc ( DBCMax )->insert ( 'seller_num' )->data ( $data )->done ();
	}
	
}

?>