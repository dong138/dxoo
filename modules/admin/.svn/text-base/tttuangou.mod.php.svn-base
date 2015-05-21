<?php
/**
 * @package php
 * @name tttuangou.mod.php
 * @date 2014-09-01 17:24:23
 */
class ModuleObject extends MasterObject {
	var $city;
	function ModuleObject($config) {
		$this->MasterObject ( $config );
		Load::logic ( 'product' );
		$this->ProductLogic = new ProductLogic ();
		Load::logic ( 'pay' );
		$this->PayLogic = new PayLogic ();
		Load::logic ( 'me' );
		$this->MeLogic = new MeLogic ();
		Load::logic ( 'order' );
		$this->OrderLogic = new OrderLogic ();
		$this->config = $config;
		$this->ID = ( int ) ($this->Post ['id'] ? $this->Post ['id'] : $this->Get ['id']);
		Load::moduleCode ( $this );
		$this->Execute ();
	}
	function Execute() {
		switch ($this->Code) {
			case 'varshow' :
				$this->Varshow ();
				break;
			case 'varedit' :
				$this->Varedit ();
				break;
			case 'addcity' :
				$this->Addcity ();
				break;
			case 'doaddcity' :
				$this->Doaddcity ();
				break;
			case 'doeditcity' :
				$this->Doeditcity ();
				break;
			case 'deletecity' :
				$this->Deletecity ();
				break;
			case 'editcity' :
				$this->Editcity ();
				break;
            case 'cityhot' :
				$this->CityHot ();
				break;
			case 'city' :
				$this->Listcity ();
				break;

			case 'deleteseller' :
				$this->Deleteseller ();
				break;
			case 'addseller' :
				$this->Addseller ();
				break;
			case 'doaddseller' :
				$this->Doaddseller ();
				break;
			case 'editseller' :
				$this->Editseller ();
				break;
			case 'doeditseller' :
				$this->Doeditseller ();
				break;
			case 'addmap' :
				$this->Addmap ();
				break;
			case 'mainseller' :
				$this->Mainseller ();
				break;
			case 'replyquestion' :
				$this->Replyquestion ();
				break;
			case 'doreplyquestion' :
				$this->Doreplyquestion ();
				break;
			case 'deletequestion' :
				$this->Deletequestion ();
				break;
			case 'mainquestion' :
				$this->Mainquestion ();
				break;
			case 'usermsg' :
				$this->Usermsg ();
				break;
			case 'readusermsg' :
				$this->Readusermsg ();
				break;
			case 'deleteusermsg' :
				$this->Deleteusermsg ();
				break;
			case 'clear' :
				$this->DataClear ();
				break;
			case 'dataapi' :
				$this->DataAPI ();
				break;
			case 'imagethumb' :
				$this->ImageThumbRebuild ();
				break;
			case 'indexnav' :
				$this->IndexNaviManager ();
				break;
			case 'doindexnav' :
				$this->doIndexNaviManager ();
				break;
			case 'sitelogo' :
				$this->SiteLogoManager ();
				break;
			case 'dositelogo' :
				$this->doSiteLogoManager ();
				break;
			case 'shareconfig' :
				$this->ShareConfig ();
				break;
			case 'mainagenter' :
				$this->Mainagenter ();
				break;
			case 'ckAgent' :
				$this->ckAgent ();
				break;
			case 'addagenter' :
				$this->Addagenter ();
				break;
			case 'editagenter' :
				$this->Editagenter ();
				break;
			case 'IsCheckUsername' :
				$this->IsCheckUsername ();
				break;
			case 'seller_orders' :
				$this->seller_orders ();
				break;
			case 'seller_statistic' :
				$this->seller_statistic ();
				break;
			case 'sellerDetailList' :
				$this->sellerDetailList ();
				break;
		 	case 'listAgenter' :
				$this->listAgenter ();
				break;
		}
	}
	function Varshow() {
		$this->CheckAdminPrivs ( 'shopset' );
		$action = '?mod=tttuangou&code=varedit';
		$product = ConfigHandler::get ( 'product' );
		include (CONFIG_PATH . 'settings.php');
		$product ['default_imgwidth'] = $config ['thumbwidth'];
		$product ['default_imgheight'] = $config ['thumbheight'];
		include (handler ( 'template' )->file ( "@admin/tttuangou_var" ));
	}
	function Varedit() {
		$this->CheckAdminPrivs ( 'shopset' );
		extract ( $this->Post );
		$set = ConfigHandler::get ( 'product' );
		$set ['default_successnum'] = $default_successnum;
		$set ['default_virtualnum'] = $default_virtualnum;
		$set ['default_oncemax'] = $default_oncemax;
		$set ['default_oncemin'] = $default_oncemin;
		$set ['default_payfinder'] = $default_payfinder;
		$set ['default_emailcheck'] = $default_emailcheck;
		$set ['default_googlemapkey'] = $default_googlemapkey;
		ConfigHandler::set ( 'product', $set );
		$this->Messager ( "操作成功", '?mod=tttuangou&code=varshow' );
	}
    function Cityhot()
    {
        extract($_GET);
//        var_dump( $isHot );

        $isHot = $isHot=='true'?0:1;
        $sql = dbc ( DBCMax )->update ( 'city' )->where ( array ('cityid' => $cityid))
                ->data(array('isHot'=>$isHot))->done();
        var_dump( $sql);
    }

	function Listcity() {
		$this->CheckAdminPrivs ( 'city' );
		$city_list = logic ( 'misc' )->CityList ( 0, true );
		$settings = ConfigHandler::get ( 'product' );
		$default_city_id = $settings ['default_city'];
		include (handler ( 'template' )->file ( "@admin/tttuangou_listcity" ));
	}
	function Addcity() {
		$this->CheckAdminPrivs ( 'city' );
		$action = "admin.php?mod=tttuangou&code=doaddcity";
		include (handler ( 'template' )->file ( "@admin/tttuangou_addcity" ));
	}
	function Doaddcity() {
		$this->CheckAdminPrivs ( 'city' );
		if ($this->Post ['cityname'] == '')
			$this->Messager ( "操作失败，地区名称不可以为空" );
		$ary = array (
				'cityname' => $this->Post ['cityname'],
				'shorthand' => $this->Post ['shorthand'],
				'display' => $this->Post ['display'] 
		);
		$this->DatabaseHandler->SetTable ( TABLE_PREFIX . 'tttuangou_city' );
		$result = $this->DatabaseHandler->Insert ( $ary );
		$this->Messager ( "操作成功", "?mod=tttuangou&code=city" );
	}
	function Editcity() {
		$this->CheckAdminPrivs ( 'city' );
		$action = "admin.php?mod=tttuangou&code=doeditcity";
		$city = logic ( 'misc' )->CityList ( $this->Get ['id'], true );
		$city = $city [0];
		$settings = ConfigHandler::get ( 'product' );
		$default_city_id = $settings ['default_city'];
		include (handler ( 'template' )->file ( "@admin/tttuangou_editcity" ));
	}
	function Doeditcity() {
		$this->CheckAdminPrivs ( 'city' );
		$display = $this->Post ['display'] == '' ? 0 : 1;
		$ary = array (
				'cityname' => $this->Post ['cityname'],
				'shorthand' => $this->Post ['shorthand'],
				'display' => $display 
		);
		$cityid = $this->Post ['cityid'];
		$this->DatabaseHandler->SetTable ( TABLE_PREFIX . 'tttuangou_city' );
		$result = $this->DatabaseHandler->Update ( $ary, 'cityid=' . $cityid );
		if ('1' == $this->Post ['default_city']) {
			$settings = ConfigHandler::get ( 'product' );
			$settings ['default_city'] = $this->Post ['cityid'];
			ConfigHandler::set ( 'product', $settings );
		}
		$this->Messager ( "操作成功", "?mod=tttuangou&code=city" );
	}
	function Deletecity() {
		$this->CheckAdminPrivs ( 'city' );
		$id = $this->Get ['id'];
		$this->DatabaseHandler->SetTable ( TABLE_PREFIX . 'tttuangou_city' );
		$result = $this->DatabaseHandler->Delete ( '', 'cityid=' . $id );
		$this->Messager ( $return ? $return : "操作成功", "?mod=tttuangou&code=city" );
	}
	function Mainagenter() {
		$this->CheckAdminPrivs ( 'seller' );
		$city = logic ( 'misc' )->CityList ();
		$newcity = array ();
		for($i = 0; $i < count ( $city ); $i ++) {
			$newcity [$city [$i] ['cityid']] = $city [$i] ['cityname'];
		}
		
		$keyword = $this->Post ['keyword'] == '' ? $this->Get ['keyword'] : $this->Post ['keyword'];
		$area = $this->Post ['city'] == '' ? $this->Get ['city'] : $this->Post ['city'];
		$addsql = ' where 1 ';
		if ($keyword != '' || ($area != 'false' && $area != '')) {
			if ($keyword != '')
				$addsql .= ' and sellername like \'%' . $keyword . '%\' ';
			if ($area != '' && $area != 'false')
				$addsql .= ' and area = ' . $area . ' ';
		}
		$enabled = isset ( $_POST ['enabled'] ) ? $this->Post ['enabled'] : '';
		if ($enabled != '') {
			$addsql .= " AND enabled = '" . $enabled . "'";
		}
		$addsql .= " and is_agent=1";
		$page = intval ( $_REQUEST ['page'] ) == false ? 1 : intval ( $_REQUEST ['page'] );
		$sql = 'SELECT count(*) from ' . TABLE_PREFIX . 'tttuangou_seller ' . $addsql;
		$query = $this->DatabaseHandler->Query ( $sql );
		$num = $query->GetRow ();
		$num = $num ['count(*)'];
// 		if ($num == 0 && $addsql != '')
// 			$this->Messager ( "无法找到匹配的代理商", "?mod=tttuangou&code=mainagenter" );
		$pagenum = 10;
		$page_arr = page ( $num, $pagenum, $query_link, $_config );
		
		$sql = 'SELECT * from ' . TABLE_PREFIX . 'tttuangou_seller ' . $addsql . ' ORDER BY id DESC' . ' limit ' . ($page - 1) * $pagenum . ',' . $pagenum;
		$query = $this->DatabaseHandler->Query ( $sql );
		$seller = $query->GetAll ();
		foreach ( $seller as $i => $one ) {
			$seller [$i] ['money'] *= 1;
		}
		include (handler ( 'template' )->file ( '@admin/tttuangou_agenter' ));
	}
	function Addagenter() {
		$this->CheckAdminPrivs ( 'agenter' );
		$city = logic ( 'misc' )->CityList ();
		$action = '?mod=tttuangou&code=doaddseller';
		$rebate = logic ( 'rebate' )->Get_Rebate_setting ( true );
		$sell_pre = $rebate ['sell_pre'];
		include (handler ( 'template' )->file ( '@admin/tttuangou_agenter_mgr' ));
	}
	function ckAgent() {
		$name = get ( 'n' );
		$id = get ( 'id' );
		$ag = logic ( 'seller' )->searchAgent ( null, $name );
		if ($ag) {
			if (id != null) {
				// 修改
				if ($ag ['id'] == $id) {
					exit ( "ok" );
				} else {
					exit ( "no" );
				}
			}
			// 添加
			exit ( "no" );
		} else {
			exit ( "ok" );
		}
	}
	
	function Editagenter() {
		$this->CheckAdminPrivs ( 'agenter' );
		extract ( $this->Get );
		extract ( $this->Post );
		$city = logic ( 'misc' )->CityList ();
		$action = '?mod=tttuangou&code=doeditseller';
		$sql = 'select * from ' . TABLE_PREFIX . 'tttuangou_seller where userid = ' . $id;
		$query = $this->DatabaseHandler->Query ( $sql );
		$seller = $query->GetRow ();
		$rebate = logic ( 'rebate' )->Get_Rebate_setting ( true );
		$profit_id = $seller ['profit_id'];
		$profit_pre = $seller ['profit_pre'];
		include (handler ( 'template' )->file ( '@admin/tttuangou_agenter_mgr' ));
	}
	function listAgenter() {
		$id = get ( 'id', int );
		$list = array (
				array (
						'id' => 0,
						'sellername' => '请选择代理商',
						'shorthand' => '__#__'
				)
		);
		$sql = 'SELECT * from ' . TABLE_PREFIX . 'tttuangou_seller where is_agent=1';
		$query = $this->DatabaseHandler->Query ( $sql );
		$agenter = $query->GetAll ();
		$list = array_merge ( $list, $agenter );
		foreach ( $list as $i => $one ) {
			$sel = '';
			if ($one ['id'] == $id) {
				$sel = ' selected="selected"';
			}
			echo '<option value="' . $one ['id'] . '"' . $sel . '>' . $one ['sellername'] . '</option>';
		}
		exit ();
	}
	function Mainseller() {
		$this->CheckAdminPrivs ( 'seller' );
		$city = logic ( 'misc' )->CityList ();
		$sql = 'select * from ystttuangou_seller where is_agent = 1';
		$query = $this->DatabaseHandler->Query ( $sql );
		$agents = $query->GetAll ();
		
		$newcity = array ();
		for($i = 0; $i < count ( $city ); $i ++) {
			$newcity [$city [$i] ['cityid']] = $city [$i] ['cityname'];
		}
		
		$keyword = $this->Post ['keyword'] == '' ? $this->Get ['keyword'] : $this->Post ['keyword'];
		$area = $this->Post ['city'] == '' ? $this->Get ['city'] : $this->Post ['city'];
		$agent = $this->Post ['agent'] == '' ? $this->Get ['agent'] : $this->Post ['agent'];
		$addsql = ' where 1 ';
		if ($keyword != '' || ($area != 'false' && $area != '') || ($agent != 'false' && $agent != '')) {
			if ($keyword != '')
				$addsql .= ' and sellername like \'%' . $keyword . '%\' ';
			if ($area != '' && $area != 'false')
				$addsql .= ' and area = ' . $area . ' ';
			if ($agent != '' && $agent != 'false')
				$addsql .= ' and agentid = ' . $agent . ' ';
		}
		$enabled = isset ( $_POST ['enabled'] ) ? $this->Post ['enabled'] : '';
		if ($enabled != '') {
			$addsql .= " AND enabled = '" . $enabled . "'";
		}
		$addsql .=" and is_agent=0 ";
		$page = intval ( $_REQUEST ['page'] ) == false ? 1 : intval ( $_REQUEST ['page'] );
		$sql = 'SELECT count(*) from ' . TABLE_PREFIX . 'tttuangou_seller ' . $addsql;
		$query = $this->DatabaseHandler->Query ( $sql );
		$num = $query->GetRow ();
		$num = $num ['count(*)'];
		if ($num == 0 && $addsql != '')
			$this->Messager ( "无法找到匹配的商家", "?mod=tttuangou&code=mainseller" );
		$pagenum = 10;
		$page_arr = page ( $num, $pagenum, $query_link, $_config );
		
		$sql = 'SELECT * from ' . TABLE_PREFIX . 'tttuangou_seller ' . $addsql . ' ORDER BY id DESC' . ' limit ' . ($page - 1) * $pagenum . ',' . $pagenum;
		$query = $this->DatabaseHandler->Query ( $sql );
		$seller = $query->GetAll ();
		foreach ( $seller as $i => $one ) {
			$seller [$i] ['money'] *= 1;
		}
		include (handler ( 'template' )->file ( '@admin/tttuangou_seller' ));
	}
	function Addmap() {
		$this->CheckAdminPrivs ( 'seller' );
		extract ( $this->Get );
		extract ( $this->Post );
		$x = '11728000';
		$y = '4320000';
		$z = 4;
		if ($id != '') {
			$xyz = explode ( ',', $id );
			$x = $xyz [0];
			$y = $xyz [1];
			$z = $xyz [2];
		} elseif ($city != '' && $city != '全国') {
			$x = 0;
			$y = 0;
			$z = 8;
		}
		include (handler ( 'template' )->file ( '@admin/tttuangou_googlemap' ));
	}
	function Addseller() {
		$this->CheckAdminPrivs ( 'seller' );
		$city = logic ( 'misc' )->CityList ();
		$action = '?mod=tttuangou&code=doaddseller';
		$rebate = logic ( 'rebate' )->Get_Rebate_setting ( true );
		$sell_pre = $rebate ['sell_pre'];
		include (handler ( 'template' )->file ( '@admin/tttuangou_seller_mgr' ));
	}
	function Doaddseller() {
		$this->CheckAdminPrivs ( 'seller' );
		extract ( $this->Get );
		extract ( $this->Post );
		if ($agenter==0 && ($username == '' || $sellername == '' || $sellerphone == '' || $selleraddress == '')) {
			$this->Messager ( "请将参数都填写完整!", - 1 );
		}
		if ($agenter==0){
			$rr = logic ( 'seller' )->Register ( $username, $password );
			$rr ['error'] && $this->Messager ( $rr ['result'], - 1 );
			$uid = $rr ['result'];
		}
		$rebate = array (
				'profit_id' => $profit_id,
				'profit_pre' => $profit_pre,
				'home_uid' => $home_uid,
				'enabled' => $enabled,
				'city_place_region' => ( int ) $__cplace_region,
				'city_place_street' => ( int ) $__cplace_street,
				'imgs' => trim ( $imgs, ',' ),
				'price_avg' => $price_avg,
				'category' => $__catalog_subclass > 0 ? ( int ) $__catalog_subclass : ( int ) $__catalog_topclass,
				'trade_time' => $trade_time,
				'content' => $content,
				'is_agent'=> $is_agent,
				'firstpinyin'=> $firstpinyin,
				'allpinyin'=> $allpinyin
		);
		$sid = logic ( 'seller' )->Add ( $area, $uid, $sellername, $sellerphone, $selleraddress, $sellerurl, $map, $rebate, $agenter );
		if (! $sid)
			$this->Messager ( '添加失败！请重试', - 1 );
		logic ( 'gps' )->product_linker ( $sid, $map );
		if($is_agent==1){
			$this->Messager ( "操作成功", '?mod=tttuangou&code=mainagenter' );
		}else{
			$this->Messager ( "操作成功", '?mod=tttuangou&code=mainseller' );
		}
	}
	function Editseller() {
		$this->CheckAdminPrivs ( 'seller' );
		extract ( $this->Get );
		extract ( $this->Post );
		$city = logic ( 'misc' )->CityList ();
		$action = '?mod=tttuangou&code=doeditseller';
		$sql = 'select * from ' . TABLE_PREFIX . 'tttuangou_seller where id = ' . $id;
		$query = $this->DatabaseHandler->Query ( $sql );
		$seller = $query->GetRow ();
		$rebate = logic ( 'rebate' )->Get_Rebate_setting ( true );
		$profit_id = $seller ['profit_id'];
		$profit_pre = $seller ['profit_pre'];
		include (handler ( 'template' )->file ( '@admin/tttuangou_seller_mgr' ));
	}
	function Doeditseller() {
		$this->CheckAdminPrivs ( 'seller' );
		extract ( $this->Post );
		$id = ( int ) $id;
		$ary = array (
				'sellername' => strip_tags ( $sellername ),
				'sellerphone' => strip_tags ( $sellerphone ),
				'selleraddress' => strip_tags ( $selleraddress ),
				'sellerurl' => strip_tags ( $sellerurl ),
				'area' => $area,
				'time' => time (),
				'agentid' => $agenter,
				'profit_id' => $profit_id,
				'profit_pre' => $profit_pre,
				'home_uid' => $home_uid,
				'enabled' => $enabled,
				'city_place_region' => ( int ) $__cplace_region,
				'city_place_street' => ( int ) $__cplace_street,
				'price_avg' => $price_avg,
				'category' => $__catalog_subclass > 0 ? ( int ) $__catalog_subclass : ( int ) $__catalog_topclass,
				'trade_time' => strip_tags ( $trade_time ),
				'content' => $content,
				'is_agent'=> $is_agent,
				'firstpinyin'=> $firstpinyin,
				'allpinyin'=> $allpinyin
		);
		if ($map != ''){
			$ary ['sellermap'] = $map;
		}
		dbc ( DBCMax )->update ( 'seller' )->data ( $ary )->where ( 'id=' . $id )->done ();
		logic ( 'seller' )->setmembertype ( $id, $enabled );
		logic ( 'gps' )->product_linker ( $id, $map );
		if($is_agent==1){
			$this->Messager ( "操作成功", '?mod=tttuangou&code=mainagenter' );
		}else{
			$this->Messager ( "操作成功", '?mod=tttuangou&code=mainseller' );
		}
	}
	function Deleteseller() {
		$this->CheckAdminPrivs ( 'seller' );
		extract ( $this->Get );
		$is_agent = dbc ( DBCMax )->select ( 'seller' )->where ( "agentid = ".$id )->done ();
		if ($is_agent) {
			$this->Messager ( "删除失败，请先删除代理商下的商家！", '?mod=tttuangou&code=mainagenter' );
		}
		$sinfo = dbc ( DBCMax )->query ( 'select * from ' . table ( 'seller' ) . ' where id=' . $id )->limit ( 1 )->done ();
		if (! $sinfo) {
			$this->Messager ( "删除失败，该商家已经不存在！", '?mod=tttuangou&code=mainseller' );
		}
		$sql = 'select * from ' . TABLE_PREFIX . 'tttuangou_product where sellerid = ' . intval ( $id );
		$query = $this->DatabaseHandler->Query ( $sql );
		$user = $query->GetAll ();
		if (! empty ( $user ))
			$this->Messager ( "您必须先删除该商家的产品！才能删除该商家", '?mod=tttuangou&code=mainseller' );
		if ($sinfo ['userid'] != 1) {
			dbc ( DBCMax )->update ( 'members' )->data ( array (
					'privs' => '',
					'role_id' => '0',
					'role_type' => 'normal' 
			) )->where ( 'uid=' . $sinfo ['userid'] )->done ();
		}
		$this->DatabaseHandler->SetTable ( TABLE_PREFIX . 'tttuangou_seller' );
		$result = $this->DatabaseHandler->Delete ( '', 'id=' . intval ( $id ) );
		dbc ( DBCMax )->delete ( 'members' )->where ( 'uid=' . $sinfo ['userid'] )->done (); // 删除用户表
		if($sinfo['is_agent']==1){
			$this->Messager ( "删除成功", '?mod=tttuangou&code=mainagenter' );
		}else{
			$this->Messager ( "删除失败", '?mod=tttuangou&code=mainseller' );
		}
	}
	function Mainquestion() {
		$this->CheckAdminPrivs ( 'question' );
		$page = intval ( $_REQUEST ['page'] ) == false ? 1 : intval ( $_REQUEST ['page'] );
		$sql = 'SELECT count(*) FROM ' . TABLE_PREFIX . 'tttuangou_question';
		$query = $this->DatabaseHandler->Query ( $sql );
		$num = $query->GetRow ();
		$num = $num ['count(*)'];
		$pagenum = 30;
		$page_arr = page ( $num, $pagenum, $query_link, $_config );
		
		$sql = 'select * from ' . TABLE_PREFIX . 'tttuangou_question order by time desc limit ' . ($page - 1) * $pagenum . ',' . $pagenum;
		$query = $this->DatabaseHandler->Query ( $sql );
		$question = $query->GetAll ();
		include (handler ( 'template' )->file ( "@admin/tttuangou_question" ));
	}
	function Replyquestion() {
		$this->CheckAdminPrivs ( 'question' );
		extract ( $this->Get );
		extract ( $this->Post );
		$sql = 'select * from ' . TABLE_PREFIX . 'tttuangou_question where id = ' . $id;
		$query = $this->DatabaseHandler->Query ( $sql );
		$action = '?mod=tttuangou&code=doreplyquestion';
		$reply = $query->GetROW ();
		if ($reply == '') {
			$this->Messager ( "找不到该提问!" );
		}
		;
		include (handler ( 'template' )->file ( "@admin/tttuangou_reply" ));
	}
	function Doreplyquestion() {
		$this->CheckAdminPrivs ( 'question' );
		extract ( $this->Get );
		extract ( $this->Post );
		$id = intval ( $id );
		if ($id == false)
			$this->Messager ( "参数错误!" );
		$ary = array (
				'reply' => $reply 
		);
		$this->DatabaseHandler->SetTable ( TABLE_PREFIX . 'tttuangou_question' );
		$result = $this->DatabaseHandler->Update ( $ary, 'id=' . $id );
		$ask = dbc ( DBCMax )->select ( 'question' )->where ( 'id=' . $id )->limit ( 1 )->done ();
		$ask ['reply'] = $reply;
		notify ( $ask ['userid'], 'list.ask.reply', $ask );
		$this->Messager ( "操作成功", "?mod=tttuangou&code=mainquestion" );
		exit ();
	}
	function Deletequestion() {
		$this->CheckAdminPrivs ( 'question' );
		$id = intval ( $this->Get ['id'] );
		$this->DatabaseHandler->SetTable ( TABLE_PREFIX . 'tttuangou_question' );
		$result = $this->DatabaseHandler->Delete ( '', 'id=' . $id );
		$this->Messager ( $return ? $return : "操作成功", "?mod=tttuangou&code=mainquestion" );
	}
	function Usermsg() {
		$this->CheckAdminPrivs ( 'usermsg' );
		$page = intval ( $_REQUEST ['page'] ) == false ? 1 : intval ( $_REQUEST ['page'] );
		$sql = 'SELECT count(*) FROM ' . TABLE_PREFIX . 'tttuangou_usermsg';
		$query = $this->DatabaseHandler->Query ( $sql );
		$num = $query->GetRow ();
		$num = $num ['count(*)'];
		$pagenum = 15;
		$page_arr = page ( $num, $pagenum, $query_link, $_config );
		$sql = 'select `id`,`name`,`time`,`type`,`readed` FROM ' . TABLE_PREFIX . 'tttuangou_usermsg order by `time` desc limit ' . ($page - 1) * $pagenum . ',' . $pagenum;
		$query = $this->DatabaseHandler->Query ( $sql );
		$usermsg = $query->GetAll ();
		include (handler ( 'template' )->file ( "@admin/tttuangou_usermsg" ));
	}
	function Readusermsg() {
		$this->CheckAdminPrivs ( 'usermsg' );
		$sql = 'select * from ' . TABLE_PREFIX . 'tttuangou_usermsg where `id` = ' . intval ( $this->Get ['id'] );
		$query = $this->DatabaseHandler->Query ( $sql );
		$msg = $query->GetRow ();
		if ($msg ['readed'] == 0) {
			$ary = array (
					'readed' => 1 
			);
			$this->DatabaseHandler->SetTable ( TABLE_PREFIX . 'tttuangou_usermsg' );
			$result = $this->DatabaseHandler->Update ( $ary, 'id=' . $msg ['id'] );
		}
		if ($msg == '')
			$this->Messager ( "该信息不存在!" );
		include (handler ( 'template' )->file ( "@admin/tttuangou_readusermsg" ));
	}
	function Deleteusermsg() {
		$this->CheckAdminPrivs ( 'usermsg' );
		extract ( $this->Get );
		$this->DatabaseHandler->SetTable ( TABLE_PREFIX . 'tttuangou_usermsg' );
		$result = $this->DatabaseHandler->Delete ( '', 'id=' . intval ( $id ) );
		$this->Messager ( "操作成功", '?mod=tttuangou&code=usermsg' );
	}
	function DataClear() {
		$this->CheckAdminPrivs ( 'dataclear' );
		if (! isset ( $_GET ['confirm'] ) || $_GET ['confirm'] == '') {
			$action = '?mod=tttuangou&code=clear&confirm=true';
			if (file_exists ( DATA_PATH . 'data.clear.lock' )) {
				$clear_locked = true;
			} else {
				$clear_locked = false;
			}
			include (handler ( 'template' )->file ( '@admin/tttuangou_clear' ));
		} else {
			if (file_exists ( DATA_PATH . 'data.clear.lock' )) {
				$this->Messager ( '初始化功能已锁定，操作失败！' );
				return;
			}
			$dataMap = array (
					'tuan' => array (
							'tttuangou_product',
							'tttuangou_order',
							'tttuangou_order_clog',
							'tttuangou_paylog',
							'tttuangou_uploads',
							'tttuangou_ticket',
							'tttuangou_express',
							'tttuangou_finder',
							'tttuangou_seller' 
					),
					'market' => array (
							'tttuangou_subscribe' 
					),
					'log' => array (
							'system_failedlogins',
							'system_log',
							'system_robot_log',
							'task_log' 
					),
					'mail' => array (
							'tttuangou_push_queue',
							'tttuangou_push_log',
							'tttuangou_push_template' 
					),
					'talk' => array (
							'tttuangou_question',
							'tttuangou_usermsg' 
					) 
			);
			foreach ( $_POST as $key => $val ) {
				if (substr ( $key, 0, 4 ) == 'data') {
					$mid = substr ( $key, 5 );
					if ('' != $dataMap [$mid]) {
						foreach ( $dataMap [$mid] as $i => $tableName ) {
							$sql = 'TRUNCATE TABLE  `' . TABLE_PREFIX . '' . $tableName . '`';
							$this->DatabaseHandler->Query ( $sql );
						}
						if ('tuan' == $mid) {
							$this->__clear_upload_image ();
						}
					}
				}
			}
			file_put_contents ( DATA_PATH . 'data.clear.lock', date ( 'Y-m-d H:i:s', time () ) );
			$this->Messager ( '初始化完成！' );
		}
	}
	function __clear_upload_image() {
		Load::lib ( 'io' );
		IoHandler::ClearDir ( UPLOAD_PATH );
	}
	function DataAPI() {
		$this->CheckAdminPrivs ( 'dataapi' );
		global $rewriteHandler;
		include_once INCLUDE_PATH . 'rewrite.php';
		$url_pre = '/?mod=apiz&code=js';
		if ($rewriteHandler) {
			$url_pre = $rewriteHandler->formatURL ( $url_pre );
		}
		$script_url = $this->Config ['site_url'] . $url_pre;
		if ($this->OPC == 'demo') {
			include handler ( 'template' )->file ( '@admin/tttuangou_data_api_demo' );
		} else {
			include (handler ( 'template' )->file ( '@admin/tttuangou_data_api' ));
		}
	}
	function ImageThumbRebuild() {
		Load::lib ( 'io' );
		$o_dirs = IoHandler::ReadDir ( IMAGE_PATH . 'product/' );
		$dirs = array ();
		foreach ( $o_dirs as $i => $dir ) {
			if (preg_match ( '/product\/\d{4}-\d{2}-\d{2}/', $dir )) {
				$dirs [] = $dir;
			}
		}
		$thumbwidth = $this->Config ['thumbwidth'];
		$thumbheight = $this->Config ['thumbheight'];
		$op = $_GET ['op'];
		if ($op == 'run') {
			$od = $_GET ['od'];
			$dir = $dirs [$od];
			$files = IoHandler::ReadDir ( $dir );
			foreach ( $files as $i => $src_file ) {
				$dst_file = str_replace ( '/product/', '/product/s-', $src_file );
				resize_image ( $src_file, $dst_file, $thumbwidth, $thumbheight );
			}
			echo '更新了目录[ ' . $dir . ' ]，有[ <b>' . count ( $files ) . '</b> ]张缩略图被生成！';
			return;
		}
		$cronLength = count ( $dirs );
		include (handler ( 'template' )->file ( '@admin/tttuangou_imagethumb_rebuild' ));
	}
	function IndexNaviManager() {
		$this->CheckAdminPrivs ( 'navset' );
		$action = '?mod=tttuangou&code=doindexnav&op=modify';
		$navs = ConfigHandler::get ( 'nav' );
		include (handler ( 'template' )->file ( '@admin/tttuangou_list_nav' ));
	}
	function doIndexNaviManager() {
		$this->CheckAdminPrivs ( 'navset' );
		$op = $this->Get ['op'];
		if ('modify' == $op) {
			$list = $this->Post;
			$order = $list ['order'];
			foreach ( $order as $i => $oid ) {
				if ($oid != '') {
					$sort [$oid] = $i;
				}
			}
			ksort ( $sort );
			foreach ( $sort as $oid => $i ) {
				$one = array ();
				$one ['order'] = $list ['order'] [$i];
				$one ['name'] = $list ['name'] [$i];
				$one ['url'] = $list ['url'] [$i];
				$one ['title'] = $list ['title'] [$i];
				$one ['target'] = $list ['target'] [$i];
				$set [] = $one;
			}
			ConfigHandler::set ( 'nav', $set );
			$this->Messager ( '保存成功！' );
		}
	}
	function SiteLogoManager() {
		$this->CheckAdminPrivs ( 'sitelogo' );
		$TPL_DIR = ROOT_PATH . $this->Config ['template_root_path'];
		$logos = array ();
		$logos [1] = array (
				'title' => '默认风格',
				'url' => $TPL_DIR . 'default/images/logo.png' 
		);
		$logos [] = array (
				'title' => '新产品邮件推广',
				'url' => $TPL_DIR . 'html/push/mail/logo.gif' 
		);
		$logos [] = array (
				'title' => TUANGOU_STR . '指南页面“' . TUANGOU_STR . '券示例”',
				'url' => $TPL_DIR . 'default/images/buy_yhq.png' 
		);
		include (handler ( 'template' )->file ( '@admin/tttuangou_site_logo' ));
	}
	function doSiteLogoManager() {
		$this->CheckAdminPrivs ( 'sitelogo' );
		$op = $this->Get ['op'];
		if ($op == 'save') {
			if (is_array ( $_FILES ['uploads'] ['name'] )) {
				$FILES_O = $_FILES;
				$_FILES = array ();
				$loopc = count ( $FILES_O ['uploads'] ['name'] );
				for($i = 0; $i < $loopc; $i ++) {
					if ($FILES_O ['uploads'] ['name'] [$i] != '') {
						break;
					}
				}
			} else {
				$this->Messager ( '出错了！' );
			}
			$_FILES ['uploads'] ['name'] = $FILES_O ['uploads'] ['name'] [$i];
			$_FILES ['uploads'] ['type'] = $FILES_O ['uploads'] ['type'] [$i];
			$_FILES ['uploads'] ['tmp_name'] = $FILES_O ['uploads'] ['tmp_name'] [$i];
			$_FILES ['uploads'] ['error'] = $FILES_O ['uploads'] ['error'] [$i];
			$_FILES ['uploads'] ['size'] = $FILES_O ['uploads'] ['size'] [$i];
			if ('' == $_FILES ['uploads'] ['name']) {
				$this->Messager ( '请选择要上传的图片！' );
			}
			$default_type = array (
					'jpg',
					'pic',
					'png',
					'jpeg',
					'bmp',
					'gif' 
			);
			$imgary = explode ( '.', $_FILES ['uploads'] ['name'] );
			if (! in_array ( strtolower ( $imgary [count ( $imgary ) - 1] ), $default_type )) {
				$this->Messager ( '不允许上传的图片格式！' );
			}
			$full_path = urldecode ( $this->Get ['path'] );
			$fp_ary = explode ( '/', $full_path );
			$file = $fp_ary [count ( $fp_ary ) - 1];
			$dir = '';
			for($i = 0; $i < count ( $fp_ary ) - 1; $i ++) {
				if ($fp_ary [$i] != '.') {
					$dir .= $fp_ary [$i] . '/';
				}
			}
			$files = logic ( 'upload' )->Save ( 'uploads', $dir . $file );
			if ($files ['error']) {
				$this->Messager ( $files ['msg'] );
			} else {
				$this->Messager ( '保存成功！' );
			}
		}
	}
	/**
	 * *****验证用户名是否存在*******
	 */
	function IsCheckUsername() {
		$val = get ( 'val', 'txt' );
		if (logic ( 'seller' )->IsMember ( $val ) == 'yes')
			exit ( "no" );
		else
			exit ( "ok" );
	}
	function ShareConfig() {
		$this->CheckAdminPrivs ( 'share' );
		$op = $this->Get ['op'];
		if ($op == 'modify') {
			$list = $this->Post;
			$order = $list ['order'];
			foreach ( $order as $i => $oid ) {
				if ($oid != '') {
					$sort [$oid] = $i;
				}
			}
			ksort ( $sort );
			foreach ( $sort as $oid => $i ) {
				$flag = $list ['flag'] [$i];
				$one = array ();
				$one ['order'] = $list ['order'] [$i];
				$one ['name'] = $list ['name'] [$i];
				$one ['display'] = (isset ( $list ['display'] [$flag] ) && $list ['display'] [$flag] == 'on') ? 'yes' : 'no';
				$set [$flag] = $one;
			}
			$bshare = ini ( 'share.~@bshare' );
			$bshare_POST = post ( 'bshare' );
			$bshare ['uuid'] = $bshare_POST ['uuid'];
			$set ['~@bshare'] = $bshare;
			ini ( 'share', $set );
			$this->Messager ( '保存成功！' );
		}
		$listAll = array (
				'link',
				'qzone',
				'kaixin001',
				'renren',
				'douban',
				'tsina',
				'bai',
				'gmail',
				'delicious',
				'digg',
				'yahoo',
				'google',
				'facebook',
				'twitter',
				'baiduhi',
				'blogbus',
				'clipboard',
				'qqmb',
				'qqxiaoyou',
				'xianguo' 
		);
		$action = '?mod=tttuangou&code=shareconfig&op=modify';
		$shares = ConfigHandler::get ( 'share' );
		foreach ( $listAll as $i => $flag ) {
			if (! array_key_exists ( $flag, $shares )) {
				$shares [$flag] = array (
						'order' => '',
						'name' => '',
						'display' => 'no' 
				);
			}
		}
		if (isset ( $shares ['~@bshare'] )) {
			$bshare = $shares ['~@bshare'];
			unset ( $shares ['~@bshare'] );
		}
		include (handler ( 'template' )->file ( '@admin/tttuangou_list_share' ));
	}
	
	/**
	 */
	function seller_orders() {
		$this->CheckAdminPrivs ( 'sellerorders' );
		$city = logic ( 'misc' )->CityList ();
		$newcity = array ();
		for($i = 0; $i < count ( $city ); $i ++) {
			$newcity [$city [$i] ['cityid']] = $city [$i] ['cityname'];
		}
		
		$keyword = isset ( $_POST ['keyword']) ? $this->Post ['keyword'] : $this->Get ['keyword'];
		$addsql = '';
		if ($keyword != null || $keyword != '') {
			$addsql .= ' and sellername like \'%' . $keyword . '%\' ';
		}
		$ordproc = isset ( $_POST ['ordproc'] ) ? $this->Post ['ordproc'] : $this->Get ['ordproc'];
		
		
		if ($ordproc != null && $ordproc != '') {
			$addsql .= " AND b.process = '" . $ordproc . "'";
		}
		
		$begintime = $this->Post ['iscp_timev_begintime'] == '' ? $this->Get ['iscp_timev_begintime'] : $this->Post ['iscp_timev_begintime'];
		$finishtime = $this->Post ['iscp_timev_finishtime'] == '' ? $this->Get ['iscp_timev_finishtime'] : $this->Post ['iscp_timev_finishtime'];
		if($begintime!=null || $begintime!='')
		{
			$addsql .=" AND b.buytime >= '" . strtotime ( $begintime) . "'";
		}
		if($finishtime!=null || $finishtime!='')
		{
			$addsql .=" AND b.buytime <= '" . (strtotime($finishtime) + 86399) . "'";
		}
		
		$page = intval ( $_REQUEST ['page'] ) == false ? 1 : intval ( $_REQUEST ['page'] );
		
		
		$sql ='SELECT ';
		$sql .='a.sellername,';
		$sql .='a.sellerurl, ';
		$sql .='b.productprice,';
		$sql .='b.totalprice,';
		$sql .='b.paymoney,';
		$sql .='b.buytime,';
		$sql .='b.process,';
		$sql .='d.`name` as dName,';
		$sql .='c.`name` as cName,';
		$sql .='c.city,';
		$sql .='d.enabled ';
		$sql .='FROM ';
		$sql .='ystttuangou_seller AS a ,';
		$sql .='ystttuangou_order AS b ,';
		$sql .='ystttuangou_product AS c ,';
		$sql .='ystttuangou_payment AS d ';
		$sql .=' WHERE 1=1 ';
		$sql .= $addsql;
		$sql .=' AND a.id = c.sellerid ';
		$sql .=' AND b.productid = c.id ';
		$sql .=' AND b.paytype = d.id ';
		//$sql .='GROUP BY ';
		//$sql .='a.id';
		
				
		$sqlc = 'SELECT count(*) from (';
		$sqlc .=$sql.") as e";
		
		//echo $sqlc;
		
		$query = $this->DatabaseHandler->Query ( $sqlc );
		$num = $query->GetRow ();
		$num = $num ['count(*)'];
		if ($num == 0 && $addsql != '')
			$this->Messager ( "无法找到匹配的商家", "?mod=tttuangou&code=seller_orders" );
		$pagenum = 10;
		$page_arr = page ( $num, $pagenum, $query_link, $_config );
		
		$sqla="SELECT e.* FROM ( ";
		$sqla.=$sql;
		$sqla.= " order by b.buytime desc limit " . ($page - 1) * $pagenum . ',' . $pagenum." ) as e";
		$query = $this->DatabaseHandler->Query ( $sqla );
		$seller = $query->GetAll ();
		foreach ( $seller as $i => $one ) {
			$seller [$i] ['money'] *= 1;
		}
		include (handler ( 'template' )->file ( '@admin/seller_orders' ));
	}
	
	
	/**
	 ******** 商家结算统计***************
	 */
	function seller_statistic() {
		$order_process = array (
				'' => '全部数据',
				'__CREATE__' => '创建订单',
				'__PAY_YET__' => '已经付款',
				'WAIT_BUYER_PAY' => '等待付款',
				'WAIT_SELLER_SEND_GOODS' => '等待发货',
				'WAIT_BUYER_CONFIRM_GOODS' => '等待收货',
				'TRADE_FINISHED' => '交易完成' 
		);
		
		$action = '?mod=tttuangou&code=seller_statistic';
		$begintime = post ( 'iscp_timev_begintime', 'txt' ) == '' ? $this->Get ['iscp_timev_begintime'] : $this->Post ['iscp_timev_begintime'];
		$endtime = post ( 'iscp_timev_finishtime', 'txt' ) == '' ? $this->Get ['iscp_timev_finishtime'] : $this->Post ['iscp_timev_finishtime'];
		$keyword = post ( 'keyword', 'txt' ) == '' ? $this->Get ['keyword'] : $this->Post ['keyword'];
		$order_state = post ( 'iscp_frc', 'txt' ) == '' ? $this->Get ['iscp_frc'] : $this->Post ['iscp_frc'];
		
		// 分页
		$page = intval ( $_REQUEST ['page'] ) == false ? 1 : intval ( $_REQUEST ['page'] );
		$sql = logic ( 'seller' )->searchsellerCount ( $order_state );
		$query = $this->DatabaseHandler->Query ( $sql );
		$num = $query->GetRow ();
		$num = $num ['count(*)'];
		if ($num == 0 && $addsql != '')
			$this->Messager ( "无法找到匹配的商家", "?mod=tttuangou&code=mainseller" );
		$pagenum = 10;
		$page_arr = page ( $num, $pagenum, $query_link, $_config );
		
		$sql = logic ( 'seller' )->searchseller ( $begintime, $endtime, $keyword, $order_state );
		$sql .= ' limit ' . ($page - 1) * $pagenum . ',' . $pagenum;
		$query = $this->DatabaseHandler->Query ( $sql );
		$sellerArray = $query->GetAll ();
		
		include (handler ( 'template' )->file ( '@admin/seller_pay' ));
	}
}