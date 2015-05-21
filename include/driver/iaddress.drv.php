<?php

/**
 * 驱动：收货地址导入
 * @package driver
 * @name iaddress.drv.php
 * @version 1.0
 */

class ImportAddressDriver
{
	
	public final function api($name)
	{
		$SID = 'driver.iaddress.api.'.$name;
		$obj = moSpace($SID);
		if ( ! $obj )
		{
			require dirname(__FILE__).'/iaddress/'.$name.'.php';
			$className = $name.'ImportAddressDriver';
			$obj = moSpace($SID, (new $className()));
		}
		return $obj;
	}
}

?>