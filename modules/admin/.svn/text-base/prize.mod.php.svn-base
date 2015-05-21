<?php

/**
 * 模块：抽奖管理
 * @package module
 * @name prize.mod.php
 * @version 1.0
 */
class ModuleObject extends MasterObject {
	function ModuleObject($config) {
		$this->MasterObject ( $config );
		$runCode = Load::moduleCode ( $this );
		$this->$runCode ();
	}
	function Main() {
		$this->CheckAdminPrivs ( 'prize' );
		header ( 'Location: ?mod=product&code=vlist' );
	}
	/**
	 * 抽奖管理列表页面
	 */
	function vList() {
		$this->CheckAdminPrivs ( 'prize' );
		logic ( 'product' )->Maintain ();
		$list = logic ( 'product' )->GetList ( - 1, null, 'type="prize"' );
		include handler ( 'template' )->file ( '@admin/product_prize_list' );
	}
	/**
	 * 某一条抽奖信息的管理页面
	 */
	function Mgr() {
		$this->CheckAdminPrivs ( 'prize' );
		$pid = get ( 'pid', 'int' );
		$product = logic ( 'product' )->GetOne ( $pid );
		$pzwin = logic ( 'prize' )->PrizeWIN ( $pid );
		if ($pzwin) {
// 			$uu = "";
// 			$arr = explode(";",$pzwin[uid]);
// 			foreach ($arr as $uid){
// 				if(is_numeric($uid)){
// 					$uu .= user ( $uid )->get ( 'name' ).",";
// 				}
// 			}
// 			$uu = substr($uu, 0,strlen($uu) - 1);
// 			$uu .= "(&&&)";
			
			
			//中奖人短信信息
// 			$smsContent = user ( $pzwin ['uid'] )->get ( 'name' ) . '，您好，我们很高兴的通知您，您在我们网站参与的活动“' . $product ['flag'] . '”开奖了，恭喜您赢得大奖，详情请登录：' . ini ( 'settings.site_url' );
			//未中奖人短信信息
			$smsContent = 'xxx，您好，我们很高兴的通知您，您在我们网站参与的活动“' . $product ['flag'] . '”开奖了，恭喜您赢得大奖，详情请登录：' . ini ( 'settings.site_url' );
			//未中奖人短信信息
			$broadcastContent = '您好，您在我们网站参与的活动“' . $product ['flag'] . '”已经开奖了，赶快来看看吧！详情请登录：' . ini ( 'settings.site_url' );
		}
		include handler ( 'template' )->file ( '@admin/prize_mgr' );
	}
	/**
	 * 查询中奖人
	 */
	function Ajax_query() {
		$this->CheckAdminPrivs ( 'prize', 'ajax' );
		$pid = get ( 'pid', 'int' );
		//中奖的号码
		//123;234;123
		$ticket = get ( 'ticket', 'txt' );
		//截取中奖号码，组织成in格式
		$arr = explode(";",$ticket);
		$a1 = "(";
		$in = "";
		foreach ($arr as $a){
			if(is_numeric($a)){
				$a1 .= $a.",";
			}
		}
		$in .= substr($a1, 0,strlen($a1) - 1);
		$in .= ")";
		$sql = 'SELECT * FROM ' . table ( 'prize_ticket' ).' WHERE pid = ' . $pid . ' AND number in ' . $in;
		$prizes = dbc ( DBCMax )->query ( $sql )->done ();
		
		if (! $prizes) {
			echo ('查询失败，找不到相关用户！<br/>失败原因：<br/>1，您输入的中奖号码不在可控范围内；<br/>2，您设置了虚拟购买人数，此中奖号码为虚拟占位号码；<br/>：：：不过，您仍然可以公开此中奖号码');
		} else {
			foreach ($prizes as $prize){
				$u = user ( $prize ['uid'] );
				$phoneDATA = logic ( 'prize' )->GetPhone ( 'uid', $u->get ( 'id' ) );
				echo '用户名：' . $u->get ( 'name' ) . '<br/>手机：' . $phoneDATA ['phone'] . '<br/>邮箱：' . $u->get ( 'email' ) . '<br/>QQ：' . $u->get ( 'qq' ) . '';
				echo '<br/>';
				echo '抽奖号备注：' . $prize ['remark'];
				echo '<br/>';
				echo '------------------------</br>';
			}
		}
		echo '<br/>';
		exit ( '<input type="button" onclick="public_prize_win()" value="公开中奖号码" />' );
	}
	/**
	 * 公开中奖号码
	 */
	function Ajax_publish() {
		$this->CheckAdminPrivs ( 'prize', 'ajax' );
		$pid = get ( 'pid', 'int' );
		//查看该抽奖是否已经被公开
		$pzwin = logic ( 'prize' )->PrizeWIN ( $pid );
		if ($pzwin) {
			exit ( '此抽奖活动已经公开过中奖号码，无法再次提交！' );
		}
		
		$ticket = get ( 'ticket', 'txt' );
		//存储中奖号和手机号，这个ticket已经是3;4这个格式
		$r = logic ( 'prize' )->PrizePUB ( $pid, $ticket );
		exit ( $r === true ? 'ok' : $r );
	}
	/**
	 * 短信通知中奖信息
	 */
	function Ajax_notify() {
		$this->CheckAdminPrivs ( 'prize', 'ajax' );
		$phones = get ( 'phone', 'txt' );
		$uids = get ( 'uid', 'txt' );
		$content = post ( 'content', 'txt' );
		//电话
		$arr1 = explode(";",$phones);
		//用户ID
		$arr2 = explode(";",$uids);
		for($i =0;$i<count($arr2);$i++) {
			$name = user ( $arr2[$i] )->get ( 'name' );
			$content = str_replace("xxx", $name, $content);
			logic ( 'push' )->addi ( 'sms', $arr1[$i], array (
				'content' => $content
			) );
		}
		exit ( 'ok' );
	}
	/**
	 * 短信通知未中奖信息
	 */
	function Ajax_broadcast() {
		$this->CheckAdminPrivs ( 'prize', 'ajax' );
		$pid = get ( 'pid', 'int' );
		$excUID = get ( 'excuid', 'txt' );
		$content = post ( 'content', 'txt' );
		
		$arr = explode(";",$excUID);
		$a1 = "(";
		$in = "";
		foreach ($arr as $a){
			if(is_numeric($a)){
				$a1 .= $a.",";
			}
		}
		$in .= substr($a1, 0,strlen($a1) - 1);
		$in .= ")";
		
		$phones = logic ( 'prize' )->GetPhoneListMy ( $pid, ';', $in );
		
		logic ( 'push' )->add ( 'sms', $phones, array (
			'content' => $content
			)
		 );
		exit ( 'ok' );
	}
	/**
	 * 查询该抽奖项目的所有抽奖号码
	 */
	function nums_list() {
		$this->CheckAdminPrivs ( 'prize' );
		$pid = get ( 'pid', 'int' );
		$prizes = logic ( 'prize' )->GetList ( $pid, false, false, 'number.DESC', true );
		include handler ( 'template' )->file ( '@admin/prize_nums_list' );
	}
}
?>