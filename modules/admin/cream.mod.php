<?php

/**
 * 模块：本周精选
 * @package module
 * @name cream.mod.php
 * @version 1.1
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
		$this->CheckAdminPrivs('cream');
		$city_list=logic('misc')->CityList(0, true);
		$settings = ConfigHandler::get('product');
		$default_city_id = $settings['default_city'];
		include(handler('template')->file("@admin/cream_list"));
	}
	function product()
	{
		$this->CheckAdminPrivs('cream');
		$cityId = get('cid', 'int');
		$key = get('key', 'txt');
		$city = dbc(DBCMax)->select('city')->where(array('cityid' => $cityId))->limit(1)->done();
		if($key){
			//key存在则是查询product产品用于设置产品为本周精选
			$product = logic('cream')->product_list($cityId,$key);
			$isCream = false;
		}else{
			//key不存在则查询cream获取当前城市的本周精选
			$product = logic('cream')->cream_list($cityId);
			$isCream = true;
		}
		include handler('template')->file('@admin/city_cream_list');
	}
	
	function Add_ajax()
	{
		$this->CheckAdminPrivs('cream','ajax');
		$cityid = get('cityid', 'int');
		$productid = get('productid', 'int');
		$cityid || $cityid = 0;
		$productid || $productid = 0;
		logic('cream')->Delete($cityid,$productid);
		$cityid && $productid && $add = logic('cream')->Add ( $cityid,$productid );
		echo $add>0 ? "0" : "1";//0成功1失败
	}
	function cream_order()
	{
		$this->CheckAdminPrivs('cream');
		$creamid = get('id', 'int');
		$cityId = get('cid', 'int');
		$city = dbc(DBCMax)->select('city')->where(array('cityid' => $cityId))->limit(1)->done();
		$order = get('order', 'int');
		if($order){
			$this->DatabaseHandler->SetTable ( TABLE_PREFIX . 'tttuangou_cream' );
			$this->DatabaseHandler->Update ( array ('order' => $order), 'id=' . $creamid );
		}
		$product = logic('cream')->cream_list($cityId);
		$isCream = true;
		include handler('template')->file('@admin/city_cream_list');
	}
	function Del_ajax()
	{
		$this->CheckAdminPrivs('cream','ajax');
		$cityid = get('cityid', 'int');
		$productid = get('productid', 'int');
		$productid || $cityid || exit('false');
		if(logic('cream')->Delete($cityid,$productid)){
			exit('0');
		}
		exit('1');
	}
}
?>