<?php

/**
 * 模块：玩广告问题分类管理
 * @copyright (C)2011 Cenwor Inc.
 * @author wzn
 * @package module
 * @name adplay_cl.mod.php
 * @version 1.0
 */
class ModuleObject extends MasterObject {
	function ModuleObject($config) {
		$this->MasterObject ( $config );
		$runCode = Load::moduleCode ( $this );
		$this->$runCode ();
	}
	function Main() {
		$this->CheckAdminPrivs ( 'adplay_cl' );
		header ( 'Location: ?mod=adplay_cl&code=vlist' );
	}
	function vList() {
		$this->CheckAdminPrivs ( 'adplay_cl' );
		$keyword =get('sstr');
		$where=1;
        if(isset($_GET['state'])){
            $state = get('state', 'int');
			is_numeric($state) && $where='state='.$state;
        }
        $where .= $keyword ? ' and (userid like "%'.$keyword.'%")' :"";
		$list = logic ( 'adplay' )->GetList_CL ( $where );
        if ($list) {
			foreach ( $list as $k => &$v ) {
				$v['addtime'] = date( 'Y-m-d H:i:s', $v['addtime'] );
				$v['dealtime'] = $v['dealtime'] != 0 ? date('Y-m-d H:i:s', $v['dealtime']) : '未处理';
                $v['state_name'] = logic('adplay')->GetStateName_CL($v['state']);
			}
		}
		include handler ( 'template' )->file ( '@admin/adplay_cl_list' );
	}
	function Add() {
		$this->CheckAdminPrivs ( 'adplay_cl' );
		$p = array ();
		include handler ( 'template' )->file ( '@admin/adplay_cl_mgr' );
	}
	function Edit() {
		$this->CheckAdminPrivs ( 'adplay_cl' );
		$id = get ( 'id', 'int' );
		$p = logic ( 'adplay' )->GetOne_CL ( $id );
		include handler ( 'template' )->file ( '@admin/adplay_cl_mgr' );
	}
	function Save() {
		$this->CheckAdminPrivs ( 'adplay_cl' );
		$data = array ();
		$data ['name'] = post ( 'name', 'txt' );
		$data ['order'] = post ( 'order', 'int' );
		foreach ( $data as $key => $name ) {
			if (is_numeric ( $data [$key] ))
				continue;
			if (! $data [$key]) {
				$this->Messager ( '【' . $name . '】不能为空！', - 1 );
			}
		}
		$id = post ( 'id', 'int' );
		if ($id == 0) {
			$id = logic ( 'adplay' )->Add_CL ( $data );
		} else {
			logic ( 'adplay' )->Update_CL ( $id, $data );
		}
		$this->Messager ( '分类数据更新完成！', '?mod=adplay_cl&code=vlist' );
	}
	function Del() {
		$this->CheckAdminPrivs ( 'adplay_cl' );
		$id = get ( 'id', 'int' );
		if ($id == 0) {
			$this->Messager ( '无效的数据！', '?mod=adplay_cl&code=vlist' );
			return;
		}
		logic ( 'adplay' )->Del_CL ( $id );
		$this->Messager ( '分类删除完成！', '?mod=adplay_cl&code=vlist' );
	}
	function Pass() {
		$this->CheckAdminPrivs ( 'adplay_cl' );
		$id = get ( 'id', 'int' );
		$type = get ( 'type', 'int' );
		if ($id == 0) {
			$this->Messager ( '无效的数据！', '?mod=adplay_cl&code=vlist' );
			return;
		}
		$credit = logic ( 'adplay' )->GetOne_CL ($id);
		
        $data['state']=1;
        $data['dealuser']=user()->get('id');
        $data['dealtime']=time();
        if ($type==2){
        	logic ( 'adplay' )->updateMember ($credit['userid'], $credit['value']);
        }
		logic ( 'adplay' )->Update_CL ($id, $data);
		$this->Messager ( '操作成功！', '?mod=adplay_cl&code=vlist' );
	}
	function UnPass() {
		$this->CheckAdminPrivs ( 'adplay_cl' );
		$id = get ( 'id', 'int' );
		if ($id == 0) {
			$this->Messager ( '无效的数据！', '?mod=adplay_cl&code=vlist' );
			return;
		}
        $data['remark']=post('remark');
        $data['state']=3;
        $data['dealuser']=user()->get('id');
        $data['dealtime']=time();
		logic ( 'adplay' )->Update_CL ($id, $data);
		$this->Messager ( '操作成功！', '?mod=adplay_cl&code=vlist' );
	}
}

?>