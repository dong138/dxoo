<?php

/**
 * 模块：活动分类管理
 * @name activity.mod.php
 * @version 1.0
 */
class ModuleObject extends MasterObject {
	function ModuleObject($config) {
		$this->MasterObject ( $config );
		$runCode = Load::moduleCode ( $this );
		$this->$runCode ();
	}
	function Main() {
		$this->CheckAdminPrivs ( 'activity' );
		header ( 'Location: ?mod=activity&code=vlist' );
	}
	function vList() {
		$this->CheckAdminPrivs ( 'activity' );
		extract ( $this->Get );
		extract ( $this->Post );
		$keyword = post ( 'keyword', 'txt' );
		$action='admin.php?mod=activity&code=vlist';
		if($keyword){
			$filter =' (id like "%'.$keyword.'%" or name like "%'.$keyword.'%" or name like "%'.$keyword.'%")';
		}else{
			$filter=1;
		}
		$list = logic ( 'activity' )->GetList (-1, $filter);
		include handler ( 'template' )->file ( '@admin/activity_category_list' );
	}
	public function InfoJson($status,$msg) {
		return jsonEncode( array (
				'status' => $status,
				'msg' => $msg
		));
	}
	function Add() {
		$this->CheckAdminPrivs ( 'activity' );
		$action = '?mod=activity&code=Doaddcategory';
		include handler ( 'template' )->file ( '@admin/activity_category_mgr' );
	}
	function Doaddcategory() {
		$this->CheckAdminPrivs ( 'activity' );
		extract ( $this->Get );
		extract ( $this->Post );
		$configkey = explode ( ',', $configkey );
		$configkey = serialize($configkey);//序列化
		$rebate = array (
				'name' =>$activity_name,
				'configkey' => $configkey,
				'addtime'=>time()
		);
		dbc ()->SetTable ( table ( 'activity_category' ) );
		$id = dbc ()->Insert ( $rebate );
		if($id){
			$this->Messager ( "操作成功", '?mod=activity' );
		}else{
			$this->Messager ( "操作失败", '?mod=activity' );
		}
	}
	function Edit() {
		$this->CheckAdminPrivs ( 'activity' );
		extract ( $this->Get );
		extract ( $this->Post );
		$action = '?mod=activity&code=Doeditactivity';
		$sql = 'select * from '. table ( 'activity_category' ). ' where id = ' . $id;
		$query = $this->DatabaseHandler->Query ( $sql );
		$activity = $query->GetRow ();
		$configkey = unserialize($activity['configkey']);//反序列化
		$string=implode(',',$configkey);
		$activity['configkey']=$string;
		include (handler ( 'template' )->file ( '@admin/activity_category_mgr' ));
	}
	
	function Doeditactivity() {
		$this->CheckAdminPrivs ( 'activity' );
		extract ( $this->Get );
		extract ( $this->Post );
		$configkey = explode ( ',', $configkey );
		$configkey = serialize($configkey);//序列化
		$rebate = array (
				'name' =>$activity_name,
				'configkey' => $configkey,
				'updatetime'=>time()
		);
		
		$update=dbc ( DBCMax )->update ( 'activity_category' )->data ( $rebate )->where ( 'id=' . $id )->done ();
		if($update){
			$this->Messager ( "操作成功", '?mod=activity' );
		}else{
			$this->Messager ( "操作成功", '?mod=activity' );
		}
	}
	function Deleteactivity() {
		$this->CheckAdminPrivs ( 'activity' );
		extract ( $this->Get );
		$sinfo = dbc ( DBCMax )->query ( 'select * from ' . table ( 'activity_category' ) . ' where id=' . $id )->limit ( 1 )->done ();
		if (! $sinfo) {
			$this->Messager ( "删除失败，该活动分类不存在！", '?mod=activity' );
		}
		$pinfo = dbc ( DBCMax )->query ( 'select * from ' . table ( 'activity_project' ) . ' where categoryid=' . $id )->limit ( 1 )->done ();
		if ($pinfo) {
			$this->Messager ( "删除失败，请先删除该分类下的活动！", '?mod=activity' );
		}
		$this->DatabaseHandler->SetTable ( table ( 'activity_category' ) );
		$result = $this->DatabaseHandler->Delete ( '', 'id=' . intval ( $id ) );
		if($result){
			$this->Messager ( "删除成功", '?mod=activity' );
		}else{
			$this->Messager ( "删除失败", '?mod=activity' );
		}
	}
	
	function Addimage()
	{
		$this->CheckAdminPrivs('activity','ajax');
		$aid = get('aid', 'int');
		$id = get('id', 'int');
		$list = logic('activity')->GetOneProject($aid);
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
		logic('activity')->Update_Project($aid, array('pic'=>$new));
		exit('ok');
	}
	function Delimage()
	{
		$this->CheckAdminPrivs('activity');
		$aid = get('aid', 'int');
		$id = get('id', 'int');
		$list = logic('activity')->GetOneProject($aid);
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
			logic('activity')->Update_Project($aid, array('pic'=>$new));
		}
		exit('ok');
	}
	
	//以下活动项目管理================================================
	function pList() {
		$this->CheckAdminPrivs ( 'activity' );
		extract ( $this->Get );
		extract ( $this->Post );
		$keyword = post ( 'keyword', 'txt' );
		$action='admin.php?mod=activity&code=plist';
		if($keyword){
			$filter =' (id like "%'.$keyword.'%" or name like "%'.$keyword.'%" or name like "%'.$keyword.'%")';
		}else{
			$filter=1;
		}
		$list = logic ( 'activity' )->GetList (-1, $filter);
		include handler ( 'template' )->file ( '@admin/activity_manager_list' );
	}
	
	function manager_list(){
		$this->CheckAdminPrivs ( 'activity' );
		extract ( $this->Get );
		extract ( $this->Post );
		$keyword = post ( 'keyword', 'txt' );
		$action='admin.php?mod=activity&code=plist&id='.$id;
		if($keyword){
			$filter =' (p.name like "%'.$keyword.'%" or p.flag like "%'.$keyword.'%" or productid like "%'.$keyword.'%" or raiseid like "%'.$keyword.'%")';
		}else{
			$filter=1;
		}
		$list = logic ( 'activity' )->GetActivityList (-1,$id, $filter);
		include handler ( 'template' )->file ( '@admin/activity_project_list' );
	}
	
	function AddProject(){
		$cid=get('cid','int');
		$list = logic ( 'activity' )->GetOne ($cid);
		$list['configkey'] = unserialize($list['configkey']);//反序列化
		$action = '?mod=activity&code=DoaddProject';
		include handler ( 'template' )->file ( '@admin/activity_project_mgr' );
	}
	
	function DoaddProject(){
		$this->CheckAdminPrivs ( 'activity' );
		extract ( $this->Get );
		extract ( $this->Post );
		$list = logic ( 'activity' )->GetOne ($cid);
		if(!$list){$this->Messager ( "操作失败", '?mod=activity&code=plist' );}
		$list['configkey'] = unserialize($list['configkey']);//反序列化
		
		$conf=array();
		foreach ( $list['configkey'] as $key => $val ) {
			$code= explode ( '-', $val );
			$typeArry = post ($code[0]);
			array_push($conf, $typeArry);
		}

		$config=array();
		$temp=array();
		foreach ( $conf[0] as $key => $value ) {
			foreach ( $list['configkey'] as $i => $val ) {
				$field= explode ( '-', $val );
				$one=array($field[0]=>$conf[$i][$key]);
				$temp = array_merge($temp, $one);
				
			}
			array_push($config, $temp);
		}
		$config = serialize($config);//序列化
		if ($pic != '')
		{
			$pic = substr($pic, 0, -1);
		}
		$rebate = array (
				'title' => $title,
				'categoryid' => ( int ) $cid,
				'config' => $config,
				'description' => stripslashes($content),
				'begintime' =>strtotime($begintime),
				'overtime' => strtotime($overtime),
				'day_times' => $day_times,
				'lottery_num' => $lottery_num,
				'times' => $times,
				'pic'=>$pic,
				'status' => (int)$status
		);
		$id = logic ( 'activity' )->AddProject ( $rebate );
		if($id){
			$this->Messager ( "操作成功", '?mod=activity&code=plist' );
		}else{
			$this->Messager ( "操作失败", '?mod=activity&code=plist' );
		}
	}
	
	function EditProject() {
		$this->CheckAdminPrivs ( 'activity' );
		extract ( $this->Get );
		extract ( $this->Post );
		$action = '?mod=activity&code=Doeditproject';
		$sql = 'select * from ' .table ( 'activity_project' ) .' where id = ' . $id;
		$query = $this->DatabaseHandler->Query ( $sql );
		$project = $query->GetRow ();
		
		$list = logic ( 'activity' )->GetOne ($cid);
		$list['configkey'] = unserialize($list['configkey']);//反序列化
		
		if(!empty($project['pic']))
		{
			$project['pic']=explode(',', $project['pic']);
		}
		$project['config'] = unserialize($project['config']);//反序列化
		include (handler ( 'template' )->file ( '@admin/activity_project_mgr' ));
	}
	function DoeditProject() {
		$this->CheckAdminPrivs ( 'activity' );
		extract ( $this->Get );
		extract ( $this->Post );
		$list = logic ( 'activity' )->GetOne ($cid);
		if(!$list){$this->Messager ( "操作失败", '?mod=activity&code=plist' );}
		$list['configkey'] = unserialize($list['configkey']);//反序列化
		
		$conf=array();
		foreach ( $list['configkey'] as $key => $val ) {
			$code= explode ( '-', $val );
			$typeArry = post ($code[0]);
			array_push($conf, $typeArry); 
		}
		$config=array();
		$temp=array();
		foreach ( $conf[0] as $key => $value ) {
			foreach ( $list['configkey'] as $i => $val ) {
				$field= explode ( '-', $val );
				$one=array($field[0]=>$conf[$i][$key]);
				$temp = array_merge($temp, $one);
		
			}
			array_push($config, $temp);
		}
		$config = serialize($config);//序列化
		$rebate = array (
				'title' => $title,
				'categoryid' => ( int ) $cid,
				'begintime' =>strtotime($begintime),
				'overtime' => strtotime($overtime),
				'day_times' => $day_times,
				'lottery_num' => $lottery_num,
				'times' => $times,
				'status' => (int)$status,
				'description' => stripslashes($content),
				'updatetime'=>time(),
				'config' =>$config
		);
		$update=dbc ( DBCMax )->update ( 'activity_project' )->data ( $rebate )->where ( 'id=' . $id )->done ();
		if($update){
			$this->Messager ( "操作成功", '?mod=activity&code=plist' );
		}else{
			$this->Messager ( "操作成功", '?mod=activity&code=plist' );
		}
	}
	function DeleteProject() {
		$this->CheckAdminPrivs ( 'activity' );
		extract ( $this->Get );
		$this->DatabaseHandler->SetTable ( table ( 'activity_project' ) );
		$result = $this->DatabaseHandler->Delete ( '', 'id=' . intval ( $id ) );
		if($result){
			$this->Messager ( "删除成功", '?mod=activity&code=plist' );
		}else{
			$this->Messager ( "删除失败", '?mod=activity&code=plist' );
		}
	}
	
	//静态页面配置=========================================
	function ajaxUpdate(){
		$enabled = get ( 'enabled', 'int' );
		$psize = get ( 'psize', 'int' );
		logic ( 'enabled' )->update ("activity",array('key'=>$enabled));
		if($psize > 0){
			logic ( 'enabled' )->update ("activity_size",array('key'=>$psize));
		}
		echo 'ok';
	}
	
	function StaticList(){
		$this->CheckAdminPrivs ( 'activity' );
		extract ( $this->Get );
		extract ( $this->Post );
		$keyword = post ( 'keyword', 'txt' );
		$action='admin.php?mod=activity&code=StaticList';
		if($keyword){
			$filter =' (id like "%'.$keyword.'%" or title like "%'.$keyword.'%")';
		}else{
			$filter=1;
		}
		$list = logic ( 'activity' )->GetListStatic (-1, $filter);
		
		$enabled = logic ( 'enabled' )->get_one ("activity");
		$activity_size = logic ( 'enabled' )->get_one ("activity_size");
		
		include (handler ( 'template' )->file ( '@admin/static_web_list' ));
	}
	
	function AddStatic(){
		$action = '?mod=activity&code=DoaddStatic';
		include handler ( 'template' )->file ( '@admin/static_web_mgr' );
	}
	function DoaddStatic(){
		$this->CheckAdminPrivs ( 'activity' );
		extract ( $this->Get );
		extract ( $this->Post );
		$tmpe=array(post('field1'));
		array_push($tmpe, post('field2'));
		array_push($tmpe, post('field3'));
		$config = serialize($tmpe);//序列化
		if ($pic != '')
		{
			$pic = substr($pic, 0, -1);
		}
		$rebate = array (
				'title' => $title,
				'config' => $config,
				'left_btn' => $left_btn,
				'right_btn' => $right_btn,
				'bottom_btn' => $bottom_btn,
				'bottom_url' => $bottom_url,
				'description' => stripslashes($content),
				'pic'=>$pic,
				'status' => (int)$status
		);
		$id = logic ( 'activity' )->AddStatic ( $rebate );
		if($id){
			$this->Messager ( "操作成功", '?mod=activity&code=StaticList' );
		}else{
			$this->Messager ( "操作失败", '?mod=activity&code=StaticList' );
		}
	}
	function EditStatic() {
		$this->CheckAdminPrivs ( 'activity' );
		extract ( $this->Get );
		extract ( $this->Post );
		$action = '?mod=activity&code=DoeditStatic';
		$list = logic ( 'activity' )->GetOneStatic ($id);
		$list['config'] = unserialize($list['config']);//反序列化
		
		$config=array();
		foreach ($list['config'][0] as $key => $value ) {
			$temp=array();
			foreach ( $list['config'] as $i => $val ) {
				$one=array($list['config'][$i][$key]);
				$temp = array_merge($temp, $one);
			}
			array_push($config, $temp);
		} 
		if(!empty($list['pic']))
		{
			$list['pic']=explode(',', $list['pic']);
		}
		$title=$list['title'];
		include (handler ( 'template' )->file ( '@admin/static_web_mgr' ));
	}
	function DoeditStatic() {
		$this->CheckAdminPrivs ( 'activity' );
		extract ( $this->Get );
		extract ( $this->Post );
		$tmpe=array(post('field1'));
		array_push($tmpe, post('field2'));
		array_push($tmpe, post('field3'));
		$config = serialize($tmpe);//序列化
		$rebate = array (
				'title' => $title,
				'config' => $config,
				'left_btn' => $left_btn,
				'right_btn' => $right_btn,
				'bottom_btn' => $bottom_btn,
				'bottom_url' => $bottom_url,
				'description' => stripslashes($content),
				'status' => (int)$status,
				'updatetime' =>time()
		);
		$update=dbc ( DBCMax )->update ( 'static_web' )->data ( $rebate )->where ( 'id=' . $id )->done ();
		if($update){
			$this->Messager ( "操作成功", '?mod=activity&code=StaticList' );
		}else{
			$this->Messager ( "操作成功", '?mod=activity&code=StaticList' );
		}
	}
	function DeleteStatic() {
		$this->CheckAdminPrivs ( 'activity' );
		extract ( $this->Get );
		$this->DatabaseHandler->SetTable ( table ( 'static_web' ) );
		$result = $this->DatabaseHandler->Delete ( '', 'id=' . intval ( $id ) );
		if($result){
			$this->Messager ( "删除成功", '?mod=activity&code=StaticList' );
		}else{
			$this->Messager ( "删除失败", '?mod=activity&code=StaticList' );
		}
	}
	function Addimage_web()
	{
		$this->CheckAdminPrivs('activity','ajax');
		$aid = get('aid', 'int');
		$id = get('id', 'int');
		$list = logic('activity')->GetOneStatic($aid);
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
		logic('activity')->Update_Static($aid, array('pic'=>$new));
		exit('ok');
	}
	function Delimage_web()
	{
		$this->CheckAdminPrivs('activity');
		$aid = get('aid', 'int');
		$id = get('id', 'int');
		$list = logic('activity')->GetOneStatic($aid);
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
			logic('activity')->Update_Static($aid, array('pic'=>$new));
		}
		exit('ok');
	}
}
?>