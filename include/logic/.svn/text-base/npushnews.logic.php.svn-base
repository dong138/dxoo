<?php

/**
 * 逻辑区：天数/次数管理
 * @package logic
 * @name npushnews.logic.php
 * @version 1.0
 */

class NpushnewsLogic
{
	
	public function get_list($limit = 0, $filter = 1)
	{
		if ($limit) {
			$sql = dbc(DBCMax)->select('d_num')->where($filter)->order('addtime.desc')->limit($limit);
		}else{
			$sql = dbc(DBCMax)->select('d_num')->where($filter)->order('addtime.desc');
		}
		$sql = dbc(DBCMax)->sql($sql);
		if (!$limit) $sql = page_moyo($sql);
		$results = dbc(DBCMax)->query($sql)->done();
		return $results;
	}
	
	public function get_one($id)
	{
		return dbc(DBCMax)->select('d_num')->where(array('id' => $id))->limit(1)->done();
	}
	public function update($id, $data)
	{
		dbc()->SetTable(table('d_num'));
		$id = dbc()->Update($data, 'id = '.$id);
		return $id;
	}
	public function create($rebate = array()) {
		return dbc(DBCMax)->insert('d_num')->data($rebate)->done();
	}
	
	public function delete($id)
	{
		$results=dbc(DBCMax)->select('d_times')->where(array('numid' => $id))->limit(1)->done();
		if($results){
			return false;
		}
		return dbc(DBCMax)->delete('d_num')->where(array('id' => $id))->done();
	}
	
	//************************模块和时间段*******************************
	public function get_model($filter = 1)
	{
		$sql = dbc(DBCMax)->select('d_model')->where($filter);
		$sql = dbc(DBCMax)->sql($sql);
		$results = dbc(DBCMax)->query($sql)->done();
		return $results;
	}
	public function get_type($filter = 1)
	{
		$sql = dbc(DBCMax)->select('d_type')->where($filter);
		$sql = dbc(DBCMax)->sql($sql);
		$results = dbc(DBCMax)->query($sql)->done();
		return $results;
	}
	public function get_type_one($id)
	{
		return dbc(DBCMax)->select('d_type')->where(array('id' => $id))->limit(1)->done();
	}
	public function get_times_list($modelid = 0, $numid = 0)
	{
		$filter=1;
		if ($numid > 0 && $modelid > 0) {
			$filter=' t.numid='.$numid .' AND t.modelid='.$modelid;
		}else{
			return false;
		}
		
		$sql = 'SELECT t.*,n.num,n.name as numname, m.name as modelname
		FROM
			'.table('d_times').' t
		LEFT JOIN
			'.table('d_num').' n
		ON
			n.id = t.numid
		LEFT JOIN
			'.table('d_model').' m
		ON
			m.id = t.modelid
		WHERE
			'.$filter.'
		ORDER BY
			t.id';
		$results = dbc(DBCMax)->query($sql)->done();
		return $results;
	}
	public function get_times_one($id)
	{
		return dbc(DBCMax)->select('d_times')->where(array('id' => $id))->limit(1)->done();
	}
	
	public function times_update($id, $data)
	{
		dbc()->SetTable(table('d_times'));
		$id = dbc()->Update($data, 'id = '.$id);
		return $id;
	}
	public function times_delete($id)
	{
		$results=dbc(DBCMax)->select('d_relevance')->where(array('timesid' => $id))->limit(1)->done();
		if($results){
			return false;
		}
		return dbc(DBCMax)->delete('d_times')->where(array('id' => $id))->done();
	}
	public function times_create($rebate = array()) {
		return dbc(DBCMax)->insert('d_times')->data($rebate)->done();
	}
	
	public function Exists($typeid=0,$commonid=0,$timesid=0) {
		$where = array(
				'typeid'=>$typeid,
				'commonid'=>$commonid,
				'timesid'=>$timesid
		);
		$result = dbc ( DBCMax )->select ( 'd_relevance' )->where ( $where )->limit ( 1 )->done ();
		return $result ? true : false;
	}
	
	public function common_list($keyword,$type = array()){
		$field = explode ( ',', $type ['field'] );
		foreach ( $field as $key => $val ) {
			$filter.=$val.' like "%'.$keyword.'%" or ';
		}
		$filter=substr($filter,0,-3);
		if($type['table'] == 'product'){
			$filter .= ' AND display <> '.PRO_DSP_None.' AND begintime < ' . time() . ' AND overtime > ' . time();
		}
		
		$sql = dbc(DBCMax)->select($type['table'])->in($type ['in'])->where($filter);
		$sql = dbc(DBCMax)->sql($sql);
		$sql = page_moyo ( $sql );
		$results = dbc(DBCMax)->query($sql)->done();
		return $results;
	}
	public function get_title($typeid = 0 ,$id = 0 ){
		$type = dbc ( DBCMax )->select ( 'd_type' )->where ( array( 'id'=>$typeid ) )->limit ( 1 )->done ();
		
		$sql = dbc(DBCMax)->select($type['table'])->in($type ['in'])->where(array( 'id'=>$id ));
		$sql = dbc(DBCMax)->sql($sql);
		$results = dbc(DBCMax)->query($sql)->limit ( 1 )->done ();
		return $results;
	}
	public function relevance_list($timesid,$typeid){
		$sql_limit_city = '1';
		if ($timesid > 0 && $typeid > 0 ) {
			$sql_limit_city = ' ( timesid = ' . $timesid . ' AND typeid='.$typeid.' ) ';
		}
		$sql = 'SELECT r.id as rid,r.commonid as id,r.order,r.upstime,t.id as tid,t.name as title
		FROM
			' . table ( 'd_relevance' ) . ' r
		LEFT JOIN
			' . table ( 'd_type' ) . ' t
		ON
			(r.typeid = t.id)
		WHERE
			'. $sql_limit_city .'
		ORDER BY
			r.order';
		$sql = page_moyo ( $sql );
		return dbc ( DBCMax )->query ( $sql )->done ();
	}
	public function relevance_add($rebate = array())
	{
		return dbc(DBCMax)->insert('d_relevance')->data($rebate)->done();
	}
	public function relevance_update($id, $data)
	{
		dbc()->SetTable(table('d_relevance'));
		$id = dbc()->Update($data, 'id = '.$id);
		return $id;
	}
	public function relevance_delete($rebate = array())
	{
		return dbc(DBCMax)->delete('d_relevance')->where($rebate)->done();
	}
	
}

?>