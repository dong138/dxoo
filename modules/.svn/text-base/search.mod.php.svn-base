<?php

/**
 * 模块：搜索
 * @package module
 * @name search.mod.php
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
    
    function Ajax()
    {
        $fid = get('fid', 'txt');
        $wd = get('wd', 'txt');
        $result = logic('isearcher')->Search($fid, $wd);
        exit(jsonEncode($result));
    }
}

?>