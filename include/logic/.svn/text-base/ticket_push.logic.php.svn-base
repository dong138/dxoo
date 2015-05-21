<?php

/**
 * 逻辑区：优惠券推送相关
 * @package logic
 * @name ticket.logic.php
 * @version 1.0
 */
class Ticket_pushLogic {
	
	//多个获取
	function get_list($filter = null ,$limit=0) {
		$sql = 'SELECT * 
				FROM ystttuangou_ticket_push_category
				WHERE 
					'.$filter.' 
				ORDER BY id DESC';
		if (!$limit) $sql = page_moyo($sql);
		return dbc(DBCMax)->query($sql)->done();
	}
	
	public function get_default($id = null) {
		return dbc(DBCMax)->select('ticket_push_category')->where(array('is_default' => $id))->limit(1)->done();
	}
	
	//单个获取
	public function get_one($id = null) {
		return dbc(DBCMax)->select('ticket_push_category')->where(array('id' => $id))->limit(1)->done();
	}
	
	//获取劵分类
	public function get_project($filter = null){
		return dbc (DBCMax)->select ('ticket_project')->where($filter)->done();
	}
	
	//添加
	public function add($rebate = null){
		return dbc(DBCMax)->insert('ticket_push_category')->data($rebate)->done();
	}
	
	//更新
	public function update($id, $rebate = null) {
		return dbc(DBCMax)->update('ticket_push_category')->data($rebate)->where(array('id' => $id))->done();
	}

	//删除
	public function delete ( $id = null ) {
		$rels = logic('ticket_push')->GetRels($id);
		if($rels){
			foreach ($rels as $v){
				logic('ticket_push')->DeleteRel($v['projectid'], $v['push_categoryid']);
			}
		}
		return dbc ( DBCMax )->delete ( 'ticket_push_category' )->where ( array ( 'id' => $id ) )->done ( );
	}

	//有关键字查询
	public function push_list ( $key , $limit = 0 ){
		$sql_key = '1';
		if($key){
			$sql_key ='(p.name like "%'.$key.'%" OR p.id like "%'.$key.'%" OR p.pic like "%'.$key.'%")';
		}
		$sql = 'SELECT 
					p.id, p.name, p.pic 
				FROM 
					ystttuangou_ticket_project p 
				WHERE 
					' .$sql_key. ' 
				ORDER BY 
					p.id asc';
		if (!$limit) $sql = page_moyo($sql);
		return dbc ( DBCMax )->query ( $sql )->done ( );
	}
	
	//无关键字查询
	public function ticket_push_list($pushid, $limit = 0){
		$sql = 'SELECT 
					p.id, p.name, p.pic , r.id rid, r.push_categoryid pcid, r.projectid, r.order 
				FROM 
					ystttuangou_ticket_project p 
				LEFT JOIN 
					ystttuangou_ticket_push_relevance r 
				ON 
					r.projectid = p.id 
				WHERE 
					r.push_categoryid = ' . $pushid . ' 
				ORDER BY 
					p.id desc';
		if (!$limit) $sql = page_moyo($sql);
		return dbc ( DBCMax )->query ( $sql )->done ();
	}
	
	//判断关联状态
	public function Exists($projectid,$pushid) {
		$result = $this->GetOne ( $projectid,$pushid );
		return $result ? true : false;
	}
	
	public function GetOne( $projectid,$pushid ) {
		return dbc ( DBCMax )->select ( 'ticket_push_relevance' )->where ( 'projectid='.$projectid . ' and push_categoryid=' . $pushid )->limit ( 1 )->done ();
	}
	
	public function GetRels( $pushid) {
		return dbc ( DBCMax )->select ( 'ticket_push_relevance' )->where ( 'push_categoryid='.$pushid)->done ();
	}
	
	//进行关联
	public function addRel( $projectid,$pushid ) {
		return dbc ( DBCMax )->insert ( 'ticket_push_relevance' )->data ( array (
				'projectid' => $projectid,
				'push_categoryid' => $pushid,
				'upstime' => time ()
		) )->done ();
	}
	
	//取消关联
	public function DeleteRel( $projectid,$pushid ) {
		return dbc ( DBCMax )->delete ( 'ticket_push_relevance' )->where ( ( 'projectid='.$projectid . ' and push_categoryid=' . $pushid ) )->done ();
	}
	
	public function relevance_update($id, $data)
	{
		dbc()->SetTable(table('ticket_push_relevance'));
		$id = dbc()->Update($data, 'id = '.$id);
		return $id;
	}
	
}

?>