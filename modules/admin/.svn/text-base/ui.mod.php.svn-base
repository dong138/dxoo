<?php

/**
 * 模块：界面设置
 * @package module
 * @name ui.mod.php
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
        $this->CheckAdminPrivs('uiigos');
		header('Location: ?mod=ui&code=igos&op=config');
    }
    function iGOS_config()
    {
        $this->CheckAdminPrivs('uiigos');
		include handler('template')->file('@admin/ui_igos_config');
    }
    function iGOS_save()
    {
        $this->CheckAdminPrivs('uiigos');
		$style = post('style', 'txt');
        ini('ui.igos.style', $style);
        $this->Messager(__('保存成功！'));
    }
}
?>