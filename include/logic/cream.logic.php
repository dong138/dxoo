<?php

/**
 * 逻辑区：本周精选
 * @package logic
 * @name cream.logic.php
 * @version 1.0
 */
class creamLogic {
	private function ProCountSync() {
		$lastUpdate = fcache ( $this->cacheKEY, dfTimer ( 'com.catalog.procount.sync' ) );
		if (! $lastUpdate) {
			$topClasses = $this->GetList ();
			foreach ( $topClasses as $i => $topClass ) {
				$subClasses = $this->GetList ( $topClass ['id'] );
				if (! $subClasses)
					continue;
				foreach ( $subClasses as $ii => $subClass ) {
					$r = dbc ( DBCMax )->select ( 'product' )->in ( 'COUNT(1) AS procount' )->where ( 'category=' . $subClass ['id'] )->limit ( 1 )->done ();
					dbc ( DBCMax )->update ( 'catalog' )->data ( 'procount=' . $r ['procount'] )->where ( 'id=' . $subClass ['id'] )->done ();
					$r = dbc ( DBCMax )->select ( 'product' )->in ( 'COUNT(1) AS oslcount' )->where ( 'category=' . $subClass ['id'] . ' AND (status=' . PRO_STA_Normal . ' OR status=' . PRO_STA_Success . ')' )->limit ( 1 )->done ();
					dbc ( DBCMax )->update ( 'catalog' )->data ( 'oslcount=' . $r ['oslcount'] )->where ( 'id=' . $subClass ['id'] )->done ();
				}
			}
			fcache ( $this->cacheKEY, time () );
		}
	}
	public function Enabled() {
		return ini ( 'cream.enabled' );
	}
	public function product_list($cid,$key){
		if($cid>0){
			$strWhere=' and (a.display = ' . PRO_DSP_Global . ' OR (a.display = ' . PRO_DSP_City . ' AND a.city = ' . $cid . '))';
		}else{
			$strWhere=' and (a.display = ' . PRO_DSP_Global . ' OR a.display = ' . PRO_DSP_City .')';
		}
		$now = time ();
		$get_cream_sql = 'select a.* ,b.sellername from ystttuangou_product a left join ystttuangou_seller b on a.sellerid = b.id where a.begintime < ' . $now . ' AND a.overtime > ' . $now . ' AND a.saveHandler = "normal" and (a.name like "%'.$key.'%" or a.id like "%'.$key.'%") ';
		if($strWhere){
			$get_cream_sql.=$strWhere;
		}
		return dbc ( DBCMax )->query ( $get_cream_sql )->done ();
	}
	public function cream_list($cid){
		$get_cream_sql = 'select a.order as creamorder,a.id as creamid,b.*,c.sellername from ystttuangou_cream a,ystttuangou_product b,ystttuangou_seller c where a.productid =b.id and b.sellerid = c.id and a.cityid = '.$cid.' order by a.`order` desc';
		return dbc ( DBCMax )->query ( $get_cream_sql )->done ();
	}
	public function place_navigate($limit = 1) {
		if ($this->Enabled ()) {
			$cityID = logic ( 'misc' )->City ( 'id' );
			$get_cream_sql = 'select g.cityid , g.upstime as creamupstime, c.* from ' . table ( 'cream' ) . ' g Inner Join ystttuangou_catalog c ON g.productid = c.id where cityid='.$cityID.' order by creamupstime desc limit '.$limit.'';
			$creams = dbc ( DBCMax )->query ( $get_cream_sql )->done ();
			include handler ( 'template' )->file ( 'cream_place_navigate' );
		}

	}
	public function Navigate($meituannav = 0) {
		$mnavall = ($meituannav == 1) ? 2 : ($meituannav == 2 ? 0 : ($meituannav > 2 ? $meituannav : 1));
		$class = $this->GetList ( 0, $mnavall );
		if (! $class)
			return array ();
		$icon_data = ini ( 'cream.icon' );
		list ( $topcss, $subcss ) = $this->Get_nav_css ( $_GET ['code'] );
		foreach ( $class as $i => $topclass ) {
			$class [$i] ['icon'] = $icon_data [$topclass ['id']] ['icon'];
			$class [$i] ['script'] = stripcslashes ( $icon_data [$topclass ['id']] ['script'] );
			if ($topclass ['id'] > 0) {
				$subclass = $this->GetList ( $topclass ['id'], $mnavall );
				if ($subclass) {
					foreach ( $subclass as $n => $value ) {
						if ($value ['flag']) {
							$subclass [$n] ['url'] = logic ( 'url' )->create ( 'catalog', array (
									'code' => $value ['flag'] 
							) );
						} else {
							$subclass [$n] ['url'] = logic ( 'url' )->create ( 'catalog', array (
									'code' => $topclass ['flag'] 
							) );
						}
						$subclass [$n] ['selected'] = $value ['flag'] == $subcss ? true : false;
					}
				}
				$class [$i] ['subclass'] = $subclass;
			}
			$class [$i] ['selected'] = $topclass ['flag'] == $topcss ? true : false;
			$class [$i] ['url'] = logic ( 'url' )->create ( 'catalog', array (
					'code' => $topclass ['flag'] 
			) );
		}
		$this->ProCountSync ();
		return $class;
	}
	
	private function front_formatted($list, $top_code = false, &$topsd = false) {
		if (is_array ( $list )) {
			array_unshift ( $list, array (
					'id' => 0,
					'name' => '全部',
					'flag' => '' 
			) );
			foreach ( $list as $i => $one ) {
				$code = $top_code ? ($top_code . (($one ['flag'] ? '_' : '') . $one ['flag'])) : $one ['flag'];
				if ($_GET ['code'] == $code) {
					$list [$i] ['selected'] = true;
					$topsd = true;
				}
				$list [$i] ['url'] = logic ( 'url' )->create ( 'catalog', array (
						'code' => $code 
				) );
			}
		}
		return $list;
	}
	public function Exists($cityid,$productid) {
		$result = $this->GetOne ( $cityid,$productid );
		return $result ? true : false;
	}
	public function GetOne( $cityid,$productid ) {
		return dbc ( DBCMax )->select ( 'cream' )->where ( 'cityid='.$cityid . ' and productid=' . $productid )->limit ( 1 )->done ();
	}

	public function GetList($parent = 0, $sortstr = 0) {
		$catalogOBJ = dbc ( DBCMax )->select ( 'catalog' )->where ( 'parent=' . $parent )->order ( '`order`.asc' );
		if ($sortstr == 2 && $parent == 0) {
			$return = $catalogOBJ->limit ( 8 )->done ();
		} elseif ($sortstr > 2 && $parent > 0) {
			$return = $catalogOBJ->limit ( $sortstr )->done ();
		} else {
			$return = $catalogOBJ->done ();
		}
		if ($sortstr == 1 && $return) {
			$catalog = array (
					'all' => array (
							'name' => '全部',
							'flag' => NULL 
					) 
			);
			foreach ( $return as $val ) {
				$catalog [$val ['flag']] = $val;
			}
			return $catalog;
		} else {
			return $return;
		}
	}
	public function Search($where, $limit = 1) {
		$dbo = dbc ( DBCMax )->select ( 'cream' )->where ( $where );
		$limit && $dbo->limit ( $limit );
		return $dbo->done ();
	}
	
	public function Add( $cityid,$productid ) {
		return dbc ( DBCMax )->insert ( 'cream' )->data ( array (
				'cityid' => $cityid,
				'productid' => $productid,
				'upstime' => time () 
		) )->done ();
	}
	private function Delete_where($where) {
		return dbc ( DBCMax )->delete ( 'cream' )->where ( $where )->done ();
	}
	public function Delete( $cityid,$productid ) {
		$cream = $this->Search ( 'cityid='.$cityid . ' and productid=' . $productid );
		if (! $cream)
			return false;
		$this->Delete_where ( 'cityid='.$cityid . ' and productid=' . $productid );
		return true;
	}
}

?>
