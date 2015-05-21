<?php

/**
 * 模块：双乾验证回调接口
 * @package module
 * @name bankdirect.mod.php
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
		echo @logic('pay')->apiz('bankdirect')->tool->callbackMod();
	}

}

?>