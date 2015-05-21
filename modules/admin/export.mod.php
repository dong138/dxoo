<?php

/**
 * 模块：数据导出
 * @package module
 * @name export.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	function Main()
	{
		exit('Modules.export.index');
	}
	function Order()
	{
		$this->CheckAdminPrivs('ordermanage');
		$this->selector('order');
	}
	

	function Seller()
	{
		$this->CheckAdminPrivs('sellerorders');
		$this->selector('seller');
	}
	
	function Order_generate()
	{
		$this->CheckAdminPrivs('ordermanage');
		$format = $this->__set_filter('order');
				$ordSTA = get('ordsta', 'number');
		is_numeric($ordSTA) || $ordSTA = ORD_STA_ANY;
		$ordPROC = get('ordproc', 'string');
		$ordPROC = $ordPROC ? ('process="'.$ordPROC.'"') : '1';
		if(MEMBER_ROLE_TYPE == 'seller'){
			$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
			$asql = 0;
			if($pids){
				$asql = implode(',',$pids);
			}
			$ordPROC .=  ' AND productid IN('.$asql.')';
		}
		$list = logic('order')->GetList(0, $ordSTA, ORD_PAID_ANY, $ordPROC);
				include handler('template')->file('@export/order.'.$format);
		$this->doResult('order', $format);
	}
	
	function Seller_generate()
	{
		$this->CheckAdminPrivs('sellerorders');
		$format = $this->__set_filter('seller');
		//搜索key
		$keyword = isset ( $_POST ['keyword']) ? $this->Post ['keyword'] : $this->Get ['keyword'];
		if ($keyword != '' || $keyword != ''){
			$addsql .= ' and sellername like \'%' . $keyword . '%\' ';
		}
		//订单状态
		$ordproc = isset ( $_POST ['ordproc'] ) ? $this->Post ['ordproc'] : $this->Get ['ordproc'];
		
		if ($ordproc != null || $ordproc != '') {
			$addsql .= " AND b.process = '" . $ordproc . "'";
		}
		
		//起止日期
		$begintime = $this->Post ['iscp_timev_begintime'] == '' ? $this->Get ['iscp_timev_begintime'] : $this->Post ['iscp_timev_begintime'];
		$finishtime = $this->Post ['iscp_timev_finishtime'] == '' ? $this->Get ['iscp_timev_finishtime'] : $this->Post ['iscp_timev_finishtime'];
		if($begintime!=null || $begintime!='')
		{
			$addsql .=" AND b.buytime >= '" . strtotime ( $begintime) . "'";
		}
		if($finishtime!=null || $finishtime!='')
		{
			$addsql .=" AND b.buytime <= '" . (strtotime($finishtime) + 86399) . "'";
		}
		
		//分页码
		$page = isset ( $_POST ['page'] ) ? $this->Post ['page'] : $this->Get ['page'];
		
		
		$sql ='SELECT ';
		$sql .='a.sellername,';
		$sql .='a.sellerurl, ';
		$sql .='b.productprice,';
		$sql .='b.totalprice,';
		$sql .='b.paymoney,';
		$sql .='b.buytime,';
		$sql .='b.process,';
		$sql .='d.`name` as dName,';
		$sql .='c.`name` as cName,';
		$sql .='c.city,';
		$sql .='d.enabled ';
		$sql .='FROM ';
		$sql .='ystttuangou_seller AS a ,';
		$sql .='ystttuangou_order AS b ,';
		$sql .='ystttuangou_product AS c ,';
		$sql .='ystttuangou_payment AS d ';
		$sql .=' WHERE 1=1 ';
		$sql .= $addsql;
		$sql .=' AND a.id = c.sellerid ';
		$sql .=' AND b.productid = c.id ';
		$sql .=' AND b.pay = d.id ';
		
		
		$sqla="SELECT e.* FROM ( ";
		$sqla.=$sql;
		$sqla.= " order by b.buytime desc "; 
		$pagenum=10;
		if($page!=null || $page!="")
		{
			$geneall = get('geneall', 'txt');
			if ($geneall == 'no'){
				$sqla.= " limit " . ($page - 1) * $pagenum . ',' . $pagenum ;
			}
		}
		$sqla.= " ) as e";
		$query = $this->DatabaseHandler->Query ( $sqla );
		$list = $query->GetAll ();
		include handler('template')->file('@export/seller.'.$format);
		$this->doResult('seller', $format);
	}
	
	function Coupon()
	{
		$this->CheckAdminPrivs('coupon');
		$this->selector('coupon');
	}
	function Coupon_generate()
	{
		$this->CheckAdminPrivs('coupon');
		$format = $this->__set_filter('coupon');
				$coupSTA = get('coupsta', 'number');
		is_numeric($coupSTA) || $coupSTA = TICK_STA_ANY;
		$fpids = '';
		if(MEMBER_ROLE_TYPE == 'seller'){
			$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
			$fpids = 0;
			if($pids){
				$fpids = implode(',',$pids);
			}
		}
        $list = logic('coupon')->GetList(USR_ANY, ORD_ID_ANY, $coupSTA, false, $fpids);
				include handler('template')->file('@export/coupon.'.$format);
		$this->doResult('coupon', $format);
	}
	function Delivery()
	{
		$this->CheckAdminPrivs('delivery');
		$this->selector('delivery');
	}
	function Delivery_generate()
	{
		$this->CheckAdminPrivs('delivery');
		$format = $this->__set_filter('delivery');
				$alsend = get('alsend', 'txt');
		$alsend = ($alsend == 'yes') ? DELIV_SEND_Yes : (($alsend == 'no') ? DELIV_SEND_No : DELIV_SEND_OK);
		$list = logic('delivery')->GetList($alsend);
				include handler('template')->file('@export/delivery.'.$format);
		$this->doResult('delivery', $format);
	}
	function Subscribe()
	{
		$this->CheckAdminPrivs('subscribe');
		$this->selector('subscribe');
	}
	function Subscribe_generate()
	{
		$this->CheckAdminPrivs('subscribe');
		$format = $this->__set_filter('subscribe');
				$class = get('class', 'txt');
		$class = $class ? $class : 'mail';
        $list = logic('subscribe')->GetList($class);
				include handler('template')->file('@export/subscribe.'.$format);
		$this->doResult('subscribe', $format);
	}
	private function selector($class)
	{
		$action = $class;
		$filter = $this->__get_filter();
		include handler('template')->file('@admin/export_selector');
	}
	
	
	private function doResult($class, $format)
	{
		$export = ob_get_contents();
		$file = $this->__write_cache($class, $format, $export);
		header('Location: ?mod=export&code=result&file='.$file);
		exit;
	}
	public function result()
	{
		$file = get('file');
		$ops = array(
			'name' => $file,
			'url' => ini('settings.site_url').'/cache/export/'.$file
		);
		exit(jsonEncode($ops));
	}
	private function __write_cache($class, $format, $content)
	{
		$dir = CACHE_PATH.'/export/';
		if (!is_dir($dir))
		{
			@tmkdir($dir);
		}
		$file = $class.'_'.date('YmdHis').'.'.$format;
		file_put_contents($dir.$file, ENC_IS_GBK ? $content : ENC_U2G($content));
		return $file;
	}
	private function __get_filter()
	{
		$url = urldecode(get('referrer'));
				$params = explode('&', $url);
		$_PARMS = array();
		foreach ($params as $query)
		{
			list($key, $val) = explode('=', $query);
			if ($key == 'mod' || $key == 'code')
			{
				continue;
			}
			$_PARMS[$key] = $val;
		}
		$filter = base64_encode(serialize($_PARMS));
		return $filter;
	}
	private function __set_filter($class)
	{
		$geneall = get('geneall', 'txt');
		$filter = unserialize(base64_decode(get('filter')));
		$_GET = array_merge($_GET, $filter);
				$_GET['mod'] = $class;
		$_GET['code'] = 'vlist';
		if ($geneall == 'yes')
		{
						$_GET[EXPORT_GENEALL_FLAG] = EXPORT_GENEALL_VALUE;
		}
		return get('format', 'txt');
	}
}


?>