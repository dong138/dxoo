<?php

/**
 * 逻辑区：活动分类相关
 * @package logic
 * @name activity.logic.php
 * @version 1.0
 */
class ActivityLogic {
	
	function GetList($pid = -1,$filter = 1) {
		$sql_limit_city = '1';
		if ($pid > 0) {
			$sql_limit_city = ' ( productid = ' . $pid . ' ) ';
		}
		$sql = 'SELECT *
		FROM
			' . table ( 'activity_category' ) . '
		WHERE
			' . $sql_limit_city . '
		AND '. $filter .'
		ORDER BY
			addtime DESC';
		$sql = page_moyo ( $sql );
		return dbc ( DBCMax )->query ( $sql )->done ();
	}
	public function GetOne($cid = null) {
		$sql_filter = '1';
		is_null ( $cid ) || $sql_filter .= ' AND id=' . ( int ) $cid;
		$sql = 'SELECT *
		FROM
			' . table ( 'activity_category' ) . '
		WHERE
			'. $sql_filter .'
		ORDER BY
			addtime DESC LIMIT 1';
		return dbc ()->Query ( $sql )->GetRow ();
	}
	public function Add($rebate = array()) {
		extract ( $rebate );
		$data = array (
				'raisename' => $raisename,
				'productid' => $productid,
				'voucherid' => $voucherid,
				'number' => $number,
				'status' => $status,
				'pic'=>$pic,
				'begintime' => $begintime,
				'overtime' => $overtime,
				'rule' => $rule,
				'addtime'=>time()
		);
		dbc ()->SetTable ( table ( 'everybody_raise' ) );
		return dbc ()->Insert ( $data );
	}
	
	public function Update_Project($id, $data)
	{
		dbc()->SetTable(table('activity_project'));
		$id = dbc()->Update($data, 'id = '.$id);
		return $id;
	}
	
	//以下为活动项目操作======================================
	public function AddProject($rebate = array()) {
		extract ( $rebate );
		$data = array (
				'title' => $title,
				'categoryid' => $categoryid,
				'config' => $config,
				'description' => $description,
				'begintime' => $begintime,
				'overtime' => $overtime,
				'day_times' => $day_times,
				'lottery_num' => $lottery_num,
				'times' => $times,
				'pic'=>$pic,
				'status' => $status,
				'addtime'=>time()
		);
		dbc ()->SetTable ( table ( 'activity_project' ) );
		return dbc ()->Insert ( $data );
	}
	public function GetOneProject($aid = null) {
		$sql_filter = '1';
		is_null ( $aid ) || $sql_filter .= ' AND id=' . ( int ) $aid;
		$sql = 'SELECT *
		FROM
			' . table ( 'activity_project' ) . '
		WHERE
			'. $sql_filter .'
		ORDER BY
			addtime DESC LIMIT 1';
		return dbc ()->Query ( $sql )->GetRow ();
	}
	function GetActivityList($aid = -1,$cid = -1,$filter = 1) {
		$sql_limit_city = '1';
		if ($cid > 0) {
			$sql_limit_city = ' ( categoryid = ' . $cid . ' ) ';
		}
		if ($aid > 0) {
			$sql_limit_city = ' ( id = ' . $aid . ' ) ';
		}
		$sql = 'SELECT *
		FROM
			' . table ( 'activity_project' ) . '
		WHERE
			' . $sql_limit_city . '
		AND '. $filter .'
		ORDER BY
			addtime DESC';
		$sql = page_moyo ( $sql );
		return dbc ( DBCMax )->query ( $sql )->done ();
	}
	function GetProduct($rid = -1,$filter = 1) {
		$sql_limit_city = '1';
		if ($rid > 0) {
			$sql_limit_city = ' ( raiseid = ' . $rid . ' ) ';
		}
		$sql = 'SELECT e.*,p.name,p.flag,p.nowprice
		FROM
			' . table ( 'everybody_product' ) . ' e
		LEFT JOIN
			' . table ( 'product' ) . ' p
		ON
			(e.productid = p.id)
		WHERE
			'. $sql_limit_city .'
		AND '. $filter .'
		ORDER BY
			e.prize';
		$sql = page_moyo ( $sql );
		return dbc ( DBCMax )->query ( $sql )->done ();
	}
	
	//自定义活动页面============================================
	function GetListStatic($id = -1,$filter = 1) {
		$sql_limit_city = '1';
		if ($id > 0) {
			$sql_limit_city = ' ( id = ' . $id . ' ) ';
		}
		$sql = 'SELECT *
		FROM
			' . table ( 'static_web' ) . '
		WHERE
			' . $sql_limit_city . '
		AND '. $filter .'
		ORDER BY
			addtime DESC';
		$sql = page_moyo ( $sql );
		return dbc ( DBCMax )->query ( $sql )->done ();
	}
	public function GetOneStatic($id = null) {
		$sql_filter = '1';
		is_null ( $id ) || $sql_filter .= ' AND id=' . ( int ) $id;
		$sql = 'SELECT *
		FROM
			' . table ( 'static_web' ) . '
		WHERE
			'. $sql_filter .'
		ORDER BY
			addtime DESC LIMIT 1';
		return dbc ()->Query ( $sql )->GetRow ();
	}
	public function AddStatic($rebate = array()) {
		extract ( $rebate );
		$data = array (
				'title' => $title,
				'left_btn' => $left_btn,
				'right_btn' => $right_btn,
				'bottom_btn' => $bottom_btn,
				'bottom_url' => $bottom_url,
				'description' => $description,
				'config'=>$config,
				'pic'=>$pic,
				'status' => $status,
				'addtime'=>time()
		);
		dbc ()->SetTable ( table ( 'static_web' ) );
		return dbc ()->Insert ( $data );
	}
	public function Update_Static($id, $data)
	{
		dbc()->SetTable(table('static_web'));
		$id = dbc()->Update($data, 'id = '.$id);
		return $id;
	}
}
?>