<?php

/**
 * 模块：众筹管理
 * @name everybody.mod.php
 * @version 1.0
 */
class ModuleObject extends MasterObject {
	function ModuleObject($config) {
		$this->MasterObject ( $config );
		$runCode = Load::moduleCode ( $this );
		$this->$runCode ();
	}
	function Main() {
		$this->CheckAdminPrivs ( 'everybody_raise' );
		header ( 'Location: ?mod=everybody&code=vlist' );
	}
	function vList() {
		$this->CheckAdminPrivs ( 'everybody_raise' );
		extract ( $this->Get );
		extract ( $this->Post );
		$keyword = post ( 'keyword', 'txt' );
		$action='admin.php?mod=everybody&code=vlist';
		if($keyword){
			$filter =' (id like "%'.$keyword.'%" or raisename like "%'.$keyword.'%" or productid like "%'.$keyword.'%" or voucherid like "%'.$keyword.'%")';
		}else{
			$filter=1;
		}
		$list = logic ( 'everybody' )->GetList (-1, $filter);
		if ($list) {
			foreach ( $list as $key => $val ) {
				$product=logic ( 'everybody' )->GetProductTitle ($val['productid']);
				$list [$key] ['productflag'] =$product['flag'];
				
				$product=logic ( 'everybody' )->GetProductTitle ($val['voucherid']);
				$list [$key] ['voucherflag'] =$product['flag'];
				
				$product=logic ( 'everybody' )->GetProductList ($val['id'], $filter);
				if($product){
					$list[$key]['count_product']=count($product);
				}else{
					$list[$key]['count_product']=0;
				}
			}
		}
		include handler ( 'template' )->file ( '@admin/everybody_raise_list' );
	}
	function RankList(){
		$eid=get('id','int');
		$this->CheckAdminPrivs ( 'everybody_raise' );
		$plist = logic ( 'everybody' )->GetProduct ($eid);
		$res = array();
		$ii = 0;
		foreach ( $plist as $key => $val ) {
			$s+= $ii;
			$e = $val['number'];
			$page = ($val['prize'] - 1) * $val['number'];
			$list= logic ( 'everybody' )->RankList ($eid,$s,$e);
			foreach ($list as $i => $a){
				$list[$i]['productid'] = $val['productid'];
				$list[$i]['prize'] = $val['prize'];
				$list[$i]['name'] = $val['name'];
				$list[$i]['flag'] = $val['flag'];
				$rankcheck=logic ( 'everybody' )->RankCheck ($val['productid'],$val['userid']);
				$list [$i] ['status']= $rankcheck['status'];
			}
			$ii+=$val['number'];
			$res = array_merge($res,$list);
		}
		/* $list= logic ( 'everybody' )->RankList ($eid);
		if ($list) {
			foreach ( $list as $key => $val ) {
				if($key==0){
					$product=logic ( 'everybody' )->GetProductTitle ($val['productid']);
					$rankcheck=logic ( 'everybody' )->RankCheck ($val['productid'],$val['userid']);
				}else{
					$product=logic ( 'everybody' )->GetProductTitle ($val['voucherid']);
					$rankcheck=logic ( 'everybody' )->RankCheck ($val['voucherid'],$val['userid']);
				}
				$list [$key] ['productflag'] =$product['flag'];
				$list [$key] ['status']= $rankcheck['status'];
				$list [$key] ['msg']= $rankcheck['msg'];
			}
		}
		var_dump($list); */
		include handler ( 'template' )->file ( '@admin/everybody_rank_list' );
	}
	public function InfoJson($status,$msg) {
		return jsonEncode( array (
				'status' => $status,
				'msg' => $msg
		));
	}
	function checkout(){
		$id=post('productid','int');
		$uid=post('userid','int');
		$rankcheck=logic ( 'everybody' )->RankCheck ($id,$uid);
		if ($rankcheck['status']==1){
			exit ($this->InfoJson( 1, $rankcheck['msg'] ));
		}
		//判断产品是否已经购买
		$product = logic ( 'product' )->BuysCheck ( $id, true,false, null, $uid = 0 );
		if (isset ( $product ['false'] )) {
			exit ($this->InfoJson( 1, $product ['false'] ));
		}
		//抽奖
		if ($product ['type'] == 'prize') {
			exit ($this->InfoJson( 1, '发送错误！该产品不参加众筹活动！' ));
		}
		exit ($this->InfoJson( 2, '验证成功！' ));
	}
	function Checkout_rank_save() {
		$product_id = post ( 'product_id', 'int' );
		$product = logic ( 'product' )->BuysCheck ( $product_id );
		if (isset ( $product ['false'] )) {
			return $this->__ajax_save_failed ( $product ['false'] );
		}
		$num_buys = post ( 'num_buys', 'int' );
		if (! $num_buys || ($product ['oncemax'] > 0 && $num_buys > $product ['oncemax']) || $num_buys < $product ['oncemin']) {
			return $this->__ajax_save_failed ( __ ( '请填写正确的购买数量！' ) );
		}
		$userid = post ( 'userid', 'int' );
		$order = logic ( 'order' )->GetFree ( $userid , $product_id );
		$order ['productnum'] = $num_buys;
		$order ['productprice'] = $product ['nowprice'];
		$order ['extmsg'] = get ( 'extmsg', 'txt' );
		if ($product ['type'] == 'stuff') {
			logic ( 'address' )->Accessed ( 'order.save', $order );
			logic ( 'express' )->Accessed ( 'order.save', $order );
		}
		logic ( 'notify' )->Accessed ( 'order.save', $order );
		if (! logic ( 'attrs' )->Accessed ( 'order.save', $order )) {
			return $this->__ajax_save_failed ( __ ( '请选择正确的产品属性规格！' ) );
		}
		$price_total = $order ['productprice'] * $order ['productnum'] + $order ['expressprice'];
		logic ( 'attrs' )->order_calc ( $order ['orderid'], $price_total );
		if (( float ) $price_total < 0) {
			return $this->__ajax_save_failed ( __ ( '订单总价不正确，请重新下单！' ) );
		}
		$order ['totalprice'] = $price_total;
		$order ['process'] = '__CREATE__';
		$order ['status'] = ORD_STA_Normal;
		$order ['is_raise'] = ORD_STA_Normal;
		if($price_total==0){//0元支付方式 余额支付 ID=1
			$order ['paytype'] = ORD_STA_Normal;
		}
		if ($product ['is_countdown'] == 1) {
			$order ['is_countdown'] = 1;
			dbc ( DBCMax )->update ( 'product' )->data ( 'sells_count=sells_count+' . ( int ) $num_buys )->where ( 'id=' . $product_id )->done ();
		}
		logic ( 'order' )->Update ( $order ['orderid'], $order );
		$ops = array (
				'status' => 'ok',
				'id' => $order ['orderid'],
				'price'=> $price_total
		);
		if (! X_IS_AJAX) {
			header ( 'Location: ' . rewrite ( '?mod=buy&code=order&id=' . $order ['orderid'] ) );
			exit ();
		}
		echo jsonEncode ( $ops );
	}
	private function __ajax_save_failed($msg) {
		$ops = array (
				'status' => 'failed',
				'msg' => $msg
		);
		if (! X_IS_AJAX) {
			$this->Messager ( $msg, - 1 );
		}
		echo jsonEncode ( $ops );
		return false;
	}
	function Add() {
		$this->CheckAdminPrivs ( 'everybody_raise' );
		$action = '?mod=everybody&code=Doaddeverybody';
		include handler ( 'template' )->file ( '@admin/everybody_raise_mgr' );
	}
	function Doaddeverybody() {
		$this->CheckAdminPrivs ( 'everybody_raise' );
		extract ( $this->Get );
		extract ( $this->Post );
		if ($pic != '')
		{
			$pic = substr($pic, 0, -1);
		}
		$rebate = array (
				'raisename' => $raisename,
				'productid' => ( int )$productid,
				'voucherid' => ( int )$voucherid,
				'number' => $number,
				'status' => ( int ) $status,
				'pic'=>$pic,
				'begintime' => strtotime($begintime),
				'overtime' => strtotime($overtime),
				'rule' => $content,
		);
		if (strtotime($begintime) >= strtotime($overtime)) {
			$this->Messager ( '结束时间不可小于开始时间', - 1 );
		}
		
		$p = logic ( 'product' )->GetOne ( ( int )$productid );
		if(!$p){
			$this->Messager ( '该产品不存在', - 1 );
		}
		$v = logic ( 'product' )->GetOne ( ( int )$voucherid );
		if(!$v){
			$this->Messager ( '该代金券不存在', - 1 );
		}
		if(( int ) $status!=0 && ( int ) $status!=1){
			$this->Messager ( '状态不存在', - 1 );
		}
		
		$id = logic ( 'everybody' )->Add ( $rebate );
		if($id){
			$this->Messager ( "操作成功", '?mod=everybody' );
		}else{
			$this->Messager ( "操作失败", '?mod=everybody' );
		}
	}
	function Edit() {
		$this->CheckAdminPrivs ( 'everybody_raise' );
		extract ( $this->Get );
		extract ( $this->Post );
		$action = '?mod=everybody&code=doediteverybody';
		$sql = 'select * from ' . TABLE_PREFIX . 'tttuangou_everybody_raise where id = ' . $id;
		$query = $this->DatabaseHandler->Query ( $sql );
		$everybody = $query->GetRow ();
		if(!empty($everybody['pic']))
		{
			$everybody['pic']=explode(',', $everybody['pic']);
		}
		$product = logic ( 'product' )->GetOne ( $everybody['productid'] );
		$proudctname=$product['flag'];
		$voucher = logic ( 'product' )->GetOne ( $everybody['voucherid'] );
		$vouchername=$voucher['flag'];
		$rebate = logic ( 'rebate' )->Get_Rebate_setting ( true );
		include (handler ( 'template' )->file ( '@admin/everybody_raise_mgr' ));
	}
	
	function Doediteverybody() {
		$this->CheckAdminPrivs ( 'everybody_raise' );
		extract ( $this->Get );
		extract ( $this->Post );
		$rebate = array (
				'raisename' => $raisename,
				'productid' => ( int )$productid,
				'voucherid' => ( int )$voucherid,
				'number' => $number,
				'status' => ( int ) $status,
				'begintime' => strtotime($begintime),
				'overtime' => strtotime($overtime),
				'rule' => $content,
				'updatetime'=>time()
		);
		if (strtotime($begintime) >= strtotime($overtime)) {
			$this->Messager ( '结束时间不可小于开始时间', - 1 );
		}
		
		$p = logic ( 'product' )->GetOne ( ( int )$productid );
		if(!$p){
			$this->Messager ( '该产品不存在', - 1 );
		}
		$v = logic ( 'product' )->GetOne ( ( int )$voucherid );
		if(!$v){
			$this->Messager ( '该代金券不存在', - 1 );
		}
		if(( int ) $status!=0 && ( int ) $status!=1){
			$this->Messager ( '状态不存在', - 1 );
		}
		
		$update=dbc ( DBCMax )->update ( 'everybody_raise' )->data ( $rebate )->where ( 'id=' . $id )->done ();
		if($update){
			$this->Messager ( "操作成功", '?mod=everybody' );
		}else{
			$this->Messager ( "操作成功", '?mod=everybody' );
		}
	}
	function Deleteraise() {
		$this->CheckAdminPrivs ( 'everybody_raise' );
		extract ( $this->Get );
		$sinfo = dbc ( DBCMax )->query ( 'select * from ' . table ( 'everybody_raise' ) . ' where id=' . $id )->limit ( 1 )->done ();
		if (! $sinfo) {
			$this->Messager ( "删除失败，该众筹已经不存在！", '?mod=everybody' );
		}
		$pinfo = dbc ( DBCMax )->query ( 'select * from ' . table ( 'everybody_product' ) . ' where raiseid=' . $id )->limit ( 1 )->done ();
		if ($pinfo) {
			$this->Messager ( "删除失败，请先删除该众筹下的产品！", '?mod=everybody' );
		}
		$this->DatabaseHandler->SetTable ( TABLE_PREFIX . 'tttuangou_everybody_raise' );
		$result = $this->DatabaseHandler->Delete ( '', 'id=' . intval ( $id ) );
		if($result){
			$this->Messager ( "删除成功", '?mod=everybody' );
		}else{
			$this->Messager ( "删除失败", '?mod=everybody' );
		}
	}
	function Addimage()
	{
		$this->CheckAdminPrivs('everybody_raise','ajax');
		$eid = get('eid', 'int');
		$id = get('id', 'int');
		$list = logic('everybody')->GetOne($eid);
		$imgs = explode(',', $list['pic']);
		foreach ($imgs as $i => $iid)
		{
			if ($iid == '' || $iid == 0)
			{
				unset($imgs[$i]);
			}
		}
		$imgs[] = $id;
		$new = implode(',', $imgs);
		logic('everybody')->Update_Raise($eid, array('pic'=>$new));
		exit('ok');
	}
	function Delimage()
	{
		$this->CheckAdminPrivs('everybody_raise');
		$eid = get('eid', 'int');
		$id = get('id', 'int');
		$list = logic('everybody')->GetOne($eid);
		if ($list['pic'] == '')
		{
			logic('upload')->Delete($id);
		}
		else
		{
			$imgs = explode(',', $list['pic']);
			foreach ($imgs as $i => $iid)
			{
				if ($iid == $id)
				{
					logic('upload')->Delete($id);
					unset($imgs[$i]);
				}
			}
			$new = implode(',', $imgs);
			logic('everybody')->Update_Raise($eid, array('pic'=>$new));
		}
		exit('ok');
	}
	
	//以下众筹产品管理================================================
	function AddProudct(){
		$eid=get('id','int');
		$action = '?mod=everybody&code=Doaddproduct';
		include handler ( 'template' )->file ( '@admin/everybody_product_mgr' );
	}
	
	function Doaddproduct(){
		$this->CheckAdminPrivs ( 'everybody_raise' );
		extract ( $this->Get );
		extract ( $this->Post );
		$rebate = array (
				'raiseid' => ( int )$raiseid,
				'productid' => ( int )$productid,
				'number' => $number,
				'prize' => $prize
		);
		$pinfo = dbc ( DBCMax )->query ( 'select * from ' . table ( 'everybody_product' ) . ' where raiseid=' . $raiseid .' and prize='.$prize )->limit ( 1 )->done ();
		if($pinfo){
			$this->Messager ( '该名次已经存在请修改！', - 1 );
		}
		$p = logic ( 'product' )->GetOne ( ( int )$productid );
		if(!$p){
			$this->Messager ( '该产品不存在', - 1 );
		}
		
		$id = logic ( 'everybody' )->AddProduct ( $rebate );
		if($id){
			$this->Messager ( "操作成功", '?mod=everybody' );
		}else{
			$this->Messager ( "操作失败", '?mod=everybody' );
		}
	}
	
	function pList() {
		$this->CheckAdminPrivs ( 'everybody_raise' );
		extract ( $this->Get );
		extract ( $this->Post );
		$keyword = post ( 'keyword', 'txt' );
		$action='admin.php?mod=everybody&code=plist&id='.$id;
		if($keyword){
			$filter =' (p.name like "%'.$keyword.'%" or p.flag like "%'.$keyword.'%" or productid like "%'.$keyword.'%" or raiseid like "%'.$keyword.'%")';
		}else{
			$filter=1;
		}
		$list = logic ( 'everybody' )->GetProduct ($id, $filter);
		include handler ( 'template' )->file ( '@admin/everybody_product_list' );
	}
	
	function EditProduct() {
		$this->CheckAdminPrivs ( 'everybody_raise' );
		extract ( $this->Get );
		extract ( $this->Post );
		$action = '?mod=everybody&code=doeditproduct';
		$sql = 'select * from ' . TABLE_PREFIX . 'tttuangou_everybody_product where id = ' . $id;
		$query = $this->DatabaseHandler->Query ( $sql );
		$everybody = $query->GetRow ();
		$product = logic ( 'product' )->GetOne ( $everybody['productid'] );
		$proudctname=$product['flag'];
		$rebate = logic ( 'rebate' )->Get_Rebate_setting ( true );
		include (handler ( 'template' )->file ( '@admin/everybody_product_mgr' ));
	}
	function Doeditproduct() {
		$this->CheckAdminPrivs ( 'everybody_raise' );
		extract ( $this->Get );
		extract ( $this->Post );
		$rebate = array (
				'productid' => ( int )$productid,
				'number' => $number,
				'prize' => $prize
		);
		$einfo = dbc ( DBCMax )->query ( 'select * from ' . table ( 'everybody_product' ) . ' where id=' . $id)->limit ( 1 )->done ();
		if ($einfo['prize']!=$prize){
			$pinfo = dbc ( DBCMax )->query ( 'select * from ' . table ( 'everybody_product' ) . ' where raiseid=' . $raiseid .' and prize='.$prize )->limit ( 1 )->done ();
			if($pinfo){
				$this->Messager ( '该名次已经存在请修改！', - 1 );
			}
		}
		$p = logic ( 'product' )->GetOne ( ( int )$productid );
		if(!$p){
			$this->Messager ( '该产品不存在', - 1 );
		}
	
		$update=dbc ( DBCMax )->update ( 'everybody_product' )->data ( $rebate )->where ( 'id=' . $id )->done ();
		if($update){
			$this->Messager ( "操作成功", '?mod=everybody' );
		}else{
			$this->Messager ( "操作成功", '?mod=everybody' );
		}
	}
	
	function Deleteproduct() {
		$this->CheckAdminPrivs ( 'everybody_raise' );
		extract ( $this->Get );
		$sinfo = dbc ( DBCMax )->query ( 'select * from ' . table ( 'everybody_product' ) . ' where id=' . $id )->limit ( 1 )->done ();
		if (! $sinfo) {
			$this->Messager ( "删除失败，该众筹产品已经不存在！", '?mod=everybody' );
		}
		$this->DatabaseHandler->SetTable ( TABLE_PREFIX . 'tttuangou_everybody_product' );
		$result = $this->DatabaseHandler->Delete ( '', 'id=' . intval ( $id ) );
		if($result){
			$this->Messager ( "删除成功", '?mod=everybody' );
		}else{
			$this->Messager ( "删除失败", '?mod=everybody' );
		}
	}
	
	function isprize(){
		$prize = get('param');
		$id = get('param','int');
		$pinfo = dbc ( DBCMax )->query ( 'select * from ' . table ( 'everybody_product' ) . ' where id=' . $id .' and prize='.$prize )->limit ( 1 )->done ();
		if ($pinfo) {
			exit('{"info":"名次已经存在请修改！","status":"n"}');
		}
		exit('{"info":" ","status":"y"}');
	}
	
	//TODO 临时活动管理===================================================================
	function aList(){
		$this->CheckAdminPrivs ( 'everybody_raise' );
		extract ( $this->Get );
		extract ( $this->Post );
		$keyword = post ( 'keyword', 'txt' );
		$action='admin.php?mod=everybody&code=alist&id='.$id;
		if($keyword){
			$filter =' (name like "%'.$keyword.'%" or flag like "%'.$keyword.'%" or productid like "%'.$keyword.'%" or id like "%'.$keyword.'%")';
		}else{
			$filter=1;
		}
		$sql = 'SELECT *
		FROM
			' . table ( 'product_activity' ) . '
		WHERE
		'. $filter .'
		ORDER BY
			id';
		$sql = page_moyo ( $sql );
		$list = dbc ( DBCMax )->query ( $sql )->done ();
		include handler ( 'template' )->file ( '@admin/activity_list' );
	}
	
	function Activity(){
		$eid=get('id','int');
		$action = '?mod=everybody&code=Doaddactivity';
		include handler ( 'template' )->file ( '@admin/activity_mgr' );
	}
	function Doaddactivity(){
		$this->CheckAdminPrivs ( 'everybody_raise' );
		extract ( $this->Get );
		extract ( $this->Post );
		if ($productid) {
			$pid = explode ( ',', $productid );
			foreach ( $pid as $key => $val ) {
				if($val>0){
					$rebate = array (
							'name' =>$aname,
							'productid' =>$val,
							'flag' => $flag
					);
					dbc ()->SetTable ( table ( 'product_activity' ) );
					$id = dbc ()->Insert ( $rebate );
				}
			}
		}
		if($id){
			$this->Messager ( "操作成功", '?mod=everybody&code=alist' );
		}else{
			$this->Messager ( "操作失败", '?mod=everybody&code=alist' );
		}
	}
	function DeleteActivity() {
		$this->CheckAdminPrivs ( 'everybody_raise' );
		extract ( $this->Get );
		$this->DatabaseHandler->SetTable ( TABLE_PREFIX . 'tttuangou_product_activity' );
		$result = $this->DatabaseHandler->Delete ( '', 'id=' . intval ( $id ) );
		if($result){
			$this->Messager ( "删除成功", '?mod=everybody&code=alist' );
		}else{
			$this->Messager ( "删除失败", '?mod=everybody&code=alist' );
		}
	}
	function EditActivity() {
		$this->CheckAdminPrivs ( 'everybody_raise' );
		extract ( $this->Get );
		extract ( $this->Post );
		$action = '?mod=everybody&code=doeditactivity';
		$sql = 'select * from ' . TABLE_PREFIX . 'tttuangou_product_activity where id = ' . $id;
		$query = $this->DatabaseHandler->Query ( $sql );
		$activity = $query->GetRow ();
		include (handler ( 'template' )->file ( '@admin/activity_mgr' ));
	}
	function doeditactivity(){
		$this->CheckAdminPrivs ( 'everybody_raise' );
		extract ( $this->Get );
		extract ( $this->Post );
		$rebate = array (
				'name' =>$aname,
				'productid' =>$productid,
				'flag' => $flag
		);
		$update=dbc ( DBCMax )->update ( 'product_activity' )->data ( $rebate )->where ( 'id=' . $id )->done ();
		if($update){
			$this->Messager ( "操作成功", '?mod=everybody&code=alist' );
		}else{
			$this->Messager ( "操作成功", '?mod=everybody&code=alist' );
		}
	}
	
	//TODO 微信产品管理===================================================================
	function tList(){
		$this->CheckAdminPrivs ( 'everybody_raise' );
		extract ( $this->Get );
		extract ( $this->Post );
		$keyword = post ( 'keyword', 'txt' );
		$action='admin.php?mod=everybody&code=tlist&id='.$id;
		if($keyword){
			$filter =' (name like "%'.$keyword.'%" or flag like "%'.$keyword.'%" or productid like "%'.$keyword.'%" or id like "%'.$keyword.'%")';
		}else{
			$filter=1;
		}
		$sql = 'SELECT *
		FROM
			' . table ( 'tejia' ) . '
		WHERE
		'. $filter .'
		ORDER BY
			id';
		$sql = page_moyo ( $sql );
		$list = dbc ( DBCMax )->query ( $sql )->done ();
		include handler ( 'template' )->file ( '@admin/wecher_list' );
	}
	
	function Addwecher(){
		$eid=get('id','int');
		$action = '?mod=everybody&code=Doaddwecher';
		include handler ( 'template' )->file ( '@admin/wecher_mgr' );
	}
	function Doaddwecher(){
		$this->CheckAdminPrivs ( 'everybody_raise' );
		extract ( $this->Get );
		extract ( $this->Post );
		$rebate = array (
				'pid' =>$pid,
				'status' => $status
		);
		dbc ()->SetTable ( table ( 'tejia' ) );
		$id = dbc ()->Insert ( $rebate );
		if($id){
			$this->Messager ( "操作成功", '?mod=everybody&code=tlist' );
		}else{
			$this->Messager ( "操作失败", '?mod=everybody&code=tlist' );
		}
	}
	function Deletewecher() {
		$this->CheckAdminPrivs ( 'everybody_raise' );
		extract ( $this->Get );
		$this->DatabaseHandler->SetTable ( TABLE_PREFIX . 'tttuangou_tejia' );
		$result = $this->DatabaseHandler->Delete ( '', 'id=' . intval ( $id ) );
		if($result){
			$this->Messager ( "删除成功", '?mod=everybody&code=tlist' );
		}else{
			$this->Messager ( "删除失败", '?mod=everybody&code=tlist' );
		}
	}
	function Editwecher() {
		$this->CheckAdminPrivs ( 'everybody_raise' );
		extract ( $this->Get );
		extract ( $this->Post );
		$action = '?mod=everybody&code=doeditwecher';
		$sql = 'select * from ' . TABLE_PREFIX . 'tttuangou_tejia where id = ' . $id;
		$query = $this->DatabaseHandler->Query ( $sql );
		$wecher = $query->GetRow ();
		include (handler ( 'template' )->file ( '@admin/wecher_mgr' ));
	}
	function doeditwecher(){
		$this->CheckAdminPrivs ( 'everybody_raise' );
		extract ( $this->Get );
		extract ( $this->Post );
		$rebate = array (
				'pid' =>$pid,
				'status' => $status
		);
		$update=dbc ( DBCMax )->update ( 'tejia' )->data ( $rebate )->where ( 'id=' . $id )->done ();
		if($update){
			$this->Messager ( "操作成功", '?mod=everybody&code=tlist' );
		}else{
			$this->Messager ( "操作成功", '?mod=everybody&code=tlist' );
		}
	}
}
?>