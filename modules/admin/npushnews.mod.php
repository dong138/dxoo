<?php

/**
 * 模块：天数/次数管理
 * @package module
 * @name npushnews.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	public function city_list(){
		$city_list=logic('misc')->CityList(0, true);
		$settings = ConfigHandler::get('product');
		$default_city_id = $settings['default_city'];
		include handler('template')->file("@admin/ncity_list");
	}
	public function main()
	{
		$this->CheckAdminPrivs('npushnews');
		extract ( $this->Get );
		extract ( $this->Post );
		$keyword = post ( 'keyword', 'txt' );
		$cityid = get ( 'cityid', 'int' );
		if($keyword){
			$filter =' (id like "%'.$keyword.'%"  or name like "%'.$keyword.'%") ';
		}else{
			$filter=1;
		}
		$cityid && $filter.=' AND cityid='.$cityid;
		
		$list = logic('npushnews')->get_list(0,$filter);

		//获取模块
		$list_model=logic('npushnews')->get_model();

// 		require (CONFIG_PATH . 'admin_left_menu.php');
// 		//查找模型名称
// 		foreach ( $list as $key => $value ) {
// 			foreach ( $menu_list as $_key => $_menu ) {
// 				foreach ( $_menu ['sub_menu_list'] as $_sub_key => $_sub_menu ) {
// 					if($_sub_menu['priv']==$value['modname']){
// 						$list[$key]['modtitle']=$_sub_menu['title'];
// 					}
// 				}
// 			}
// 		}
		
		include handler('template')->file('@admin/npushnews_list');
	}
	
	public function create()
	{
		$cityid = get ( 'cityid', 'int' );
		$this->CheckAdminPrivs('npushnews');
		include handler('template')->file('@admin/npushnews_mgr');
	}
	
	public function modify()
	{
		$this->CheckAdminPrivs('npushnews');
		$id = get('id', 'int');
		$npushnews = logic('npushnews')->get_one($id);
		include handler('template')->file('@admin/npushnews_mgr');
	}
	
	public function delete()
	{
		$this->CheckAdminPrivs('npushnews');
		$id = get('id', 'int');
		$results=logic('npushnews')->delete($id);
		if ($results){
			$this->Messager('删除成功！', -1);
		}else{
			$this->Messager('删除失败！,请先删除改目录下的数据！', -1);
		}
	}
	
	public function save()
	{
		$this->CheckAdminPrivs('npushnews');
		extract ( $this->Get );
		extract ( $this->Post );
		
		$rebate = array (
				'name' => $name,
				'num'=>$num,
				'cityid'=>$cityid,
				'status'=>(int)$status
		);
		$id = post('id', 'int');
		if ($id)
		{
			$rebate = array_merge($rebate, array('updatetime'=>time()));
			logic('npushnews')->update($id,$rebate);
		}
		else
		{
			$rebate = array_merge($rebate, array('addtime'=>time()));
			logic('npushnews')->create($rebate);
		}
		$this->Messager('保存成功！', '?mod=npushnews&cityid='.$cityid);
	}
	
// 	function getnpushnews(){
// 		require (CONFIG_PATH . 'admin_left_menu.php');
// 		$npushnews=array();
// 		foreach ( $menu_list as $_key => $_menu ) {
// 			foreach ( $_menu ['sub_menu_list'] as $_sub_key => $_sub_menu ) {
// 				if($_sub_menu['is_npushnews']){
// 					array_push($npushnews,$_sub_menu);
// 				}
// 			}
// 		}
// 		return $npushnews;
// 	}
	
	//************************模块和时间段*******************************
	function list_times(){
		$numid = get('numid', 'int');
		$modelid=get('modelid', 'int');
		$list = logic('npushnews')->get_times_list($modelid,$numid);
		//获取类型
		$list_type=logic('npushnews')->get_type();
		include handler('template')->file('@admin/ntimes_list');
	}
	
	public function times_create()
	{
		$numid = get('numid', 'int');
		$modelid = get('modelid', 'int');
		$this->CheckAdminPrivs('pushnews');
		include handler('template')->file('@admin/npushtime_mgr');
	}
	
	public function times_modify()
	{
		$this->CheckAdminPrivs('npushnews');
		$id = get('id', 'int');
		$npushnews = logic('npushnews')->get_times_one($id);
		include handler('template')->file('@admin/npushtime_mgr');
	}
	
	public function times_save()
	{
		$this->CheckAdminPrivs('npushnews');
		extract ( $this->Get );
		extract ( $this->Post );
	
		$rebate = array (
				'name' => $name,
				'modelid'=>$modelid,
				'numid'=>$numid,
				'begintime'=>$begintime,
				'endtime'=>$endtime,
				'status'=>(int)$status,
		);
		$id = post('id', 'int');
		if ($id)
		{
			$rebate = array_merge($rebate, array('updatetime'=>time()));
			logic('npushnews')->times_update($id,$rebate);
		}
		else
		{
			$rebate = array_merge($rebate, array('addtime'=>time()));
			logic('npushnews')->times_create($rebate);
		}
		$this->Messager('保存成功！', '?mod=npushnews&code=list_times&numid='.$numid.'&modelid='.$modelid);
	}
	
	public function times_delete()
	{
		$this->CheckAdminPrivs('npushnews');
		$id = get('id', 'int');
		$results=logic('npushnews')->times_delete($id);
		if ($results){
			$this->Messager('删除成功！', -1);
		}else{
			$this->Messager('删除失败！,请先删除改目录下的数据！', -1);
		}
	}
	
	function relevance(){
		$timesid=get('timesid', 'int');
		$typeid=get('typeid', 'int');
		$key=get('key');
		
		if($typeid){
			$type=logic('npushnews')->get_type_one($typeid);
			
			if($key && $type){
				//key存在则是查询用于设置产品为推送
				$list=logic('npushnews')->common_list($key,$type);
			}else{
				//key不存在则查询relevance关联产品信息推送
				$list=logic('npushnews')->relevance_list($timesid,$typeid);
			}
		}
		$isSort = $key ? false :true;
		include handler('template')->file('@admin/nrelevance_mgr');
	}
	
	function ajaxupdate(){
		$this->CheckAdminPrivs('pushnews','ajax');
		$commonid = get('commonid', 'int');
		$timesid=get('timesid', 'int');
		$typeid=get('typeid', 'int');
		$type=get('type', 'int');
		
		$commonid || $commonid = 0;
		$timesid || $timesid = 0;
		$typeid || $typeid = 0;
		
		$data = array(
				'timesid'=>$timesid,
				'typeid'=>$typeid,
				'commonid'=>$commonid
		);
		if($type){//添加、删除
			logic('npushnews')->relevance_delete($data);
			echo  "0" ;
		}else{
			if($commonid){
				$data = array_merge($data, array('commonid'=>$commonid,'upstime'=>time()));
					
				$add = logic('npushnews')->relevance_add ( $data );
				echo $add >0 ? "0" : "1";//0成功1失败
			}
		}
	}
	function order()
	{
		$this->CheckAdminPrivs('npushnews','ajax');
		$id = get('id', 'int');
		$order = get('order', 'int');
		$rebate=array('order'=>$order);
		logic('npushnews')->relevance_update($id,$rebate);
		echo 'ok';
	}
}

?>