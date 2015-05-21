<?php

/**
 * 模块：商家优惠券管理
 * @name sellerTicket.mod.php
 * @version 1.0
 */
class ModuleObject extends MasterObject {
	
	function ModuleObject($config) {
		$this->MasterObject ( $config );
		$runCode = Load::moduleCode ( $this );
		$this->$runCode ();
	}
	
	//首页绑定
	function Main() {
		$this->CheckAdminPrivs ( 'sellerTicket' );
		header ( 'Location: ?mod=sellerTicket&code=vlist' );
	}
	
	//列表
	function vList() {
		$this->CheckAdminPrivs ( 'sellerTicket' );
		$keyword = post ( 'keyword', 'txt' );
		$action='admin.php?mod=sellerTicket&code=vlist';
		if($keyword){
			$filter =' (id like "%'.$keyword.'%" or sellerid like "%'.$keyword.'%" )';
		}else{
			$filter=1;
		}
		$list = logic ( 'sellerTicket' )->get_list ( $filter );
		include handler ( 'template' )->file ( '@admin/seller_ticket_list' );
	}
	
	//添加
	function Add() {
		$this->CheckAdminPrivs ( 'sellerTicket' );
		$action = '?mod=sellerTicket&code=Doticket';
		include handler ( 'template' )->file ( '@admin/seller_ticket_mgr' );
	}
	
	//添加&编辑处理
	function Doticket() {
		$this->CheckAdminPrivs ( 'sellerTicket' );
		extract ( $this->Get );
		extract ( $this->Post );
		$rebate = array (
				'sellerid'=>$sellerid,
				'pushtime' => date("Y-m-d", strtotime($pushtime)),
				'usednum' => $usednum,
				'num' => $num,
				'addtime' => time(),
				'status' => $status
		);
		if($id){
			logic('sellerTicket')->update($id, $rebate) ? $this->Messager ( "更新成功", '?mod=sellerTicket' ) : $this->Messager('更新失败', '?mod=sellerTicket');
		}else{
			logic('sellerTicket')->add($rebate) ? $this->Messager ( "添加成功", '?mod=sellerTicket' ) : $this->Messager('添加失败', '?mod=sellerTicket');
		}
	}
	
	//编辑
	function Edit() {
		$this->CheckAdminPrivs ( 'sellerTicket' );
		extract ( $this->Get );
		extract ( $this->Post );
		$action = '?mod=sellerTicket&code=Doticket';
		$list = logic( 'sellerTicket' )->get_one ( $id );
		include handler ( 'template' )->file ( '@admin/seller_ticket_mgr' );
	}
	
}
?>