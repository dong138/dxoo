<?php

/**
 * 逻辑区：商家相关
 * @package logic
 * @name seller.logic.php
 * @version 1.0
 */
class SellerLogic {
	public $price_filtes = array (
			0 => array (
					'name' => '全部',
					'sql' => ' 1 ' 
			),
			1 => array (
					'name' => '20元以下',
					'sql' => ' s.price_avg <= 20 ' 
			),
			2 => array (
					'name' => '20-50 元',
					'sql' => ' s.price_avg >= 20 AND s.price_avg <= 50 ' 
			),
			3 => array (
					'name' => '50-80 元',
					'sql' => ' s.price_avg >= 50 AND s.price_avg <= 80 ' 
			),
			4 => array (
					'name' => '80-120 元',
					'sql' => ' s.price_avg >= 80 AND s.price_avg <= 120 ' 
			),
			5 => array (
					'name' => '120-200 元',
					'sql' => ' s.price_avg >= 120 AND s.price_avg <= 200 ' 
			),
			6 => array (
					'name' => '200-500 元',
					'sql' => ' s.price_avg >= 200 AND s.price_avg <= 500 ' 
			),
			7 => array (
					'name' => '500元以上',
					'sql' => ' s.price_avg >= 500 ' 
			) 
	);
	public function price_navigate() {
		$price = get ( 'price', 'int' );
		$price_navs = array ();
		foreach ( $this->price_filtes as $k => $r ) {
			$r ['title'] = ($r ['title'] ? $r ['title'] : $r ['name']);
			$r ['url'] = logic ( 'url' )->create ( 'seller', array (
					'price' => $k 
			) );
			$r ['selected'] = ($price == $k ? true : false);
			$price_navs [$k] = $r;
		}
		include handler ( 'template' )->file ( '@html/seller/price_navigate' );
	}
	public function price_sql_filter() {
		$price = get ( 'price', 'int' );
		$price = isset ( $this->price_filtes [$price] ) ? $price : 0;
		return $this->price_filtes [$price] ['sql'];
	}
	function GetList($cid = -1, $category = '1') {
		$sql_limit_city = '1';
		if ($cid > 0) {
			$sql_limit_city = ' ( s.area = ' . $cid . ' ) ';
		}
		$sql = 'SELECT s.*
		FROM
			' . table ( 'seller' ) . ' s
		WHERE
			' . $sql_limit_city . '
		AND
			' . $category . '
		AND
			' . logic ( 'city' )->seller_sql_filter () . '
		AND
			' . logic ( 'isearcher' )->seller_sql_filter () . '
		AND
			' . $this->price_sql_filter () . '
		AND 
			`enabled` = "true"
		ORDER BY
			' . logic ( 'sort' )->seller_sql_filter ();
		$sql = page_moyo ( $sql );
		return dbc ( DBCMax )->query ( $sql )->done ();
	}
	public function GetOne($sid = null, $uid = null, $city = null) {
		$sql_filter = '1';
		is_null ( $sid ) || $sql_filter .= ' AND id=' . ( int ) $sid;
		is_null ( $uid ) || $sql_filter .= ' AND userid=' . ( int ) $uid;
		is_null ( $city ) || $sql_filter .= ' AND area=' . ( int ) $city;
		$sql = 'SELECT * FROM ' . table ( 'seller' ) . ' WHERE ' . $sql_filter . ' LIMIT 1';
		return dbc ()->Query ( $sql )->GetRow ();
	}
	public function Add($city, $userid, $sellername = '合作商家', $sellerphone = '', $selleraddress = '', $sellerurl = '', $map = '', $rebate = array(), $agentid = 0) {
		extract ( $rebate );
		$userid = ( int ) $userid;
		$data = array (
				'userid' => intval ( $userid ),
				'sellername' => strip_tags ( $sellername ),
				'sellerphone' => strip_tags ( $sellerphone ),
				'selleraddress' => strip_tags ( $selleraddress ),
				'sellerurl' => $sellerurl,
				'sellermap' => $map,
				'area' => $city,
				'time' => time (),
				'profit_id' => $profit_id,
				'profit_pre' => $profit_pre,
				'sell_pre' => $sell_pre,
				'home_uid' => $home_uid,
				'enabled' => $enabled,
				'city_place_region' => ( int ) $city_place_region,
				'city_place_street' => ( int ) $city_place_street,
				'imgs' => trim ( $imgs, ',' ),
				'price_avg' => $price_avg,
				'category' => $category,
				'trade_time' => strip_tags ( $trade_time ),
				'content' => $content,
				'agentid' => $agentid,
				'is_agent' => $is_agent,
				'firstpinyin'=> $firstpinyin,
				'allpinyin'=> $allpinyin
		);
		if ($userid != 1) {
			dbc ( DBCMax )->update ( 'members' )->data ( array (
					'role_id' => '6',
					'role_type' => 'seller',
					'privs' => '' 
			) )->where ( 'uid=' . ( int ) $userid )->done ();
		}
		dbc ()->SetTable ( table ( 'seller' ) );
		return dbc ()->Insert ( $data );
	}
	public function Join($in) {
		$fields = array (
				'userid',
				'area',
				'sellername',
				'selleraddress',
				'sellerphone',
				'sellerurl',
				'sellermap',
				'profit_id',
				'id_card',
				'zhizhao' 
		);
		foreach ( $fields as $f ) {
			$data [$f] = strip_tags ( $in [$f] );
		}
		$rebate = logic ( 'rebate' )->Get_Rebate_setting ();
		$profit_id = $data ['profit_id'];
		$profit_id = $profit_id ? $profit_id : 0;
		if ($rebate ['profit'] [$profit_id] && $rebate ['profit'] [$profit_id] ['pre']) {
			$data ['profit_pre'] = $rebate ['profit'] [$profit_id] ['pre'];
		}
		$data ['time'] = time ();
		$data ['enabled'] = 'false';
		return dbc ( DBCMax )->insert ( 'seller' )->data ( $data )->done ();
	}
	public function Register($username, $password) {
		$userExists = account ()->Exists ( 'username', $username );
		if ($userExists) {
			$user = account ()->Search ( 'username', $username, 1 );
			if ($user ['role_type'] == 'admin' && $user ['uid'] != 1) {
				return array (
						'error' => true,
						'result' => '此用户是管理员，请换一个！' 
				);
			}
			$seller = logic ( 'seller' )->GetOne ( null, $user ['id'] );
			if ($seller) {
				return array (
						'error' => true,
						'result' => '此用户已经是商家，请换一个！' 
				);
			}
			return array (
					'error' => true,
					'result' => '该用户名已被普通用户注册！' 
			);
		} else {
			if ($password == '') {
				return array (
						'error' => true,
						'result' => '密码不能为空！' 
				);
			}
			
			$mail = logic ( 'misc' )->rndString () . '@apiz.org';
			$rr = account ()->Register ( $username, $password, $mail );
			if ($rr ['error']) {
				return array (
						'error' => true,
						'result' => $rr ['result'] 
				);
			}
			$uid = $rr ['result'];
			account ()->Validated ( $uid );
			user ( $uid )->set ( 'role_type', 'seller' );
		}
		return array (
				'error' => false,
				'result' => $uid 
		);
	}
	public function U2SID($uid = null) {
		is_null ( $uid ) && $uid = user ()->get ( 'id' );
		if ($uid < 0)
			return $uid;
		$seller = $this->GetOne ( null, $uid, null );
		return $seller ? $seller ['id'] : - 1;
	}
	public function setmembertype($sid = 0, $status = 'true') {
		$sellerinfo = dbc ( DBCMax )->query ( 'select userid from ' . table ( 'seller' ) . ' where id=' . $sid )->limit ( 1 )->done ();
		if ($sellerinfo ['userid'] > 1) {
			if ($status == 'true') {
				dbc ( DBCMax )->update ( 'members' )->data ( array (
						'role_type' => 'seller',
						'role_id' => '6' 
				) )->where ( 'uid=' . ( int ) $sellerinfo ['userid'] )->done ();
			} else {
				dbc ( DBCMax )->update ( 'members' )->data ( array (
						'role_type' => 'normal',
						'role_id' => '0' 
				) )->where ( 'uid=' . ( int ) $sellerinfo ['userid'] )->done ();
			}
		}
	}
	public function AVParser(&$product) {
		if (! $product)
			return false;
		if (is_array ( $product [0] )) {
			$returns = array ();
			foreach ( $product as $i => &$one ) {
				$this->AVParser ( $one );
			}
			return;
		}
		$pid = $product ['id'];
		if ($product ['type'] == 'ticket') {
			$product ['views'] ['tikCount'] = array (
					'TICK_STA_Used' => logic ( 'coupon' )->Count ( null, $pid, null, TICK_STA_Used ),
					'TICK_STA_Unused' => logic ( 'coupon' )->Count ( null, $pid, null, TICK_STA_Unused ),
					'TICK_STA_Overdue' => logic ( 'coupon' )->Count ( null, $pid, null, TICK_STA_Overdue ),
					'TICK_STA_Invalid' => logic ( 'coupon' )->Count ( null, $pid, null, TICK_STA_Invalid ) 
			);
		} elseif ($product ['type'] == 'stuff') {
			$product ['views'] ['delivery'] = array (
					'sended' => logic ( 'delivery' )->Count ( $pid, DELIV_SEND_Yes ),
					'waiting' => logic ( 'delivery' )->Count ( $pid, DELIV_SEND_No ),
					'finished' => logic ( 'delivery' )->Count ( $pid, DELIV_SEND_OK ) 
			);
		}
		$fbase = 'productid=' . $pid . ' AND pay=' . ORD_PAID_Yes . ' AND status=' . ORD_STA_Normal;
		$product ['views'] ['money'] = array (
				'all' => logic ( 'order' )->Summary ( $fbase ),
				'real' => 0 
		);
		if ($product ['type'] == 'ticket') {
			$product ['views'] ['money'] ['real'] = logic ( 'coupon' )->Summary ( $pid );
		} elseif ($product ['type'] == 'stuff') {
			$product ['views'] ['money'] ['real'] = logic ( 'order' )->Summary ( $fbase . ' AND process IN("WAIT_BUYER_CONFIRM_GOODS","TRADE_FINISH")' );
		}
	}
	public function money_add($sid, $money) {
		dbc ( DBCMax )->update ( 'seller' )->data ( 'money = money + ' . $money )->where ( 'id=' . $sid )->done ();
	}
	public function money_less($sid, $money) {
		dbc ( DBCMax )->update ( 'seller' )->data ( 'money = money - ' . $money )->where ( 'id=' . $sid )->done ();
	}
	public function order_success($sid) {
		dbc ( DBCMax )->update ( 'seller' )->data ( 'successnum = successnum + 1' )->where ( 'id=' . $sid )->done ();
	}
	public function order_failed($sid) {
		dbc ( DBCMax )->update ( 'seller' )->data ( 'successnum = successnum - 1' )->where ( 'id=' . $sid )->done ();
	}
	public function product_add($sid) {
		dbc ( DBCMax )->update ( 'seller' )->data ( 'productnum = productnum + 1' )->where ( 'id=' . $sid )->done ();
	}
	public function product_del($sid) {
		dbc ( DBCMax )->update ( 'seller' )->data ( 'productnum = productnum - 1' )->where ( 'id=' . $sid )->done ();
	}
	public function maplocation4google2sogou($w, $sellerid) {
		$seller = $this->GetOne ( $sellerid );
		$map = $seller ['sellermap'];
		list ( $x, $y, $z ) = explode ( ',', $map );
		if ($x && $y && $z) {
			$r = dfopen ( 'http:/' . '/api.go2map.com/engine/api/translate/xml?points=' . $y . ',' . $x . '&type=2' );
			if (stristr ( $r, '<status>error</status>' ) || ! stristr ( $r, '<status>ok</status>' )) {
				return 'false';
			}
			preg_match_all ( '/<x>(.*?)<\/x>/i', $r, $m );
			$x = $m [1] [0];
			preg_match_all ( '/<y>(.*?)<\/y>/i', $r, $m );
			$y = $m [1] [0];
			$map_new = $x . ',' . $y . ',' . $z;
			dbc ( DBCMax )->update ( 'seller' )->data ( 'sellermap="' . $map_new . '"' )->where ( 'id=' . ( int ) $sellerid )->done ();
			return 'ok';
		} else {
			return 'false';
		}
	}
	
	/**
	 * **判断是否存在用户名****
	 */
	function IsMember($username) {
		$member = dbc ( DBCMax )->select ( 'members' )->where ( "username = '" . $username . "'" )->done ();
		if ($member)
			return 'yes';
		else
			return 'no';
	}
	
	/**
	 * 获取代理商名称
	 *
	 * @param unknown $id        	
	 */
	function getAgenter($id) {
		if ($id == null || $id == "" || $id == 0) {
			echo '无代理商';
		} else {
			$sql = 'SELECT * FROM ' . table ( 'seller' ) . ' where id = ' . $id . ' LIMIT 1';
			$ag = dbc ( DBCMax )->select ( 'seller' )->where ( "id = ".$id )->done ();
			if($ag && count($ag) > 0){
				echo $ag[0]['sellername'];
			}else {
				echo '无代理商';
			}
		}
	}
	
	/**
	 * 获取代理商名称
	 *
	 * @param unknown $id        	
	 */
	function searchAgent($id, $name) {
		$where = " where 1 ";
		if ($id != null && $id != "") {
			$where .= " and id = " . $id;
		}
		if ($name != null && $name != "") {
			$where .= " and name = '" . $name . "'";
		}
		
		$sql = 'SELECT * FROM ' . table ( 'agenter' ) . $where;
		return dbc ( DBCMax )->Query ( $sql )->GetRow ();
	}
	
	/**
	 * ******************搜索商家结算统计************************
	 */
	function searchseller($begintime, $endtime, $keyword, $order_state) {
		$andsql = '';
		if (strlen ( $keyword ) > 0) {
			$andsql .= " and a.sellername like '%" . $keyword . "%' ";
		}
		if (strlen ( $begintime ) > 0 && strlen ( $endtime ) > 0 && $endtime >= $begintime) {
			$andsql .= " and b.buytime between " . strtotime ( $begintime ) . " and " . (strtotime ( $endtime ) + 86399);
		}
		if (strlen ( $order_state ) > 0) {
			$andsql .= " and b.process='" . $order_state . "'";
		}
		
		$sql = "select a.id,a.sellerphone,max(b.buytime) buytime,a.sellername,count(b.productid) totalcounts,sum(b.totalprice) totalprices from " . table ( seller ) . " a," . table ( order ) . " b," . table ( product ) . " c where b.productid=c.id and c.sellerid=a.id " . $andsql . " group by a.id";
		return $sql;
	}
	function searchsellerCount($order_state) {
		$andsql = '';
		if (strlen ( $keyword ) > 0) {
			$andsql .= " and a.sellername like '%" . $keyword . "%' ";
		}
		if (strlen ( $begintime ) > 0 && strlen ( $endtime ) > 0 && $endtime >= $begintime) {
			$andsql .= " and b.buytime between " . strtotime ( $begintime ) . " and " . (strtotime ( $endtime ) + 86399);
		}
		if (strlen ( $order_state ) > 0) {
			$andsql .= " and b.process='" . $order_state . "'";
		}
		$sql = 'select count(*) from (';
		$sql .= "select a.sellername from " . table ( seller ) . " a," . table ( order ) . " b," . table ( product ) . " c where b.productid=c.id and c.sellerid=a.id" . $andsql . " group by a.id";
		$sql .= ') as t';
		return $sql;
	}
}
?>