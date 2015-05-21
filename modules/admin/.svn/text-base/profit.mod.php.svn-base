<?php
/**
 * Created by PhpStorm.
 * User: lofzefjg
 * Date: 2015/4/7
 * Time: 15:30
 */
class ModuleObject extends MasterObject
{
    function ModuleObject($config)
    {
        $this->MasterObject($config);
        $runCode = Load::moduleCode($this);
        $this->$runCode ();
    }

    function Lists()
    {
        $months = array(1,2,3,4,5,6,7,8,9,10,11,12);
        $years = array();
        $minYear = 2015;
        $maxYear = date('Y');
        for($i=0;$minYear<=$maxYear;$i++,$minYear++){
            $years[$i]=$minYear;
        }
        $sellerid = $_GET['sellerid'];
//        $userid = $_GET['userid'];
        $year = $_GET['year'];
        $month = $_GET['month'];
        $result = logic('profit')->getList($sellerid,$year,$month);
        $sums = logic('profit')->querySum($sellerid);
        include handler('template')->file('@admin/profit_list');
    }

    public function recommendUser(){
        $sid = $_GET['sellerid'];
        $stime = strtotime(get('stime'));
        $etime = strtotime(get('etime'));
        if($etime) $etime = strtotime('+1 day',$etime);
        $users = logic('profit')->getUsersBySellerId($sid,$stime,$etime);
        include handler('template')->file('@admin/seller_recommend_User');
    }
}