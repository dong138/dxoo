<?php
/**
 * @name downapp.mod.php
 * @date 2014-09-01 17:24:23
 */
 




class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
				$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	public function main()
	{
		$this->Title = '下载手机版享更多优惠';
		$android_url = ini('settings.site_url').'/get-last-apk.php';
		$iphone = ini('iphone');
		$iphone_url = $iphone['url'];
		include handler('template')->file('downapp');
	}
	function down()
	{
		$android_url = ini('settings.site_url').'/get-last-apk.php';
		$iphone = ini('iphone');
		$iphone_url = $iphone['url'];
		if($_SERVER['HTTP_USER_AGENT'] && preg_match("/(iphone|android)/i",$_SERVER['HTTP_USER_AGENT'],$match))
		{
			if($match[0] == 'iPhone')
			{
				header('Location: '.$iphone_url);exit;
			}
			else
			{
				header('Location: '.$android_url);exit;
			}
		}
		else
		{
			$this->main();
		}
	}
}
?>