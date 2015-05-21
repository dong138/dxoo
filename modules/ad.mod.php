<?php

/**
 * 模块：广告管理
 * @package module
 * @name ad.mod.php
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
	
	function Config()
	{
		$this->CheckAdminPrivs('adset');
		$flag = get('flag', 'txt');
		$cfg = ini('ad.'.$flag.'.config');
		include handler('template')->file('@html/ad/'.$flag.'.config');
	}
	function oneConfig()
	{
		$flag = get('flag', 'txt');
		$type= get('type', 'int');
		$cfg = ini('ad.'.$flag.'.config');
		foreach ( $cfg['list'] as $i => $one ) {
			if($one['order']==$type){
				echo('<a href="'.$one['link'].'">');
				echo('<img src="'.ini ( 'settings.site_url' ).'/'.$one['image'].'"></a>');
			}
		}
	}
	function slideConfig()
	{
		$flag = get('flag', 'txt');
		$cfg = ini('ad.'.$flag.'.config');
		foreach ( $cfg['list'] as $i => $one ) {
			echo('<li style="background: url('.ini ( 'settings.site_url' ).'/'.$one['image'].') no-repeat scroll center center transparent; float: left; width: 1440px;">');
			echo('<a href="'.$one['link'].'"></a></li>');
		}
	}
}

?>