<?php

/**
 * 模块：签到配置
 * @package module
 * @name signIn.mod.php
 * @author lofzefjg
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
    function ModuleObject($config)
    {
        $this->MasterObject($config);
        $runCode = Load::moduleCode($this);
        $this->$runCode();
    }

    function Main()
    {
        if($_SERVER['REQUEST_METHOD']=='POST')
        {
            dbc(DBCMax)->delete('signin_cfg')->done();

            extract ( $this->Post );
            $cfg = array('description'=>stripslashes($description),'pic'=>$pic);
            dbc()->setTable(table('signin_cfg' ));
            dbc()->Insert($cfg);
        }
        else
        {
            $cfg = dbc(DBCMax)->select('signin_cfg')->done();
            if(is_array($cfg) && count($cfg)){
                $cfg = $cfg[0];
                $cfg['pic'] = explode(',',$cfg['pic']);
            }
        }
        include (handler ( 'template' )->file ( '@admin/signinStatic' ));
    }

}