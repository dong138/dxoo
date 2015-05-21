<?php

/**
 * 逻辑区：产品相关
 * @package logic
 * @name product.logic.php
 * @version 1.1
 */
class ProductLogic {
	public function display($category = '1') {
		if (count ( $_GET ) == 1 && $_GET ['mod'] == 'index') {
			define ( 'INDEX_DEFAULT', TRUE );
		} else {
			define ( 'INDEX_DEFAULT', false );
		}
		$productID = get ( 'view', 'int' );
		if (false == $productID) {
			$mutiView = true;
			if (ini ( 'ui.igos.pager' )) {
			} else {
				$_GET [EXPORT_GENEALL_FLAG] = EXPORT_GENEALL_VALUE;
			}
			$product = logic ( 'product' )->GetList ( logic ( 'misc' )->City ( 'id' ), PRO_ACV_Yes, $category );
		} else {
			$mutiView = false;
// 			$product = logic ( 'product' )->GetOne ( $productID );
			//做商家信息显示的处理
			$product = logic ( 'product' )->MyGetOne ( $productID );
		}
		$usePager = get ( 'page', 'int' ) > 0 ? true : false;
		if (! $usePager && false == $product) {
			return false;
		}
		if (! $usePager && $mutiView && count ( $product ) == 1) {
			$mutiView = false;
			$product = $product [0];
			$product ['product_link'] = $this->get_link_product ( $product ['linkid'] );
			$_GET ['view'] = $product ['id'];
		} else if (ini ( 'ui.igos.dsper' ) && $mutiView && count ( $product ) > 1) {
			logic ( 'product' )->reSort ( $product );
		}
		$file = $mutiView ? 'home' : 'xtn_detail';
		if (INDEX_DEFAULT === true && $mutiView) {
			$cache_product = fcache ( 'default.catalog.procount', 1800 );
			if ($cache_product) {
				$product = $cache_product;
			} else {
				$catalogs = logic ( 'catalog' )->Navigate ( 6 );
				if ($catalogs) {
					foreach ( $catalogs as $key => $val ) {
						$val ['navcate'] = logic ( 'catalog' )->Filter ( $val ['flag'], 'product' );
						$catalogs [$key] ['product'] = logic ( 'product' )->GetList ( logic ( 'misc' )->City ( 'id' ), PRO_ACV_Yes, $val ['navcate'] );
						if (! $catalogs [$key] ['product']) {
							unset ( $catalogs [$key] );
						}
					}
					$product = $catalogs;
				}
				fcache ( 'default.catalog.procount', $product );
			}
		}
		return array (
				'mutiView' => $mutiView,
				'file' => $file,
				'product' => $product 
		);
	}
	function GetNewList($limit = 2) {
		$cid = logic ( 'misc' )->City ( 'id' );
		$now = time ();
		$where = '(display = ' . PRO_DSP_Global . ' OR (display = ' . PRO_DSP_City . ' AND city = ' . $cid . ')) and (is_special is null or is_special = 0) AND begintime < ' . $now . ' AND overtime > ' . $now . ' AND saveHandler = "normal"';
		$sql = 'SELECT id,name,nowprice,img,linkid FROM ' . table ( 'product' ) . ' WHERE ' . $where . ' ORDER BY id DESC LIMIT ' . $limit;
		$product = dbc ( DBCMax )->query ( $sql )->done ();
		if ($product) {
			foreach ( $product as $key => $val ) {
				$imgs = $val ['img'] != '' ? explode ( ',', $val ['img'] ) : null;
				$product [$key] ['img'] = ($imgs && $imgs [0]) ? $imgs [0] : 0;
			}
		}
		return $product;
	}
	
	/**
	 * 本周精选
	 */
	function GetCream($limit = 2){
		//获取城市
		$cid = logic ( 'misc' )->City ( 'id' );
		$where = "";
		if($cid){
			//$where .= ' and p.city = '.$cid;
			$where.= ' and (p.display = ' . PRO_DSP_Global . ' OR (p.display = ' . PRO_DSP_City . ' AND p.city = ' . $cid . ') )';
		}
		//默认城市丽江
		$now = time ();
		$sql = 'select p.* from ystttuangou_cream c left join ystttuangou_product p on c.productid = p.id where p.begintime < ' . $now . ' AND p.overtime > ' . $now . ' and (p.is_special is null or p.is_special = 0) AND p.saveHandler = "normal" '.$where.' order by c.`order` desc limit '.$limit;
		$product = dbc ( DBCMax )->query ( $sql )->done ();
		if(!$product || count($product) < 4){
			$sql = 'SELECT p.* FROM ' . table ( 'product' ) . ' p WHERE p.begintime < ' . $now . ' AND p.overtime > ' . $now . ' AND p.saveHandler = "normal" ' . $where . ' ORDER BY p.`order` DESC LIMIT ' . $limit;
			$product = dbc ( DBCMax )->query ( $sql )->done ();
		}
		//处理已售
		if ($product) {
			foreach ( $product as $key => $val ) {
				$imgs = $val ['img'] != '' ? explode ( ',', $val ['img'] ) : null;
				$product [$key] ['img'] = ($imgs && $imgs [0]) ? $imgs [0] : 0;
				
				$product[$key] ['succ_total'] = $product[$key] ['successnum'];
				if ($product[$key]['type'] == 'prize') {
					$product[$key]['succ_real'] = logic ( 'prize' )->sigCount ( 'pid=' . $product[$key]['id'] );
				} else {
					$product[$key]['succ_real'] = $this->BuyersCount ( $product[$key]['id'] );
				}
				$product[$key]['succ_buyers'] = $product[$key]['succ_real'] + $product[$key]['virtualnum'];
				$product[$key]['succ_remain'] = $product[$key]['succ_total'] - $product[$key]['succ_buyers'];
				if ($product[$key]['type'] == 'prize') {
					$product[$key]['sells_real'] = $product[$key]['succ_real'];
				} else {
					$product[$key]['sells_real'] = $this->SellsCount ( $product[$key]['id'] );
				}
				$product[$key]['sells_count'] = ($product[$key]['is_countdown'] ? ( int ) $product[$key]['sells_count'] : $product[$key]['sells_real']) + $product[$key]['virtualnum'];
				
			}
		}
		return $product;
	}
	/**
	 * 做商家信息显示的处理
	 * @param unknown $id
	 * @param string $cached
	 * @return boolean
	 */
	function MyGetOne($id, $cached = true){
		$ckey = 'product.mygetone.' . $id;
		$list = $cached ? cached ( $ckey ) : false;
		if ($list)
			return $list;
		//1判断显示商家还是代理商
		//商家和代理商显示(2代理商(单独商家)、1商家(禁用商家))
		$p = dbc ( DBCMax )->query ( "select show_type,child_sellerid from ". table ( 'product' ) ." where id = ".$id )->limit ( 1 )->done ();
		if($p){
			if($p['show_type'] == 2 && $p['child_sellerid']){
				//显示禁用商家
				$sql = 'SELECT p.*,s.sellername,s.sellerphone,s.selleraddress,s.sellerurl,s.sellermap,s.content as seller_content
				FROM
					' . table ( 'product' ) . ' p
				LEFT JOIN ' . table ( 'seller' ) . ' s
				ON
					(p.child_sellerid = s.id)
				WHERE
					p.id = ' . ( int ) $id;
			}else{
				//显示代理商或者是独立商家
				$sql = 'SELECT p.*,s.sellername,s.sellerphone,s.selleraddress,s.sellerurl,s.sellermap,s.content as seller_content
				FROM
					' . table ( 'product' ) . ' p
				LEFT JOIN ' . table ( 'seller' ) . ' s
				ON
					(p.sellerid = s.id)
				WHERE
					p.id = ' . ( int ) $id;
			}
			$data = $this->__parse_result ( dbc ( DBCMax )->query ( $sql )->limit ( 1 )->done () );
				
			if ($data) {
				if ($data ['begintime'] > time ()) {
					$lasttime = $data ['begintime'] - time ();
					if ($lasttime > 86400) {
						$data ['begin_date'] = date ( 'Y-m-d H:i:s', $data ['begintime'] );
					} else {
						$data ['limit_time'] = $lasttime;
					}
				}
				if ($data ['is_countdown'] == 1) {
					logic ( 'order' )->FreeCountDownOrderNew ( $id );
				}
				$data ['product_link'] = $this->get_link_product ( $data ['linkid'] );
			}
			return cached ( $ckey, $data );
		}else{
			return false;
		}
	}
	
	function GetOne($id, $cached = true) {
		$ckey = 'product.getone.' . $id;
		$list = $cached ? cached ( $ckey ) : false;
		if ($list)
			return $list;
		$sql = 'SELECT p.*,s.sellername, s.sellerphone,s.selleraddress,s.sellerurl,s.sellermap,s.content as seller_content
		FROM
			' . table ( 'product' ) . ' p
		LEFT JOIN ' . table ( 'seller' ) . ' s
		ON
			(p.sellerid = s.id)
		WHERE
			p.id = ' . ( int ) $id;
		$data = $this->__parse_result ( dbc ( DBCMax )->query ( $sql )->limit ( 1 )->done () );
		
		if ($data) {
			if ($data ['begintime'] > time ()) {
				$lasttime = $data ['begintime'] - time ();
				if ($lasttime > 86400) {
					$data ['begin_date'] = date ( 'Y-m-d H:i:s', $data ['begintime'] );
				} else {
					$data ['limit_time'] = $lasttime;
				}
			}
			if ($data ['is_countdown'] == 1) {
				logic ( 'order' )->FreeCountDownOrderNew ( $id );
			}
			$data ['product_link'] = $this->get_link_product ( $data ['linkid'] );
		}
		return cached ( $ckey, $data );
	}
	public function GetFirst() {
		$list = $this->GetList ( logic ( 'misc' )->City ( 'id' ), PRO_ACV_Yes );
		return $list [0];
	}
	public function get_list_ext_sql($sql = null) {
		static $ssql = '';
		if (is_null ( $sql )) {
			$sql = $ssql;
			$ssql = '';
			return $sql;
		} else {
			return $ssql = $sql;
		}
	}
	function GetOwnerList($sellerID = 0, $limit = null) {
		$sql_limit = '';
		if (! empty ( $limit ) && ( int ) $limit > 0) {
			$sql_limit = ' LIMIT ' . ( int ) $limit;
		}
		$sql = "SELECT `id`,`name`,`price`,`nowprice`,`img`,(`virtualnum`+`sells_count`) AS `sells_count` FROM `" . table ( 'product' ) . "` WHERE `display`>0 and (is_special is null or is_special = 0) AND `saveHandler`='normal' AND (`sellerid`='" . $sellerID . "' or `child_sellerid`='" . $sellerID . "') AND `overtime`>=" . time () . " ORDER BY `overtime` ASC " . $sql_limit;
		$data = dbc ( DBCMax )->query ( $sql )->done ();
		if ($data) {
			foreach ( $data as &$v ) {
				$img = explode ( ',', $v ['img'] );
				$v ['pic'] = imager ( $img [0], IMG_Normal );
			}
		}
		return $data;
	}
	function GetList($cid = -1, $actived = null, $extend = '1') {
		$cid = ( int ) $cid;
		$sql_limit_city = '1';
		if ($cid > 0) {
			$sql_limit_city = '(p.display = ' . PRO_DSP_Global . ' OR (p.display = ' . PRO_DSP_City . ' AND p.city = ' . $cid . ') )';
		}
		$sql_limit_actived = '1';
		$now = time ();
		if (! is_null ( $actived )) {
			if ($actived === PRO_ACV_Yes) {
				$sql_limit_actived = 'p.begintime < ' . $now . ' AND p.overtime > ' . $now;
			} else {
				$sql_limit_actived = 'p.overtime < ' . $now;
			}
		}
		$sql = 'SELECT p.*,s.sellername,s.sellerphone,s.selleraddress,s.sellerurl,s.sellermap,p.totalnum' . $this->get_list_ext_sql ();
		$sql .= ' FROM ' . table ( 'product' ) . ' p LEFT JOIN ' . table ( 'seller' ) . ' s ON (p.sellerid=s.id) WHERE ';
		$sql .= $sql_limit_actived . ' AND ' . $sql_limit_city . ' AND ' . $extend;
		$sql .= INDEX_DEFAULT === true ? '' : ' AND ' . logic ( 'city' )->place_sql_filter () . ' AND ' . logic ( 'isearcher' )->product_sql_filter ();
		$sql .= ' and (p.is_special is null or p.is_special = 0) AND p.saveHandler = "normal" ORDER BY ';
		$sql .= INDEX_DEFAULT === true ? 'p.order DESC LIMIT 6' : logic ( 'sort' )->product_sql_filter ();
		if (INDEX_DEFAULT === true) {
		} else {
			logic ( 'isearcher' )->Linker ( $sql );
			$sql = page_moyo ( $sql );
		}
		$result = dbc ( DBCMax )->query ( $sql )->done ();
		return $this->__parse_result ( $result );
	}
	function GetManagerList($cid = -1, $actived = null, $extend = '1') {
		$cid = ( int ) $cid;
		$sql_limit_city = '1';
		if ($cid > 0) {
			$sql_limit_city = '(p.display = ' . PRO_DSP_Global . ' OR (p.display = ' . PRO_DSP_City . ' AND p.city = ' . $cid . ') )';
		}
		$sql_limit_actived = '1';
		$now = time ();
		if (! is_null ( $actived )) {
			if ($actived === PRO_ACV_Yes) {
				$sql_limit_actived = 'p.begintime < ' . $now . ' AND p.overtime > ' . $now;
			} else {
				$sql_limit_actived = 'p.overtime < ' . $now;
			}
		}
		$sql = 'SELECT p.*,s.sellername,s.sellerphone,s.selleraddress,s.sellerurl,s.sellermap,p.totalnum' . $this->get_list_ext_sql ();
		$sql .= ' FROM ' . table ( 'product' ) . ' p LEFT JOIN ' . table ( 'seller' ) . ' s ON (p.sellerid=s.id) WHERE ';
		$sql .= $sql_limit_actived . ' AND ' . $sql_limit_city . ' AND ' . $extend;
		$sql .= INDEX_DEFAULT === true ? '' : ' AND ' . logic ( 'city' )->place_sql_filter () . ' AND ' . logic ( 'isearcher' )->product_sql_filter ();
		$sql .= ' AND p.saveHandler = "normal" ORDER BY ';
		$sql .= INDEX_DEFAULT === true ? 'p.updatetime DESC LIMIT 6' : logic ( 'sort' )->product_sql_filter ();
		if (INDEX_DEFAULT === true) {
		} else {
			logic ( 'isearcher' )->Linker ( $sql );
			$sql = page_moyo ( $sql );
		}
		$result = dbc ( DBCMax )->query ( $sql )->done ();
		return $this->__parse_result ( $result );
	}
	function GetOtherList($city_id, $category, $selfid, $type = 0) {
		$now = time ();
		$city_id = ( int ) $city_id;
		$catasql = $type ? ' AND category = ' . ( int ) $category : '';
		$sql_where = '(display = ' . PRO_DSP_Global . ' OR (display = ' . PRO_DSP_City . ' AND city = ' . $city_id . '))' . $catasql . ' and (is_special is null or is_special = 0) AND begintime < ' . $now . ' AND overtime > ' . $now . ' AND id != ' . $selfid . ' AND saveHandler = "normal"';
		$result = dbc ( DBCMax )->select ( 'product' )->where ( $sql_where )->order ( 'id.desc' )->limit ( 5 )->done ();
		if (count ( $result ) < 5 && $type) {
			$sql_where = '(display = ' . PRO_DSP_Global . ' OR (display = ' . PRO_DSP_City . ' AND city = ' . $city_id . ')) AND begintime < ' . $now . ' AND overtime > ' . $now . ' AND id != ' . $selfid . ' AND saveHandler = "normal"';
			$result = dbc ( DBCMax )->select ( 'product' )->where ( $sql_where )->order ( 'id.desc' )->limit ( 5 )->done ();
		}
		return $this->__parse_result ( $result );
	}
	public function SrcOne($id) {
		return dbc ( DBCMax )->select ( 'product' )->where ( 'id=' . ( int ) $id )->limit ( 1 )->done ();
	}
	public function Where($sql_limit) {
		$sql = 'SELECT * FROM ' . table ( 'product' ) . ' WHERE ' . $sql_limit;
		return dbc ( DBCMax )->query ( $sql )->done ();
	}
	public function Update($id, $array) {
		$id = ( int ) $id;
		zlog ( 'product' )->update ( $id, $array );
		dbc ()->SetTable ( table ( 'product' ) );
		if (isset ( $array ['@extra'] ))
			unset ( $array ['@extra'] );
		dbc ()->Update ( $array, 'id = ' . $id );
		fcache ( 'default.catalog.procount', 0 );
	}
	public function Update_direct($id, $array) {
		dbc ()->SetTable ( table ( 'product' ) );
		if (isset ( $array ['@extra'] ))
			unset ( $array ['@extra'] );
		dbc ()->Update ( $array, 'id = ' . ( int ) $id );
		fcache ( 'default.catalog.procount', 0 );
	}
	public function Delete($id) {
		$id = ( int ) $id;
		$p = $this->SrcOne ( $id );
		zlog ( 'product' )->delete ( $id, $p );
		$imgs = explode ( ',', $p ['img'] );
		foreach ( $imgs as $i => $iid ) {
			logic ( 'upload' )->Delete ( $iid );
		}
		dbc ( DBCMax )->delete ( 'product' )->where ( 'id=' . $id )->done ();
		$sqls = array (
				'DELETE FROM ' . table ( 'finder' ) . ' WHERE productid=' . $id,
				'DELETE FROM ' . table ( 'ticket' ) . ' WHERE productid=' . $id,
				'DELETE FROM ' . table ( 'favorite' ) . ' WHERE pid=' . $id 
		);
		$orderList = logic ( 'order' )->Where ( 'productid=' . $id );
		foreach ( $orderList as $i => $order ) {
			$oid = $order ['orderid'];
			$sqls [] = 'DELETE FROM ' . table ( 'order' ) . ' WHERE orderid=' . $oid;
			$sqls [] = 'DELETE FROM ' . table ( 'order_clog' ) . ' WHERE sign=' . $oid;
			$sqls [] = 'DELETE FROM ' . table ( 'paylog' ) . ' WHERE sign=' . $oid;
		}
		foreach ( $sqls as $i => $sql ) {
			dbc ( DBCMax )->query ( $sql )->done ();
		}
		logic ( 'seller' )->product_del ( $p ['sellerid'] );
		fcache ( 'default.catalog.procount', 0 );
		return true;
	}
	public function Publish($data) {
		logic ( 'seller' )->product_add ( $data ['sellerid'] );
		dbc ()->SetTable ( table ( 'product' ) );
		$id = dbc ()->Insert ( $data );
		zlog ( 'product' )->publish ( $id, $data );
		fcache ( 'default.catalog.procount', 0 );
		return $id;
	}
	function MoneySaves() {
		$now = time ();
		$sql = 'SELECT SUM((price-nowprice)*(virtualnum+totalnum)) AS saves
		FROM
			' . table ( 'product' ) . '
		WHERE
			overtime < ' . $now . '
		AND
			status = 2';
		$result = dbc ( DBCMax )->query ( $sql )->limit ( 1 )->done ();
		return $result ['saves'];
	}
	function SellsCount($id) {
		$sql = 'SELECT SUM(productnum) AS sums
		FROM
			' . table ( 'order' ) . '
		WHERE
			productid=' . intval ( $id ) . '
		AND
			' . logic ( 'pay' )->OrderPaidSQL () . '
		AND
			status = ' . ORD_STA_Normal;
		$result = dbc ( DBCMax )->query ( $sql )->limit ( 1 )->done ();
		return ( int ) $result ['sums'];
	}
	function BuyersCount($id) {
		$sql = 'SELECT COUNT(1) AS cnts
		FROM
			' . table ( 'order' ) . '
		WHERE
			productid = ' . intval ( $id ) . '
		AND
			' . logic ( 'pay' )->OrderPaidSQL () . '
		AND
			status = ' . ORD_STA_Normal;
		$result = dbc ( DBCMax )->query ( $sql )->limit ( 1 )->done ();
		return ( int ) $result ['cnts'];
	}
	function Surplus($maxnum, $sells) {
		$surplusnum = $maxnum - $sells;
		return $surplusnum;
	}
	/**
	 * 1、产品ID是否存在--请选择你要购买的产品！
	 * 2、是否显示--没有找到相应的产品！
	 * 3、开始时间--还没有开始哦！
	 * 4、结束时间--已经结束了哦！
	 * @param unknown $id 产品ID
	 * @param string $checkIfBuyed 是否是检查是否已经购买，默认是；这里判断的是已经支付的
	 * @param string $curBuys 当前的购买数量
	 * @param string $ord_is_Paid 当前的订单状态
	 * @param number $uid 用户ID
	 * @param number $amount 一次购买数量
	 * @return multitype:NULL |Ambigous <boolean, unknown, multitype:NULL >
	 */
	function BuysCheck($id, $checkIfBuyed = true, $curBuys = false, $ord_is_Paid = null, $uid = 0, $amount = 0) {
		$id = ( int ) $id;
		if (! $id)
			return array (
					'false' => __ ( '请选择你要购买的产品！' ) 
			);
		$sql = 'SELECT *
		FROM
			' . table ( 'product' ) . '
		WHERE (display = ' . PRO_DSP_Global . ' OR display = ' . PRO_DSP_City.') and id = ' . $id;
		$product = dbc ( DBCMax )->query ( $sql )->limit ( 1 )->done ();
		if (! $product ['id'])
			return array (
					'false' => __ ( '没有找到相应的产品！' ) 
			);
		$now = time ();
		if ($product ['begintime'] > $now)
			return array (
					'false' => __ ( TUANGOU_STR . '还没有开始哦！' ) 
			);
		if ($product ['overtime'] < $now)
			return array (
					'false' => __ ( TUANGOU_STR . '已经结束了哦！' ) 
			);
		if ($product ['maxnum'] > 0) {
			//剩余数量
			$surplus = $this->Surplus ( $product ['maxnum'], $this->SellsCount ( $id ) );
			
			if ($curBuys && $ord_is_Paid === ORD_PAID_Yes) {
				//是确认支付成功的，则把购买的数量加上，以免出现卖完或者不够
				$surplus += $curBuys;
			}
			if (! $surplus)
				return array (
						'false' => __ ( '该产品已经卖完了！下次请赶早' ) 
				);
			if ($curBuys && $curBuys > $surplus)
				return array (
						'false' => __ ( '该产品库存已经不足，请重新下单购买！' ) 
				);
		}
		if ($checkIfBuyed && $product ['multibuy'] == 'false') {
			$buid = $uid ? $uid : user ()->get ( 'id' );
			if ($this->AlreadyBuyed ( $id, $buid ))
				return array (
						'false' => __ ( '您已经购买过此产品了哦！' ) 
				);
		}
		if ($amount > 0 && $product ['oncemax'] > 0 && $amount > $product ['oncemax']) {
			return array (
					'false' => __ ( '您一次不能购买这么多！' ) 
			);
		}
		return $this->__parse_result ( $product );
	}
	function AlreadyBuyed($id, $uid) {
		$sql = '
		SELECT
			orderid
		FROM
			' . table ( 'order' ) . '
		WHERE
			productid = ' . ( int ) $id . '
		AND
			userid= ' . ( int ) $uid . '
		AND
			pay=1';
		$result = dbc ()->Query ( $sql )->GetRow ();
		return $result ? true : false;
	}
	function Maintain($pid = false) {
		logic ( 'product' )->UpdateSTATUS ();
		if ($pid) {
			$product = logic ( 'product' )->GetOne ( $pid, false );
			if ($product ['succ_remain'] <= 0) {
				logic ( 'order' )->findSuccess ( $pid );
			}
			$sellsCount = $this->SellsCount ( $pid );
			if (! $product ['is_countdown'] && $sellsCount) {
				$this->Update_direct ( $pid, array (
						'sells_count' => $sellsCount 
				) );
			}
		}
	}
	function UpdateSTATUS() {
		$now = time ();
		$sqls = array (
				'UPDATE ' . table ( 'product' ) . ' SET status=' . PRO_STA_Failed . ' WHERE successnum>(virtualnum+totalnum) AND overtime<' . $now . ' AND begintime<' . $now,
				'UPDATE ' . table ( 'product' ) . ' SET status=' . PRO_STA_Finish . ' WHERE successnum<=(virtualnum+totalnum) AND overtime<' . $now . ' AND begintime<' . $now,
				'UPDATE ' . table ( 'product' ) . ' SET status=' . PRO_STA_Normal . ' WHERE successnum>(virtualnum+totalnum) AND overtime>' . $now . ' AND begintime<' . $now,
				'UPDATE ' . table ( 'product' ) . ' SET status=' . PRO_STA_Success . ' WHERE successnum<=(virtualnum+totalnum) AND overtime>' . $now . ' AND begintime<' . $now 
		);
		$r = 0;
		foreach ( $sqls as $i => $sql ) {
			$r += dbc ( DBCMax )->query ( $sql )->done ();
		}
		$r && zlog ( 'product' )->maintain ( $r );
	}
	/**
	 * 产品格式化
	 * @param unknown $product
	 * @return boolean|multitype:Ambigous <boolean, unknown, multitype:NULL > |unknown
	 */
	private function __parse_result($product) {
		if (! $product)
			return false;
		if (is_array ( $product [0] )) {
			$returns = array ();
			foreach ( $product as $i => $one ) {
				$returns [] = $this->__parse_result ( $one );
			}
			return $returns;
		}
		$product ['price'] *= 1;
		$product ['nowprice'] *= 1;
		if ($product ['nowprice'] > 0) {
			//折扣
			$product ['discount'] = round ( 10 / ($product ['price'] / $product ['nowprice']), 1 );
		} else {
			$product ['discount'] = 0;
		}
		if ($product ['discount'] <= 0)
			$product ['discount'] = 0;
		//剩余时间
		$product ['time_remain'] = $product ['overtime'] - time ();
		//successnum成功团购人数要求
		$product ['succ_total'] = $product ['successnum'];
		if ($product ['type'] == 'prize') {
			//中奖人数，去查的中奖纪录表
			$product ['succ_real'] = logic ( 'prize' )->sigCount ( 'pid=' . $product ['id'] );
		} else {
			//已经支付订单数(一个订单会有多个产品)，去查的订单表，COUNT(1) AS cnts
			$product ['succ_real'] = $this->BuyersCount ( $product ['id'] );
		}
		//支付人数=实际支付人数+虚拟人数
		$product ['succ_buyers'] = $product ['succ_real'] + $product ['virtualnum'];
		//上下多少人才能成团=成功团购人数要求-已经支付人数
		$product ['succ_remain'] = $product ['succ_total'] - $product ['succ_buyers'];
		
		if ($product ['type'] == 'prize') {
			//中奖数量
			$product ['sells_real'] = $product ['succ_real'];
		} else {
			//已经支付的产品数(一个订单会有多个产品)，SUM(productnum) AS sums
			$product ['sells_real'] = $this->SellsCount ( $product ['id'] );
		}
		//总的订单数：如果是限购模式，则是sells_count；否则则是已经支付+虚拟人数
		//sells_count用于限购模式，只要产生订单就加，FreeCountDownOrder的时候会减去
		$product ['sells_count'] = ($product ['is_countdown'] ? ( int ) $product ['sells_count'] : $product ['sells_real']) + $product ['virtualnum'];
		if ($product ['oncemin'] <= 0) {
			$product ['oncemin'] = 1;
		}
		if ($product ['maxnum'] > 0) {
			//剩余多少件
			$product ['surplus'] = $this->Surplus ( $product ['maxnum'], $product ['sells_real'] );
		} else {
			//不限制
			$product ['surplus'] = 9999;
		}
		
		$product ['imgs'] = ($product ['img'] != '') ? explode ( ',', $product ['img'] ) : null;
		$product ['img'] = $product ['imgs'] [0];
		$product ['sellermap'] = explode ( ',', $product ['sellermap'] );
		if ($product ['type'] == 'stuff') {
			//重量
			$product ['weightsrc'] = $product ['weight'];
			//重量单位
			$product ['weightunit'] = ($product ['weight'] >= 1000) ? 'kg' : 'g';
			$product ['weight'] *= ($product ['weightunit'] == 'kg') ? 0.001 : 1;
		}
		$this->PresellParser ( $product );
		//标签
		$product ['tags'] = logic ( 'product_tag' )->get_list ( $product ['id'] );
		return $product;
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
		$base = 'productid=' . $product ['id'];
		$STA_Normal = 'status=' . ORD_STA_Normal;
		$product ['mny_all'] = ( float ) logic ( 'order' )->Summary ( $base . ' AND ' . $STA_Normal );
		$product ['mny_paid'] = ( float ) logic ( 'order' )->Summary ( $base . ' AND pay=' . ORD_PAID_Yes . ' AND ' . $STA_Normal );
		$product ['mny_waited'] = ( float ) logic ( 'order' )->Summary ( $base . ' AND pay=' . ORD_PAID_No . ' AND ' . $STA_Normal );
		$product ['mny_refund'] = ( int ) logic ( 'order' )->Summary ( $base . ' AND status=' . ORD_STA_Refund );
	}
	private function PresellParser(&$product) {
		if (isset ( $product ['id'] ) && $product ['id']) {
			$ptext = meta ( 'p_presell_text_' . $product ['id'] );
			if ($ptext) {
				$pprice = meta ( 'p_presell_price_full_' . $product ['id'] );
				$product ['presell'] = array (
						'text' => $ptext,
						'price_full' => $pprice 
				);
			}
		}
	}
	public function PresellSubmit($id) {
		if (post ( 'presell_is' )) {
			meta ( 'p_presell_text_' . $id, post ( 'presell_text' ) );
			meta ( 'p_presell_price_full_' . $id, post ( 'presell_price' ) );
		} else {
			meta ( 'p_presell_text_' . $id, null );
			meta ( 'p_presell_price_full_' . $id, null );
		}
	}
	public function STA_Name($STA_Code) {
		$STA_NAME_MAP = array (
				PRO_STA_Failed => '已结束，' . TUANGOU_STR . '失败',
				PRO_STA_Normal => '进行中，未成团',
				PRO_STA_Success => '进行中，已成团',
				PRO_STA_Finish => '已结束，' . TUANGOU_STR . '成功',
				PRO_STA_Refund => '已结束，已经返款' 
		);
		return $STA_NAME_MAP [$STA_Code];
	}
	public function reSort($productList) {
		foreach ( $productList as $i => $product ) {
			if ($product ['surplus'] < 0 && $product ['order'] > 0) {
				logic ( 'product' )->Update ( $product ['id'], array (
						'order' => 0 
				) );
			}
		}
	}
	public function ClearDraft($pID, $dID, $exceptPID = false) {
		if ($pID) {
			$sql_filter = '1';
			$exceptPID && $sql_filter = 'id<>' . $exceptPID;
			$whereSQL = 'saveHandler="draft" AND draft=' . $pID . ' AND ' . $sql_filter;
			$affCount = dbc ( DBCMax )->delete ( 'product' )->where ( $whereSQL )->done ();
			zlog ( 'product' )->draftClear ( $whereSQL, $affCount );
		}
		if (post ( 'draft-pro-id' ))
			$dID = post ( 'draft-pro-id', 'int' );
		if ($dID > 0 && $dID != $pID) {
			meta ( 'p_hs_' . $dID, null );
			meta ( 'p_ir_' . $dID, null );
			meta ( 'expresslist_of_' . $dID, null );
			meta ( 'paymentlist_of_' . $dID, null );
			if ($pID == 0) {
				$whereSQL = 'saveHandler="draft" AND id=' . $dID;
				$affCount = dbc ( DBCMax )->delete ( 'product' )->where ( $whereSQL )->done ();
				zlog ( 'product' )->draftClear ( $whereSQL, $affCount );
			}
		}
		logic ( 'catalog' )->ProUpdate ();
	}
	public function allowCSaveHandler($pid, $newHandler) {
		if (! in_array ( $newHandler, array (
				'normal',
				'draft' 
		) ))
			return false;
		$product = logic ( 'product' )->SrcOne ( $pid );
		if (! in_array ( $product ['saveHandler'], array (
				'normal',
				'draft' 
		) ))
			return false;
		if ($product ['saveHandler'] == 'normal' && $newHandler == 'draft')
			return false;
		return true;
	}
	public function GetDraftCount() {
		$r = dbc ( DBCMax )->select ( 'product' )->in ( 'COUNT(1) AS DrfCount' )->where ( 'saveHandler="draft"' )->limit ( 1 )->done ();
		return $r ['DrfCount'];
	}
	public function GetDraftList() {
		return dbc ( DBCMax )->select ( 'product' )->where ( 'saveHandler="draft"' )->done ();
	}
	public function CheckProductDraft($pid) {
		return dbc ( DBCMax )->select ( 'product' )->where ( 'saveHandler="draft" AND draft=' . $pid )->order ( 'addtime.DESC' )->limit ( 1 )->done ();
	}
	function productCheck($id, $city = '') {
		$id = (is_numeric ( $id ) ? $id : 0);
		$now = time ();
		if ($city != '') {
			$sql = 'select * from ' . TABLE_PREFIX . 'tttuangou_product where begintime <= ' . $now . ' and overtime > ' . $now . ' and id = ' . $id . ' and (city = ' . floatval ( $city ) . ' or display = 2)';
		} else {
			$sql = 'select * from ' . TABLE_PREFIX . 'tttuangou_product where begintime <= ' . $now . ' and overtime > ' . $now . ' and id = ' . $id;
		}
		$query = dbc ()->Query ( $sql );
		if (! $query) {
			return false;
		}
		$product = $query->GetRow ();
		$product ['price'] *= 1;
		$product ['nowprice'] *= 1;
		return $product;
	}
	function AddSellerProNum($sellerid) {
		$sql = 'update ' . TABLE_PREFIX . 'tttuangou_seller set productnum = productnum + 1 where id = ' . floatval ( $sellerid );
		$query = dbc ()->Query ( $sql );
		return true;
	}
	function DelSellerProNum($sellerid) {
		$sql = 'update ' . TABLE_PREFIX . 'tttuangou_seller set productnum = productnum - 1 where id = ' . floatval ( $sellerid );
		$query = dbc ()->Query ( $sql );
		return true;
	}
	function AddSellerSucNum($sellerid) {
		$sql = 'update ' . TABLE_PREFIX . 'tttuangou_seller set successnum = successnum + 1 where `is_countdown`=0 AND id = ' . floatval ( $sellerid );
		$query = dbc ()->Query ( $sql );
		return true;
	}
	function AddSellerTotMoney($sellerid, $money) {
		$sql = 'update ' . TABLE_PREFIX . 'tttuangou_seller set money = money + ' . $money . ' where id = ' . floatval ( $sellerid );
		$query = dbc ()->Query ( $sql );
	}
	function delSellerTotMoney($sellerid, $money) {
		$sql = 'update ' . TABLE_PREFIX . 'tttuangou_seller set money = money - ' . $money . ' where id = ' . floatval ( $sellerid );
		$query = dbc ()->Query ( $sql );
	}
	function GetUserSellerProduct($uid) {
		$pids = array ();
		$sinfo = dbc ( DBCMax )->query ( 'select id from ' . table ( 'seller' ) . " where userid='" . $uid . "'" )->limit ( 1 )->done ();
		$sql = "SELECT id FROM " . table ( 'product' ) . " WHERE sellerid ='" . $sinfo ['id'] . "'";
		$product = dbc ( DBCMax )->query ( $sql )->done ();
		if ($product) {
			foreach ( $product as $key => $val ) {
				$pids [] = $val ['id'];
			}
		}
		return $pids;
	}
	function GetOwnerLink($sellerid) {
		$sql = "SELECT `id`,`name` FROM `" . table ( 'product' ) . "` WHERE `display`>0 AND `saveHandler`='normal' AND `linkid`=0 AND `sellerid`='" . $sellerid . "'";
		$data = dbc ( DBCMax )->query ( $sql )->done ();
		return $data;
	}
	public function get_link_product($linkid) {
		$data = array ();
		if ($linkid > 0) {
			$sql = 'SELECT * FROM `' . table ( 'product_link' ) . '` WHERE `id`=' . $linkid;
			$data = dbc ( DBCMax )->query ( $sql )->limit ( 1 )->done ();
			if ($data) {
				$data = $this->_format_link_data ( $data );
			}
		}
		return $data;
	}
	private function _format_link_data($data) {
		$pids = array ();
		$linkproduct = unserialize ( $data ['link_product'] );
		foreach ( $linkproduct as $k => $v ) {
			$pids [] = $v ['pid'];
		}
		$productnames = dbc ( DBCMax )->query ( 'SELECT id,name FROM `' . table ( 'product' ) . '` WHERE `id` IN(' . implode ( ',', $pids ) . ')' )->done ();
		foreach ( $linkproduct as $lk => $lv ) {
			foreach ( $productnames as $pk => $pv ) {
				if ($lv ['pid'] == $pv ['id']) {
					$linkproduct [$lk] ['product_name'] = $pv ['name'];
				}
			}
		}
		$data ['products'] = $linkproduct;
		$data ['pids'] = $pids;
		return $data;
	}
	public function get_link_list($sellerid = 0) {
		$data = array ();
		if ($sellerid > 0) {
			$sql = 'SELECT * FROM `' . table ( 'product_link' ) . '` WHERE `sellerid`=' . $sellerid;
		} else {
			$sql = 'SELECT * FROM `' . table ( 'product_link' ) . '`';
		}
		$sql = page_moyo ( $sql );
		$data = dbc ( DBCMax )->query ( $sql )->done ();
		if ($data) {
			foreach ( $data as $key => $val ) {
				$data [$key] = $this->_format_link_data ( $val );
			}
		}
		return $data;
	}
	public function linksave($sellerid = 0, $data = array()) {
		if ($sellerid > 0 && $data && is_array ( $data ) && count ( $data ) > 1) {
			$pids = array ();
			foreach ( $data as $k => $v ) {
				$pids [] = $v ['pid'];
			}
			$ldata = array (
					'sellerid' => $sellerid,
					'link_product' => serialize ( $data ) 
			);
			$rid = dbc ( DBCMax )->insert ( 'product_link' )->data ( $ldata )->done ();
			if ($rid > 0) {
				dbc ( DBCMax )->update ( 'product' )->data ( array (
						'linkid' => $rid 
				) )->where ( 'id IN(' . implode ( ',', $pids ) . ')' )->done ();
			}
		}
	}
	public function deletelink($id = 0) {
		if ($id > 0 && $this->check_link_byid ( $id )) {
			dbc ( DBCMax )->delete ( 'product_link' )->where ( array (
					'id' => $id 
			) )->limit ( 1 )->done ();
			dbc ( DBCMax )->update ( 'product' )->data ( array (
					'linkid' => '0' 
			) )->where ( array (
					'linkid' => $id 
			) )->done ();
		}
	}
	public function check_link_byid($id = 0) {
		$return = false;
		if ($id > 0) {
			$linkinfo = $this->get_link_product ( $id );
			if ($linkinfo) {
				if (MEMBER_ROLE_TYPE == 'seller') {
					$sellerid = logic ( 'seller' )->U2SID ( MEMBER_ID );
					if ($sellerid == $linkinfo ['sellerid']) {
						$return = true;
					}
				} elseif (MEMBER_ROLE_TYPE == 'admin') {
					$return = true;
				}
			}
		}
		return $return;
	}
	public function updatelink($id = 0, $data = array()) {
		if ($id > 0 && $data && is_array ( $data ) && count ( $data ) > 1 && $this->check_link_byid ( $id )) {
			$pids = array ();
			foreach ( $data as $k => $v ) {
				$pids [] = $v ['pid'];
			}
			dbc ( DBCMax )->update ( 'product_link' )->data ( array (
					'link_product' => serialize ( $data ) 
			) )->where ( array (
					'id' => $id 
			) )->done ();
			dbc ( DBCMax )->update ( 'product' )->data ( array (
					'linkid' => '0' 
			) )->where ( array (
					'linkid' => $id 
			) )->done ();
			dbc ( DBCMax )->update ( 'product' )->data ( array (
					'linkid' => $id 
			) )->where ( 'id IN(' . implode ( ',', $pids ) . ')' )->done ();
		}
	}
}
?>