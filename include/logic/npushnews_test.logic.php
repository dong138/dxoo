<?php
/**
 * Created by PhpStorm.
 * User: 建国
 * Date: 2015/3/31
 * Time: 15:15
 */
class Npushnews_testLogic{
    public function getDModelList()
    {
        $data = dbc ( DBCMax )->select ( 'd_model' )->done();
        return $data;
    }

    public function getTests($cityid,$modelid,$days,$times){
        $times = str_replace(':','',$times);
        $sql = "select * from ystttuangou_d_relevance where timesid in
            (select id from ystttuangou_d_times where numid in
            ( select id from ystttuangou_d_num where cityid=".$cityid." and num=".$days." and `status`=1)
            and `status`=1 and modelid = ".$modelid."
            and ".$times.">=REPLACE(begintime,':','') and ".$times."<=REPLACE(endtime,':','')) order by `order`";
        $results =  $query = dbc (DBCMax)->Query ( $sql )->done ();
        if(!is_array($results)||!count($results)){
            $sql = "select * from ystttuangou_d_relevance where timesid in
            (select id from ystttuangou_d_times where numid in
            ( select id from ystttuangou_d_num where cityid=".$cityid." and num=0 and `status`=1)
            and `status`=1 and modelid = ".$modelid."
            and ".$times.">=REPLACE(begintime,':','') and ".$times."<=REPLACE(endtime,':','')) order by `order`";
            $results =  $query = dbc (DBCMax)->Query ( $sql )->done ();
        }
        $lists = array();
        if(is_array($results)&&count($results)){
            foreach($results as $i=> $val){
                $typeid = $val['typeid'];
                $where = 'id = '.$typeid;
                $query = dbc (DBCMax)->select('d_type')->where($where)->done();
                $typeName = $query[0]['name'];
                $rTabName = table($query[0]['table']);
                $rin = $query[0]['in'];
                $sql = 'select '.$rin.' from '.$rTabName.' where id ='.$val['commonid'];
                $query = dbc ()->Query ( $sql )->GetAll ();
                if(is_array($query)&&count($query))
                {
                    $lists[$i] = $query[0];
                    $lists[$i]['typeName'] = $typeName;
                }
            }
        }
        return $lists;
    }
}