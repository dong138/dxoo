<?php

/**
 * 逻辑区：众筹相关
 * @package logic
 * @name everybody.logic.php
 * @version 1.0
 */
class EverybodyLogic {
	
	function GetList($pid = -1,$filter = 1) {
		$sql_limit_city = '1';
		if ($pid > 0) {
			$sql_limit_city = ' ( productid = ' . $pid . ' ) ';
		}
		$sql = 'SELECT *
		FROM
			' . table ( 'everybody_raise' ) . '
		WHERE
			' . $sql_limit_city . '
		AND '. $filter .'
		ORDER BY
			addtime DESC';
		$sql = page_moyo ( $sql );
		return dbc ( DBCMax )->query ( $sql )->done ();
	}
	public function GetOne($eid = null, $pid = null) {
		$sql_filter = '1';
		is_null ( $eid ) || $sql_filter .= ' AND e.id=' . ( int ) $eid;
		is_null ( $pid ) || $sql_filter .= ' AND e.productid=' . ( int ) $pid;
		$sql = 'SELECT e.*,p.img
		FROM
			' . table ( 'everybody_raise' ) . ' e
		LEFT JOIN
			' . table ( 'product' ) . ' p
		ON
			(e.productid = p.id)
		WHERE
			'. $sql_filter .'
		AND e.status=1
		ORDER BY
			e.addtime DESC LIMIT 1';
		return dbc ()->Query ( $sql )->GetRow ();
	}
	function RankCheck($productid,$userid){
		$productid = ( int ) $productid;
		$userid = ( int ) $userid;
		if (! $productid)
			return array (
				'status'=> 1,
				'msg' => __ ( '无产品' )
			);
		$sql = 'SELECT *
		FROM
			' . table ( 'order' ) . '
		WHERE  productid = ' . $productid .' and userid=' . $userid .' and is_raise =' . ORD_STA_Normal;
		$order = dbc ( DBCMax )->query ( $sql )->limit ( 1 )->done ();
		if ($order){
			return array (
				'status'=> 1,
				'msg' => __ ( '已发送' )
			);
		}
		return array (
			'status'=> 2,
			'msg' => __ ( '发送' )
		);
	}
	public function RankList($eid ,$page,$number=0){
		$sql_filter = '1';
		is_null ( $eid ) || $sql_filter .= ' AND l.everybody_raise_id=' . ( int ) $eid;
		$sql = 'SELECT p.name,p.flag, l.userid,l.openid,count(l.num) as num, l.everybody_raise_id, m.username, m.phone,e.productid,e.voucherid
		FROM
			' . table ( 'likes' ) . ' l
		LEFT JOIN
			' . table ( 'members' ) . ' m
		ON
			(l.userid = m.uid)
		LEFT JOIN
			' . table ( 'everybody_raise' ) . ' e
		ON
			(l.everybody_raise_id = e.id)
		LEFT JOIN
			' . table ( 'product' ) . ' p
		ON
			(e.productid = p.id)
		WHERE
			'. $sql_filter .'
		GROUP BY l.userid,l.openid
		ORDER BY
			num DESC LIMIT '.$page.',' .$number;
		return dbc ( DBCMax )->query ( $sql )->done ();
	}
	public function GetProductTitle($id){
		$sql_filter = '1';
		is_null ( $id ) || $sql_filter .= ' AND id=' . ( int ) $id;
		$sql = 'SELECT flag,name
		FROM
			' . table ( 'product' ) . ' 
		WHERE
			'. $sql_filter .'
		LIMIT 1';
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
	
	public function Update_Raise($id, $data)
	{
		dbc()->SetTable(table('everybody_raise'));
		$id = dbc()->Update($data, 'id = '.$id);
		return $id;
	}
	
	//以下为众筹产品操作======================================
	public function AddProduct($rebate = array()) {
		extract ( $rebate );
		$data = array (
				'productid' => $productid,
				'raiseid' => $raiseid,
				'number' => $number,
				'prize' => $prize,
				'pic'=>$pic,
				'addtime'=>time()
		);
		dbc ()->SetTable ( table ( 'everybody_product' ) );
		return dbc ()->Insert ( $data );
	}
	
	function GetProductList($rid = -1,$filter = 1) {
		$sql_limit_city = '1';
		if ($rid > 0) {
			$sql_limit_city = ' ( raiseid = ' . $rid . ' ) ';
		}
		$sql = 'SELECT *
		FROM
			' . table ( 'everybody_product' ) . '
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
}
?>