<?php

/**
 * 模块：玩广告问题分类管理
 * @copyright (C)2011 Cenwor Inc.
 * @author wzn
 * @package module
 * @name adplay_qt.mod.php
 * @version 1.0
 */
class ModuleObject extends MasterObject {
	function ModuleObject($config) {
		$this->MasterObject ( $config );
		$runCode = Load::moduleCode ( $this );
		$this->$runCode ();
	}
	function Main() {
		$this->CheckAdminPrivs ( 'adplay_qt' );
		header ( 'Location: ?mod=adplay_qt&code=vlist' );
	}
	function vList() {
		$this->CheckAdminPrivs ( 'adplay_qt' );
		$where = array ();
		$list = logic ( 'adplay' )->GetList_QT ( $where );
		include handler ( 'template' )->file ( '@admin/adplay_qt_list' );
	}
	function Add() {
		$this->CheckAdminPrivs ( 'adplay_qt' );
		$p = array ();
		include handler ( 'template' )->file ( '@admin/adplay_qt_mgr' );
	}
	function Edit() {
		$this->CheckAdminPrivs ( 'adplay_qt' );
		$id = get ( 'id', 'int' );
		$p = logic ( 'adplay' )->GetOne_QT ( $id );
		include handler ( 'template' )->file ( '@admin/adplay_qt_mgr' );
	}
	function Save() {
		$this->CheckAdminPrivs ( 'adplay_qt' );
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
			$id = logic ( 'adplay' )->Add_QT ( $data );
		} else {
			logic ( 'adplay' )->Update_QT ( $id, $data );
		}
		$this->Messager ( '分类数据更新完成！', '?mod=adplay_qt&code=vlist' );
	}
	function Del() {
		$this->CheckAdminPrivs ( 'adplay_qt' );
		$id = get ( 'id', 'int' );
		if ($id == 0) {
			$this->Messager ( '无效的数据！', '?mod=adplay_qt&code=vlist' );
			return;
		}
		logic ( 'adplay' )->Del_QT ( $id );
		$this->Messager ( '分类删除完成！', '?mod=adplay_qt&code=vlist' );
	}
}

?>