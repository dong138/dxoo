<?php
/**
 * Created by PhpStorm.
 * User: lofzefjg
 * Date: 2015/3/31
 * Time: 15:44
 */

class ModuleObject extends MasterObject
{
    function ModuleObject($config)
    {
        $this->MasterObject($config);
        $runCode = Load::moduleCode($this);
        $this->$runCode();
    }
    function main()
    {
        extract($_GET);
        extract($_POST);
        if(!isset($times)||$times=='') {
            $times = date("H:i", time());
            $_POST['times'] = $times;
        }
        $lists = logic('Npushnews_test')->getTests($cityid,$modelid,$days,$times);
        include handler('template')->file('@admin/npushnews_test_list');
    }
}
