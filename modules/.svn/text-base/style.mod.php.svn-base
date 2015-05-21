<?php

/**
 * 模块：样式相关
 * @package module
 * @name style.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this, false, false);
		$this->$runCode();
	}
	
	public function load()
	{
		$id = get('id', 'txt');
		ui('style')->setCSS($id);
		$this->Messager('界面切换成功！', $_SERVER['HTTP_REFERER']);
	}
}

?>