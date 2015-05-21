<?php
/**
 * Created by PhpStorm.
 * User: lofzefjg
 * Date: 2015/4/7
 * Time: 15:33
 */
class ProfitLogic{
    public function getListBySID($sellerid,$uid=null,$notpage=1)
    {
        $where = ' and w.sellerid='.$sellerid;
        if(!is_null($uid))$where .= ' and t.uid='.$uid;
        $sql = 'select t.usetime,p.`name`,o.productprice,p.fundprice,t.mutis,w.userid,
                ROUND((o.productprice-p.fundprice)*t.mutis * 0.3,2) as commission,w.sellerid
                from `ystttuangou_ticket`  as t
                        left join `ystttuangou_wecher`  as w on t.uid = w.userid
                        left join `ystttuangou_product` as p on p.id = t.productid
                        left join `ystttuangou_order` as o on o.orderid = t.orderid
                        where t.status = 1
                        and p.fundprice >0
                        and o.productprice>p.fundprice'.$where.'
                        order by t.usetime desc';
        if($notpage){
            $sql = page_moyo($sql);
        }
        $result = dbc ( DBCMax )->query ( $sql )->done ();
        return $result;
    }

    public function getList($sellerid,$year,$month,$notpage=1)
    {
        $where = $this->createWhere($year,$month,$sellerid);
        $sql = 'select t.usetime,p.`name`,o.productprice,p.fundprice,t.mutis,w.userid,
                ROUND((o.productprice-p.fundprice)*t.mutis * 0.3,2) as commission,w.sellerid
                from `ystttuangou_ticket`  as t
                        left join `ystttuangou_wecher`  as w on t.uid = w.userid
                        left join `ystttuangou_product` as p on p.id = t.productid
                        left join `ystttuangou_order` as o on o.orderid = t.orderid
                        where t.status = 1
                        and p.fundprice >0
                        and o.productprice>p.fundprice'.$where.'
                        order by t.usetime desc';
        if($notpage){
            $sql = page_moyo($sql);
        }
        $result = dbc ( DBCMax )->query ( $sql )->done ();
        return $result;
    }

    function querySum($sellerid,$year=null,$month=null)
    {
        if(!isset($year) || is_null($year) || $year=='') $year = date('Y');
        if(!isset($month) || is_null($month) || $month=='') $month = date('m');
        $where = $this -> createWhere($year,$month,$sellerid);
        $sql = 'select ROUND(sum((o.productprice-p.fundprice)*t.mutis * 0.3),2) as sumPrice from `ystttuangou_ticket`  as t
        left join `ystttuangou_wecher`  as w on t.uid = w.userid
        left join `ystttuangou_product` as p on p.id = t.productid
        left join `ystttuangou_order` as o on o.orderid = t.orderid
        where t.status = 1
        and p.fundprice >0
        and o.productprice>p.fundprice'.$where.'
        order by t.usetime';
        $result = dbc ( DBCMax )->query ( $sql )->done ();
        if($result==null|| !is_array($result) || $result[0]['sumPrice']==null)
            $result='0.00';
        else
            $result = $result[0]['sumPrice'];
        return $result;
    }

    public function getUsersBySellerId($sid,$stime,$etime){
        $where = ' ';
        if($sid){$where.= whereAnd($where).'sellerid='.$sid;}
        if($stime){$where.= whereAnd($where).'scantime>='.$stime;}
        if($etime){$where.= whereAnd($where).'scantime<'.$etime;}
//        $where = ' ';
        $sql = 'select *,(select scantime from ystttuangou_wecher where userid=uid limit 1) as scantime from yssystem_members where uid in(
            select userid from ystttuangou_wecher
            where userid is not null '.$where.')';
//        $sql = 'select * from yssystem_members';
        $sql = page_moyo($sql);
        $result = dbc(DBCMax)->query($sql)->done();
        ArrayTimeToString($result,array('regdate','scantime'));
        return $result;
    }

    function createWhere($year,$month,$sellerid)
    {
        if(!isset($year)||is_null($year)||$year==''){
            return ' and w.sellerid='.$sellerid;
        }
        $monthLast = $month+1;
        $yearLast = $year;
        if(!isset($month) || is_null($month) || $month==''){
            $month = 1;
            $monthLast = 12;

        }
        if($monthLast>12)
        {
            $monthLast = 1;
            $yearLast++;
        }
        $dateStart = $year.'-'.$month.'-1';
        $dateEnd = $yearLast.'-'.$monthLast.'-1';
        $where = ' and t.usetime>="'.$dateStart.'" and t.usetime<"'.$dateEnd.'" and w.sellerid='.$sellerid;
        return $where;
    }
}