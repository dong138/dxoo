<?php
/**
 * 模块：默认功能区
 * @package module
 * @name index.mod.php
 * @version 1.0
 */
class ModuleObject extends MasterObject {
	function ModuleObject($config) {
		if($this->checkmobile()){
			header ( 'Location: ' . rewrite ( 'http://m.dxoo.cn' ) );
			exit();
		}
		$this->MasterObject ( $config );
		$runCode = Load::moduleCode ( $this );
		$this->$runCode ();
	}
	
	function checkmobile() {   
	   global $_G;   
	   $mobile = array();   
	   static $mobilebrowser_list =array('iphone', 'android', 'phone', 'mobile', 'wap', 'netfront', 'java', 'opera mobi', 'opera mini',   
		  'ucweb', 'windows ce', 'symbian', 'series', 'webos', 'sony', 'blackberry', 'dopod', 'nokia', 'samsung',   
		  'palmsource', 'xda', 'pieplus', 'meizu', 'midp', 'cldc', 'motorola', 'foma', 'docomo', 'up.browser',   
		  'up.link', 'blazer', 'helio', 'hosin', 'huawei', 'novarra', 'coolpad', 'webos', 'techfaith', 'palmsource',   
		  'alcatel', 'amoi', 'ktouch', 'nexian', 'ericsson', 'philips', 'sagem', 'wellcom', 'bunjalloo', 'maui', 'smartphone',   
		  'iemobile', 'spice', 'bird', 'zte-', 'longcos', 'pantech', 'gionee', 'portalmmm', 'jig browser', 'hiptop',   
		  'benq', 'haier', '^lct', '320x320', '240x320', '176x220');   
	   $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);   
	   if(($v = $this->dstrpos($useragent, $mobilebrowser_list, true))) {   
		$_G['mobile'] = $v;   
		return true;   
	   }   
	   $brower = array('mozilla', 'chrome', 'safari', 'opera', 'm3gate', 'winwap', 'openwave', 'myop');   
	   if($this->dstrpos($useragent, $brower)) return false;   
		 
	   $_G['mobile'] = 'unknown';   
	   if($_GET['mobile'] === 'yes') {   
		return true;   
	   } else {   
		return false;   
	   }   
	  }   
		 
	  function dstrpos($string, &$arr, $returnvalue = false) {   
	   if(empty($string)) return false;   
	   foreach((array)$arr as $v) {   
		if(strpos($string, $v) !== false) {   
		 $return = $returnvalue ? $v : true;   
		 return $return;   
		}   
	   }   
	   return false;   
	  } 

	function Main() {
		$clientUser = get ( 'u', 'int' );
		if ($clientUser != '') {
			handler ( 'cookie' )->setVar ( 'finderid', $clientUser );
			handler ( 'cookie' )->setVar ( 'findtime', time () );
		}
		
		$data = logic ( 'product' )->display ();
		if (! $data && get ( 'page', 'int' ) == 0) {
			header ( 'Location: ' . rewrite ( '?mod=subscribe&code=mail' ) );
			exit ();
		}
		$product = $data ['product'];
		$this->Title = $data ['mutiView'] ? '' : $product ['name'];
		$data ['mutiView'] || mocod ( 'product.view' );
		$data ['mutiView'] || productCurrentView ( $product );
		$favorited = logic ( 'favorite' )->get_one ( $product ['id'] );
		if (INDEX_DEFAULT === true && ini ( 'settings.template_path' ) == 'meituan') {
			$new_product = logic ( 'product' )->GetCream ( 12 );
		}else{
			//登录，并且有特权才能看
// 			if($product['is_special']){
// 				if(MEMBER_ID > 0){
// 					$has = logic('check')->specialCK($product['id'],MEMBER_ID);
// 					if(!$has){
// 						$this->Messager ( __ ( '对不起，您没有权限进入' ), '?mod=index' );
// 					}
// 				}else{
// 					$this->Messager ( __ ( '对不起，您没有权限进入' ), '?mod=index' );
// 				}
// 			}
		}
		include handler ( 'template' )->file ( $data ['file'] );
	}
	function ExpressConfirm() {
		$oid = $this->Get ['id'];
		$result = $this->OrderLogic->orderExpressConfirm ( $oid );
		if ($result) {
			$this->Messager ( __ ( '已经确认收货，本次交易完成！' ), '?mod=me&code=order' );
		} else {
			$this->Messager ( __ ( '无效的订单号！' ), '?mod=me&code=order' );
		}
	}
}
?>