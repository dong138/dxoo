<?php

/**
 * 模块：热门商圈
 * @package module
 * @name circle.mod.php
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
		$this->CheckAdminPrivs('circle');
		$city_list=logic('misc')->CityList(0, true);
		$settings = ConfigHandler::get('product');
		$default_city_id = $settings['default_city'];
		include(handler('template')->file("@admin/circle_list"));
	}
	function Add_ajax()
	{
		$this->CheckAdminPrivs('circle','ajax');
		$cityid = get('cityid', 'int');
		$placeid = get('placeid', 'int');
		$cityid || $cityid = 0;
		$placeid || $placeid = 0;
		logic('circle')->Delete( $cityid,$placeid );
		$cityid && $placeid && $add = logic('circle')->Add ( $cityid,$placeid );
		echo $add>0 ? "0" : "1";//0成功1失败
	}
	function Del_ajax()
	{
		$this->CheckAdminPrivs('circle','ajax');
		$cityid = get('cityid', 'int');
		$placeid = get('placeid', 'int');
		$cityid & $placeid || exit('false');
		logic('circle')->Delete($cityid,$placeid);
		exit('0');
	}
}
?>