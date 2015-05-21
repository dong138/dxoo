<?php

/**
 * 模块：抽奖相关
 * @package module
 * @name prize.mod.php
 * @version 1.1
 */
class ModuleObject extends MasterObject {
	function ModuleObject($config) {
		$this->MasterObject ( $config );
		if (MEMBER_ID < 1) {
			if (get ( 'pid' )) {
				header ( 'Location: ' . rewrite ( '?view=' . get ( 'pid' ) ) );
				exit ();
			}
			$this->Messager ( __ ( '请先登录！' ), '?mod=myaccount&code=login' );
		}
		$runCode = Load::moduleCode ( $this );
		$this->$runCode ();
	}
	function Main() {
		header ( 'Location: .' );
	}
	/**
	 * 进入抽奖页面
	 */
	function Sign() {
		$pid = get ( 'pid', 'int' );
		//判断产品是否已经购买
		$product = logic ( 'product' )->BuysCheck ( $pid );
		if (isset ( $product ['false'] )) {
			$this->Messager ( $product ['false'] );
		}
		$prizes = logic ( 'prize' )->GetList ( $pid, user ()->get ( 'id' ), 1 );
		if ($prizes) {
			header ( 'Location: ' . rewrite ( '?mod=prize&code=view&pid=' . $pid ) );
			exit ();
		}
		$phone = logic ( 'prize' )->GetPhone (user ()->get ( 'id' ));
		$product = logic ( 'product' )->BuysCheck ( $pid, false );
		isset ( $product ['false'] ) && $this->Messager ( $product ['false'] );
		include handler ( 'template' )->file ( 'prize_sign' );
	}
	/**
	 * 1
	 * 点击直接抽奖的第一个处理
	 */
	function Iniz() {
		$pid = get ( 'pid', 'int' );
		//在订单表插入一个订单信息，并且生成一个ticket在prize_ticket表里
		$iz = logic ( 'prize' )->InizTicket ( $pid, user ()->get ( 'id' ) );
		if ($iz !== true) {
			$this->Messager ( $iz );
		}
		header ( 'Location: ' . rewrite ( '?mod=prize&code=view&pid=' . $pid ) );
	}
	/**
	 * 2
	 * 点击直接抽奖的第二个处理
	 */
	function View() {
		$pid = get ( 'pid', 'int' );
		$pid || exit ( '.O O. I need the Product-ID...' );
		//user ()->get ( 'id' )这句话是获取当前登录的用户
		//去prize_ticket表获取ticket
		$prizes = logic ( 'prize' )->GetList ( $pid, user ()->get ( 'id' ) );
		$product = logic ( 'product' )->GetOne ( $pid );
		$product || exit ( '> _ < Product-ID invaid' );
		$this->Title = $product ['name'];
		include handler ( 'template' )->file ( 'prize_view' );
	}
	function Ajax_S2Phone() {
		$phone = get ( 'phone', 'number' );
		if (strlen ( $phone ) != 11)
			exit ( '无效的手机号码！' );
		$r = logic ( 'prize' )->S2Phone ( $phone );
		exit ( $r === true ? 'ok' : $r );
	}
	function Ajax_Vfcode() {
		$phone = get ( 'phone', 'number' );
		if (strlen ( $phone ) != 11)
			exit ( '无效的手机号码！' );
		$vcode = get ( 'vcode', 'number' );
		if (strlen ( $vcode ) != 5)
			exit ( '无效的验证码！' );
		$r = logic ( 'prize' )->Vfcode ( $phone, $vcode );
		exit ( $r === true ? 'ok' : $r );
	}
	
}

?>