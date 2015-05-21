<?php

/**
 * 逻辑区：商家分店相关
 * @package logic
 * @name branch.logic.php
 * @version 1.0
 */
class BranchLogic {
	function GetList() {
		$sql = 'SELECT *
		FROM ' . table ( 'branch' ) . '
		ORDER BY addstime DESC';
		$sql = page_moyo ( $sql );
		return dbc ( DBCMax )->query ( $sql )->done ();
	}
	public function Exists($sid) {
		$result = $this->GetOne ( $sid );
		return $result ? true : false;
	}
	public function GetOne($sid = null) {
		return dbc ( DBCMax )->select ( 'seller_branch' )->where ( 'sellerid='.$sid )->limit ( 1 )->done ();
	}
	public function Search($where, $limit = 1) {
		$dbo = dbc ( DBCMax )->select ( 'seller_branch' )->where ( $where );
		$limit && $dbo->limit ( $limit );
		return $dbo->done ();
	}
	public function Add( $rebate = array()) {
		extract ( $rebate );
		$data = array (
				'name' => strip_tags ( $name ),
				'addstime' => time()
		);
		dbc ()->SetTable ( table ( 'branch' ) );
		return dbc ()->Insert ( $data );
	}
	
	public function Addseller( $sellerid,$branchid ) {
		return dbc ( DBCMax )->insert ( 'seller_branch' )->data ( array (
				'sellerid' => $sellerid,
				'branchid' => $branchid,
				'upstime' => time () 
		) )->done ();
	}
	public function branch_list($id){
		$sql = 'select s.* from  ystttuangou_seller_branch  b left join ystttuangou_seller s on b.sellerid = s.id where b.branchid='.$id.' order by upstime desc ';
		return dbc ( DBCMax )->query ( $sql )->done ();
	}
	public function seller_list($branchid,$key){
		$sql = 'select s.* from ystttuangou_seller s left join ystttuangou_seller_branch b on s.id = b.sellerid 
				where s.`enabled` = "true" and s.is_agent=0 and (s.sellername like "%'.$key.'%" or s.id like "%'.$key.'%") and ( branchid='.$branchid.' or  branchid is null ); ';
		return dbc ( DBCMax )->query ( $sql )->done ();
	}
	private function Delete_where($where) {
		return dbc ( DBCMax )->delete ( 'seller_branch' )->where ( $where )->done ();
	}
	public function Delete( $sellerid,$branchid) {
		$branch = $this->Search ( 'sellerid='.$sellerid . ' and branchid=' . $branchid );
		if (! $branch)
			return false;
		$this->Delete_where ( 'sellerid='.$sellerid . ' and branchid=' . $branchid );
		return true;
	}
}
?>