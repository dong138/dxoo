<?php

/**
 * 模块：优惠券分类管理
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
		$this->CheckAdminPrivs ( 'ticket' );
		header ( 'Location: ?mod=ticket&code=vlist' );
	}
	
	//分类列表
	function vList() {
		$this->CheckAdminPrivs ( 'ticket' );
		$keyword = post ( 'keyword', 'txt' );
		$action='admin.php?mod=ticket&code=vlist';
		if($keyword){
			$filter =' (id like "%'.$keyword.'%" or name like "%'.$keyword.'%" or pic like "%'.$keyword.'%")';
		}else{
			$filter=1;
		}
		$list = logic ( 'ticket' )->get_list ( $filter ,10);
		$category = logic( 'ticket' )->get_category ( );
		include handler ( 'template' )->file ( '@admin/ticket_category_list' );
	}
	//添加
	function Add() {
		$this->CheckAdminPrivs ( 'ticket' );
		$action = '?mod=ticket&code=Doticket';
		$ticket = logic ('ticket' )->get_one ( $id );
		$category = logic ('ticket')->get_category ( );
		include handler ( 'template' )->file ( '@admin/ticket_category_mgr' );
	}
	
	//添加&编辑处理
	function Doticket() {
		$this->CheckAdminPrivs ( 'ticket' );
		extract ( $this->Get );
		extract ( $this->Post );
		$rebate = array (
				'categoryid' => $categoryid,
				'name' =>$name,
				'begintime' => strtotime($begintime),
				'overtime' => strtotime($overtime),
				'pic' => $pic,
				'max_num' => $max_num,
				'sale_price' => $sale_price,
				'addtime'=>time()
		);
		if($id){
			unset($rebate['addtime']);unset($rebate['pic']);
			$rebate = array_merge($rebate, array('updatetime'=>time()));
			logic('ticket')->update($id, $rebate) ? $this->Messager ( "更新成功", '?mod=ticket' ) : $this->Messager('更新失败', '?mod=ticket');
		}else{
			logic('ticket')->add($rebate) ? $this->Messager ( "添加成功", '?mod=ticket' ) : $this->Messager('添加失败', '?mod=ticket');
		}
	}
	
	//编辑
	function Edit() {
		$this->CheckAdminPrivs ( 'ticket' );
		extract ( $this->Get );
		extract ( $this->Post );
		$action = '?mod=ticket&code=Doticket';
		$ticket = logic( 'ticket' )->get_one ( $id );
		$category = logic( 'ticket' )->get_category ( );
		if(!empty($ticket['pic'])){
			$ticket['pic'] = explode(',', $ticket['pic']);
		}
		include handler ( 'template' )->file ( '@admin/ticket_category_mgr' );
	}

	//删除
	function Deleteticket() {
		$this->CheckAdminPrivs ( 'ticket' );
		$id = get('id', 'int');
		logic ( 'ticket' )->delete ( $id );
		$this->Messager ( '删除成功！', '?mod=ticket' );
	}
	
	function ticket_category_copy(){
		$this->CheckAdminPrivs ( 'ticket' );
		$projectid = get('projectid', 'int');
		$action = '?mod=ticket&code=do_category_copy';
		include handler ( 'template' )->file ( '@admin/ticket_category_copy' );
	}
	
	function do_category_copy() {
		$this->CheckAdminPrivs ( 'ticket' );
		extract ( $this->Get );
		extract ( $this->Post );
// 		var_dump ($_REQUEST);exit;
// 		$rebate = array (
// 				'ticket_projectid' =>$projectid,
// 				'sellerid' => $sellerid,
// 				'pushtime' => $pushtime,
// 				'num' => $num,
// 				'status' => $status,
// 				'addtime'=>time()
// 		);
		
		//复制优惠券信息
		$ori_project = logic('ticket')->get_one($projectid);
		unset($ori_project['id']);
		//var_dump($ori_project);exit;
		$last_copy = logic('ticket')->add($ori_project);
		
		//复制优惠券关联信息
		$ori_ticket_rel = logic('ticket')->getRels($projectid);
		foreach ($ori_ticket_rel as $k => $v){
			unset($v['id']);
			$v['projectid'] = $last_copy;
			logic('ticket')->addRels($v);
		}
		$this->Messager ( '复制成功！', '?mod=ticket' );
		//添加到表seller_num
		//var_dump($rebate);exit;
// 		logic('ticket')->add_seller_num($rebate);
// 		exit('{"info":"添加成功！","status":"y"}');
		// ? $this->Messager ( "添加成功", '?mod=ticket' ) : $this->Messager('添加失败', '?mod=ticket');
		
	}
	
	/*
	 * ======== 以下对 优惠券 & 商品 进行关联 ========
	 *
	 * */
	function product(){
		$this->CheckAdminPrivs ( 'ticket' );
		//树形数据
		$commonid = get ( 'commonid','int' );//分类、城市、商家ID
		$type = get ( 'type');
		
		$projectid = get ( 'projectid', 'int' );
		$key = get ( 'key', 'txt' );
		
		switch ($type){
			case 1:
				$catalog = logic ( 'catalog' )->GetOne ( $commonid );
				$extend = logic ( 'catalog' )->Filter ( $catalog['flag'], 'product' );
				break;
			case 2 :
				$extend = logic ( 'ticket' )->Filter ( $commonid );
				break;
			case 3 :
				$extend= 'p.sellerid=' . $commonid;
				break;
			default:
				$extend=1;
			
		}
		if($key || $type){
			//key存在则是查询product产品用于关联为当前优惠券分类
			$product = logic ( 'ticket' )->product_list ( $key ,$extend );
		}else{
			//key不存在则查询ticket获取当前已关联产品
			$product = logic ( 'ticket' )->ticket_list ( $projectid ,10 );
		}
		$project = logic ( 'ticket' )->get_one ( $projectid );
		include handler ( 'template' )->file ( '@admin/ticket_product_list' );
	}
	
	function toHell(){
		$projectid = get ( 'id', 'int' );
		$type = get ( 'type', 'int' );
		include handler ('template')->file('@admin/hell');
	}

	function updateAjax(){
		$this->CheckAdminPrivs ( 'ticket', 'ajax' );
		$projectid = get ( 'projectid', 'int' );
		$productid = get ( 'productid', 'int' );
		$sellerid = get ( 'sellerid', 'int' );
		$type = get ( 'type', 'int' );
		
		$projectid || exit();
		$productid || exit();			
		$product = logic ( 'ticket' )->ticket_list ( $projectid ,10 );
		$rebate = array(
				'pushtime' => date("Y-m-01 00:00:00"),
				'usednum' => '0',
				'num' => '0',
				'addtime' => time(),
				'status' => '0'
		);
		if($type){//添加、删除
			logic ( 'ticket' )->deleteRel ( $projectid, $productid );
			echo  "0" ;
		}else{
			$getSeller = logic('sellerTicket')->get_seller('sellerid= '.$sellerid.' AND pushtime= '.'"'.date("Y-m-01 00:00:00").'"');
			$rebate['sellerid'] = $sellerid;
			$getSeller ? false : logic('sellerTicket')->add($rebate);
			$add = logic ( 'ticket' )->addRel ( $projectid, $productid );
			echo $add >0 ? "0" : "1";//0成功1失败
		}
	}
	
	function updateAjaxAll(){
		$this->CheckAdminPrivs ( 'ticket', 'ajax' );
		$projectid = get ( 'projectid', 'int' );
		$commonid = get ( 'commonid','int' );//分类、城市、商家ID
		$type = get ( 'type', 'int' );
		$status = get ( 'status', 'int' );
		
		if($type==1){
			$catalog = logic ( 'catalog' )->GetOne ( $commonid );
			$extend = logic ( 'catalog' )->Filter ( $catalog['flag'], 'product' );
		}elseif($type==2){
			$extend = logic ( 'ticket' )->Filter ( $commonid );
		}else{
			$extend= 'p.sellerid=' . $commonid;
		}
		$product = logic ( 'ticket' )->product_list ( "" ,$extend , 1 );
		
		foreach ($product as $i => $value){
			if($status){
				logic ( 'ticket' )->deleteRel ( $projectid, $value['id'] );
			}else{
				logic ( 'ticket' )->deleteRel ( $projectid, $value['id'] );
				$add = logic ( 'ticket' )->addRel ( $projectid, $value['id'] );
				echo $add ? "1" : "0";
			}
		}
	}
	
	//产品分类下拉菜单栏获取
	function tree(){
		$parent = post ( 'id', 'int' );
		$value = post ( 'value' );
		$checkstate = post ( 'checkstate', 'int' );
		
		if($checkstate==1){
			$list=logic('catalog')->GetList($parent);
		}else{
			if ($value == 'city' || $value == 'region'){
				$list=logic('city')->getCity($value,$parent);
				$value = 'region';
			}else{
				$list=logic('city')->getList();
				$value = 'city';
			}
		}
	
		$ret = array();
		foreach ($list as $i => $one)
		{
			$id=$one['id'];
			$text=$one['name'];
			
			if($checkstate==1){
				$value=$one['flag'];
			}else{
				if ($value == 'city'){
					$id=$one['cityid'];
					$text=$one['cityname'];
				}
			}
			
			$ret[] = array(
					"id" => $id,
					"text" => $text,
					"value" => $value,
					"showcheck" => true,
					"complete" => false,
					"isexpand" => false,
					"checkstate" => $checkstate,
					"hasChildren" => true
			);
		}
		echo jsonEncode($ret);
	}
	
	//添加上传图片
	function Addimage()
	{
		$this->CheckAdminPrivs('ticket','ajax');
		$aid = get('aid', 'int');
		$id = get('id', 'int');
		$list = logic('ticket')->get_one($aid);
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
		logic('ticket')->update($aid, array('pic'=>$new));
		exit('ok');
	}
	
	//删除上传图片
	function Delimage()
	{
		$this->CheckAdminPrivs('ticket');
		$aid = get('aid', 'int');
		$id = get('id', 'int');
		$list = logic('ticket')->get_one($aid);
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
			logic('ticket')->update($aid, array('pic'=>$new));
		}
		exit('ok');
	}
}
?>