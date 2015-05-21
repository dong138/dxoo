<?php

/**
 * 模块：优惠券推送管理
 * @name ticket.mod.php
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
		$this->CheckAdminPrivs ( 'ticket_push' );
		header ( 'Location: ?mod=ticket_push&code=vlist' );
	}
	
	//分类列表
	function vList() {
		$this->CheckAdminPrivs ( 'ticket_push' );
		$keyword = post ( 'keyword', 'txt' );
		$action='admin.php?mod=ticket_push&code=vlist';
		if($keyword){
			$filter =' (id like "%'.$keyword.'%" or name like "%'.$keyword.'%" or pic like "%'.$keyword.'%")';
		}else{
			$filter=1;
		}
		$list = logic ( 'ticket_push' )->get_list ( $filter , 10);
// 		var_dump($list);
		include handler ( 'template' )->file ( '@admin/ticket_push_category_list' );
	}
	
// 	function getPush(){
//         extract($_GET);
//         $is_default = $is_default=='true'?0:1;
//         $sql = dbc ( DBCMax )->update ( 'ticket_push_category' )->where ( array ('id' => $pushid))
//                 ->data(array('is_default'=>$is_default))->done();
//         var_dump( $sql);
// 	}
	function getEnable(){
		extract($_GET);
		$is_enable = $is_enable=='true'?0:1;
		$is_time = $is_time=='true'?0:1;
		$rebate = ($is_enable == 0 && $is_time == 0) ? array('is_enable'=>$is_enable,'is_time'=>$is_time) : array('is_enable'=>$is_enable);
		$sql = dbc ( DBCMax )->update ( 'ticket_push_category' )->where ( array ('id' => $pushid))
		->data($rebate)->done();
		var_dump( $sql);
	}
	function getTime(){
		extract($_GET);
		$is_time = $is_time=='true'?0:1;
		$is_enable = $is_enable=='true'?0:1;
		if($is_enable == 0){
			$sql = dbc ( DBCMax )->update ( 'ticket_push_category' )->where ( array ('id' => $pushid))->data(array('is_time'=>$is_time))->done();
			var_dump( $sql);
		}
	}
	//添加
	function Add() {
		$this->CheckAdminPrivs ( 'ticket_push' );
		$action = '?mod=ticket_push&code=Doticket';
		$list = logic ('ticket_push' )->get_one ( $id );
		include handler ( 'template' )->file ( '@admin/ticket_push_category_mgr' );
	}
	
	//添加&编辑处理
	function Doticket() {
		$this->CheckAdminPrivs ( 'ticket_push' );
		extract ( $this->Get );
		extract ( $this->Post );
		$rebate = array (
				'name' => $name,
				'is_default' => $is_default,
				'addtime' => time(),
				'pushtime' => $pushtime,
				'pic' => $pic
		);
		if($rebate['is_default'] == '1'){
			$rs = logic('ticket_push')->get_default('1');
			if($rs){
				$rs['is_default'] = '0';
				logic('ticket_push')->update($rs['id'], $rs);
			}
		}
		if($id){
			unset($rebate['pic']);
			logic('ticket_push')->update($id, $rebate) ? $this->Messager ( "更新成功", '?mod=ticket_push' ) : $this->Messager('更新失败', '?mod=ticket_push');
		}else{
			logic('ticket_push')->add($rebate) ? $this->Messager ( "添加成功", '?mod=ticket_push' ) : $this->Messager('添加失败', '?mod=ticket_push');
		}
	}
	
	//编辑
	function Edit() {
		$this->CheckAdminPrivs ( 'ticket_push' );
		extract ( $this->Get );
		extract ( $this->Post );
		$action = '?mod=ticket_push&code=Doticket';
		$list = logic( 'ticket_push' )->get_one ( $id );
		if(!empty($list['pic'])){
			$list['pic'] = explode(',', $list['pic']);
		}
		include handler ( 'template' )->file ( '@admin/ticket_push_category_mgr' );
	}

	//删除
	function Deleteticket() {
		$this->CheckAdminPrivs ( 'ticket_push' );
		$id = get('id', 'int');
		logic ( 'ticket_push' )->delete ( $id );
		$this->Messager ( '删除成功！', '?mod=ticket_push' );
	}
	
	function toPush(){
		$this->CheckAdminPrivs ( 'ticket_push' );
		$pushid = get ( 'id', 'int' );
		$keyword = get ( 'key', 'txt' );
		$action='admin.php?mod=ticket_push&code=toPush';
		$isSort = $keyword ? false :true;
		if($keyword){
			$list = logic ( 'ticket_push' )->push_list ( $keyword );
		}else{
			$list = logic ( 'ticket_push' )->ticket_push_list ( $pushid );
		}
		include handler ( 'template' )->file ( '@admin/to_Push' );
	}

	function order()
	{
		$this->CheckAdminPrivs('ticket_push','ajax');
		$id = get('id', 'int');
		$order = get('order', 'int');
		$rebate=array('order'=>$order);
		logic('ticket_push')->relevance_update($id,$rebate);
		echo 'ok';
	}
	function updateAjax(){
		$this->CheckAdminPrivs ( 'ticket_push', 'ajax' );
		$projectid = get ( 'projectid', 'int' );
		$pushid = get ( 'pushid', 'int' );
		$type = get ( 'type', 'int' );
		
		$projectid || exit();
		$pushid || exit();
		
		if($type){//添加、删除
			logic ( 'ticket_push' )->deleteRel ( $projectid, $pushid );
			echo  "0" ;
		}else{
			$add = logic ( 'ticket_push' )->addRel ( $projectid, $pushid );
			echo $add >0 ? "0" : "1";//0成功1失败
		}
	}
	
	//添加上传图片
	function Addimage()
	{
		$this->CheckAdminPrivs('ticket_push','ajax');
		$aid = get('aid', 'int');
		$id = get('id', 'int');
		$list = logic('ticket_push')->get_one($aid);
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
		logic('ticket_push')->update($aid, array('pic'=>$new));
		exit('ok');
	}
	
	//删除上传图片
	function Delimage()
	{
		$this->CheckAdminPrivs('ticket_push');
		$aid = get('aid', 'int');
		$id = get('id', 'int');
		$list = logic('ticket_push')->get_one($aid);
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
			logic('ticket_push')->update($aid, array('pic'=>$new));
		}
		exit('ok');
	}
}
?>