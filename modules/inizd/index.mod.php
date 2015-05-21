<?php

/**
 * 模块：默认页面
 * @package module
 * @name index.mod.php
 * @version 1.0
 */


class ModuleObject extends MasterObject
{
    public function __construct($config)
    {
        $runCode = Load::moduleCode($this);
        $this->$runCode();
    }
    public function Main()
    {
        include handler('template')->file('@inizd/index');
    }
}

?>