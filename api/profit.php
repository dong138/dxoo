<?php
/**
 * Created by PhpStorm.
 * User: lofzefjg
 * Date: 15/3/16
 * Time: 下午3:44
 */
require_once 'init.php';
$sql = 'select * from ystttuangou_order';
$tableName = 'wecher_bind';
$dataList = array('id'=>10,'openid'=>'22234234','userid'=>22,'status'=>1);
$where = 'id = 10';
//var_dump(myDBC()->insert($tableName,$dataList,true));
//var_dump(myDBC()->update($tableName,$dataList,$where));
//$db->get_results -- 从数据库中读取数据集。
//$db->get_row -- 从数据库中读取一行数据。
//$db->get_col -- 从数据库中读取一列指定的数据集。
//$db->get_var -- 从数据库的数据集中读取一个值。
//$db->query -- 执行一条SQL语句。
//$db->debug -- 打印最后执行的SQL语句及其返回的结果。
//$db->vardump -- 打印变量的结构及其内容。
//$db->select -- 选择一个新数据库。
//$db->get_col_info -- 获取列的信息。
//$db->hide_errors -- 隐藏错误。
//$db->show_errors -- 显示错误。

function updateBind()
{
    $sql = 'update `ystttuangou_wecher` as w
      set userid =  (select b.userid from `ystttuangou_wecher_bind` as b where w.openid=b.openid order by b.id limit 0,1)
      where w.userid is null';
    return myDBC()->query($sql);
}
function createWhere($year,$month,$sellerid)
{
    $monthLast = $month+1;
    $yearLast = $year;
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
function querySum($year,$month,$sellerid)
{
    $where = createWhere($year,$month,$sellerid);
    $sql = 'select ROUND(sum((o.productprice-p.fundprice)*t.mutis * 0.3),2) as sumPrice from `ystttuangou_ticket`  as t
        left join `ystttuangou_wecher`  as w on t.uid = w.userid
        left join `ystttuangou_product` as p on p.id = t.productid
        left join `ystttuangou_order` as o on o.orderid = t.orderid
        where t.status = 1
        and p.fundprice >0
        and o.productprice>p.fundprice'.$where.'
        order by t.usetime';
//    return $sql;
    $result = myDBC()->get_var($sql);
    if($result==null)
        $result='0.00';
    return $result;
}
function query($year,$month,$sellerid)
{
    $where = createWhere($year,$month,$sellerid);
    $sql = 'select t.usetime,p.`name`,o.productprice*t.mutis,p.fundprice*t.mutis,
            ROUND((o.productprice-p.fundprice)*t.mutis * 0.3,2) as commission,w.sellerid
            from `ystttuangou_ticket`  as t
        left join `ystttuangou_wecher`  as w on t.uid = w.userid
        left join `ystttuangou_product` as p on p.id = t.productid
        left join `ystttuangou_order` as o on o.orderid = t.orderid
        where t.status = 1
        and p.fundprice >0
        and o.productprice>p.fundprice'.$where.'
        order by t.usetime';
//    return $sql;
//    $sql = 'select t.usetime,p.`name`,o.productprice,p.fundprice,ROUND(((o.productprice-p.fundprice) * 0.3),2) as commission,w.sellerid from `ystttuangou_ticket`  as t
//        left join `ystttuangou_wecher`  as w on t.uid = w.userid
//        left join `ystttuangou_product` as p on p.id = t.productid
//        left join `ystttuangou_order` as o on o.orderid = t.orderid
//        and w.sellerid is not null and w.sellerid<>0
//        and p.fundprice >0
//        and o.productprice>p.fundprice
//        where t.usetime<>"0000-00-00 00:00:00"
//        order by t.usetime';
    return myDBC()->get_results($sql);
}
function checkParam($year,$month,$sellerid)
{
    if(!is_numeric($year))return false;
    if(!is_numeric($month))return false;
    if(!is_numeric($sellerid))return false;
    return true;
}
if(isset($_POST['year']))
{
    $year =  $_POST['year'];
}
if(isset($_POST['month']))
{
    $month =  $_POST['month'];
}
if(isset($_POST['sellerid']))
{
    $sellerid =  $_POST['sellerid'];
}
extract($_GET);
extract($_POST);
if(!checkParam($year,$month,$sellerid))
{
    $arr = array('status'=>'01','msg'=>'参数错误');
    outputJson($arr);
    return;
}

$sumPrice = querySum($year,$month,$sellerid);
if(isset($_POST['sum'])&&$_POST['sum']=='1')
{
    $arr = array('status'=>'00','sumPrice'=>$sumPrice);
    outputJson($arr);
    return;
}
$arr = query($year,$month,$sellerid);
$arr = array('status'=>'00','list'=>$arr,'sumPrice'=>$sumPrice);
outputJson($arr);