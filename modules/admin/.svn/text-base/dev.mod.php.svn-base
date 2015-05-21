<?php

/**
 * 模块：开发管理
 * @package module
 * @name dev.mod.php
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
        $this->CheckAdminPrivs('dev');
		include handler('template')->file('@admin/dev_debug_status');
    }
}

?>