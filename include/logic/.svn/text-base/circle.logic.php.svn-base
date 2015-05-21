<?php

/**
 * 逻辑区：热门团购
 * @package logic
 * @name circle.logic.php
 * @version 1.0
 */
class circleLogic {
	public function Enabled() {
		return ini ( 'circle.enabled' );
	}
	public function place_navigate($limit = 1) {
		if ($this->Enabled ()) {
			$cityID = logic ( 'misc' )->City ( 'id' );
			$get_circle_sql = 'select c.upstime, t.cityid , p.* from ' . table ( 'circle' ) . ' c 
					Inner Join ystttuangou_city t ON (c.cityid = t.cityid) 
					Inner Join ystttuangou_city_place p ON (c.placeid=p.id) 
					where c.cityid='.$cityID.' order by upstime desc limit '.$limit.'';
			$circles = dbc ( DBCMax )->query ( $get_circle_sql )->done ();
			include handler ( 'template' )->file ( 'circle_place_navigate' );
		}

	}
	public function Exists($cityid,$placeid) {
		$result = $this->GetOne ( $cityid,$placeid );
		return $result ? true : false;
	}
	public function GetOne($cityid,$placeid) {
		return dbc ( DBCMax )->select ( 'circle' )->where ( 'cityid=' . $cityid .' and placeid='. $placeid )->limit ( 1 )->done ();
	}
	public function Search($where, $limit = 1) {
		$dbo = dbc ( DBCMax )->select ( 'circle' )->where ( $where );
		$limit && $dbo->limit ( $limit );
		return $dbo->done ();
	}
	public function Add($cityid,$placeid) {
		return dbc ( DBCMax )->insert ( 'circle' )->data ( array (
				'cityid' => $cityid,
				'placeid' => $placeid,
				'upstime' => time () 
		) )->done ();
	}
	private function Delete_where($where) {
		return dbc ( DBCMax )->delete ( 'circle' )->where ( $where )->done ();
	}
	public function Delete($cityid,$placeid) {
		$circle = $this->Search ( 'cityid=' . $cityid .' and placeid='. $placeid );
		if (! $circle)
			return false;
		$this->Delete_where ( 'cityid=' . $cityid .' and placeid='. $placeid );
		return true;
	}
}

?>
