<?php

/**
 * 模块：热门团购
 * @package module
 * @name group.mod.php
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
		$this->CheckAdminPrivs('group');
		$city_list=logic('misc')->CityList(0, true);
		$settings = ConfigHandler::get('product');
		$default_city_id = $settings['default_city'];
		include(handler('template')->file("@admin/group_list"));
	}
	function catalog()
	{
		$this->CheckAdminPrivs('group');
		$cityId = get('cid', 'int');
		$city = dbc(DBCMax)->select('city')->where(array('cityid' => $cityId))->limit(1)->done();
		$places = logic('city')->get_places($cityId);
		$catalog = logic('group')->Navigate(2);
		include handler('template')->file('@admin/city_group_list');
	}
	function Add_ajax()
	{
		$this->CheckAdminPrivs('group','ajax');
		$cityid = get('cityid', 'int');
		$catalogid = get('catalogid', 'int');
		$cityid || $cityid = 0;
		$catalogid || $catalogid = 0;
		logic('group')->Delete($cityid,$catalogid);
		$cityid && $catalogid && $add = logic('group')->Add ( $cityid,$catalogid );
		echo $add>0 ? "0" : "1";//0成功1失败
	}
	function Del_ajax()
	{
		$this->CheckAdminPrivs('group','ajax');
		$cityid = get('cityid', 'int');
		$catalogid = get('catalogid', 'int');
		$catalogid || $cityid || exit('false');
		logic('group')->Delete($cityid,$catalogid);
		exit('0');
	}
}
?>