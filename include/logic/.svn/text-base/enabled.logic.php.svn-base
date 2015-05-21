<?php

/**
 * 逻辑区：文章管理
 * @package logic
 * @name enabled.logic.php
 * @version 1.0
 */

class EnabledLogic
{
	
	public function get_list($limit = 0, $filter = 1)
	{
		if ($limit) {
			$sql = dbc(DBCMax)->select('enabled')->where($filter)->order('addtime.desc')->limit($limit);
		}else{
			$sql = dbc(DBCMax)->select('enabled')->where($filter)->order('addtime.desc');
		}
		$sql = dbc(DBCMax)->sql($sql);
		if (!$limit) $sql = page_moyo($sql);
		$results = dbc(DBCMax)->query($sql)->done();
		return $results;
	}
	
	public function get_one($name)
	{
		return dbc(DBCMax)->select('enabled')->where(array('name' => $name))->limit(1)->done();
	}
	public function update($name, $data)
	{
		dbc()->SetTable(table('enabled'));
		$id = dbc()->Update($data, "name ="."'".$name."'");
		return $id;
	}
	public function create($rebate = array()) {
		return dbc(DBCMax)->insert('enabled')->data($rebate)->done();
	}
	
	public function delete($id)
	{
		return dbc(DBCMax)->delete('enabled')->where(array('id' => $id))->done();
	}
}

?>