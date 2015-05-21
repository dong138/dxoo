<?php

/**
 * 逻辑区：查询
 * @package logic
 * @name search.logic.php
 * @version 1.0
 */
class searchLogic {
	
	public function GetLike( $filter = 1 ,$table='seller' ,$in='id')
	{
		$sql = dbc(DBCMax)->select($table)->where($filter)->in($in);
		$sql = dbc(DBCMax)->sql($sql);
		$results = dbc(DBCMax)->query($sql)->done();
		return $results;
	}
	
	public function GetList($limit = 0, $filter = 1)
	{
		if ($limit) {
			$sql = dbc(DBCMax)->select('seller')->where($filter)->order('id.desc')->limit($limit);
		}else{
			$sql = dbc(DBCMax)->select('seller')->where($filter)->order('id.desc');
		}
		$sql = dbc(DBCMax)->sql($sql);
		//if (!$limit) $sql = page_moyo($sql);
		$results = dbc(DBCMax)->query($sql)->done();
		return $results;
	}
	public function GetOne($id) {
		return dbc ( DBCMax )->select ( 'seller' )->where ( 'id=' . $id )->limit ( 1 )->done ();
	}
	public function Search($where, $limit = 1) {
		$dbo = dbc ( DBCMax )->select ( 'seller' )->where ( $where );
		$limit && $dbo->limit ( $limit );
		return $dbo->done ();
	}
	public function Update($id, $data)
	{
		dbc()->SetTable(table('seller'));
		$id = dbc()->Update($data, 'id = '.$id);
		return $id;
	}
}

?>
