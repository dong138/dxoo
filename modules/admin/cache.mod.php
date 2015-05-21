<?php
/**
 * @package php
 * @name cache.mod.php
 * @date 2014-09-01 17:24:22
 */
 



class ModuleObject extends MasterObject
{

	
	function ModuleObject($config)
	{
		$this->MasterObject($config);

		Load::moduleCode($this);$this->Execute();
	}
	function Execute()
	{
		switch($this->Code)
		{

			default:
				$this->Main();
				break;
		}
	}
	function Main()
	{
		$this->CheckAdminPrivs('cache');
		$this->clearAll();
	}
	function clearAll()
	{
		$this->CheckAdminPrivs('cache');
		include(LIB_PATH.'io.han.php');
		$IO=new IoHandler();
		@$IO->ClearDir(CACHE_PATH);
		@$IO->ClearDir(ROOT_PATH . '/uc_client/data/cache/');
		
		$this->DatabaseHandler->Query("DELETE FROM ".TABLE_PREFIX.'system_failedlogins', 'UNBUFFERED');
		
		$this->Messager("缓存已清空",null);
	}

}
?>