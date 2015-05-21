<?php

/**
 * 模块：动态数据显示
 * @package module
 * @name list.mod.php
 * @version 1.2
 */

class ModuleObject extends MasterObject
{
	var $city;
	var $cityname;
	var $ProductLogic;
	var $PayLogic;
	var $MeLogic;
	var $OrderLogic;
	function ModuleObject( $config )
	{
		$this->MasterObject($config); 		Load::logic('product');
		$this->ProductLogic = new ProductLogic();
		Load::logic('pay');
		$this->PayLogic = new PayLogic();
		Load::logic('me');
		$this->MeLogic = new MeLogic();
		Load::logic('order');
		$this->OrderLogic = new OrderLogic();
		$this->ID = ( int )($this->Post['id'] ? $this->Post['id'] : $this->Get['id']);
		$this->CacheConfig = ConfigHandler::get('cache'); 		$this->ShowConfig = ConfigHandler::get('show'); 		$runCode = Load::moduleCode($this, $this->Code);
		$this->$runCode();
	}
	function Main()
	{
		header('Location: '.rewrite('?mod=list&code=ask'));
	}
	function Ask()
	{
		$this->Title = __('在线问答');
		$action = '?mod=list&code=doquestion';
		include ($this->TemplateHandler->Template("ask"));
	}
	function Doquestion()
	{
		$question = post('question', 'txt');
		if ( MEMBER_ID < 1 ) $this->Messager(__('您必须先登录才能发表您的提问！'));
		if ( $question == '' ) $this->Messager(__('问题不可以为空哦！'));
		if ( $a = filter($question) ) $this->Messager($a);
		$ary = array(
			userid => MEMBER_ID, username => MEMBER_NAME, content => $question, time => time()
		);
		$this->DatabaseHandler->SetTable(TABLE_PREFIX . 'tttuangou_question');
		$result = $this->DatabaseHandler->Insert($ary);
		$ary['time'] = date('Y-m-d H:i:s', $ary['time']);
		notify(MEMBER_ID, 'list.ask.new', $ary);
		$this->Messager(__("提问成功，请等待管理员的回复！"), "?mod=list&code=ask");
		exit();
	}
	function Business()
	{ 	
		$this->Title = __('商务合作');
		$action = '?mod=index&code=doteamwork';
		$sql = 'select * from ' . table('catalog').' where id not in (25,26) and parent not in (25)';
		$catalog = dbc ( DBCMax )->query ( $sql )->done ();
		$isComments = true;
		include ($this->TemplateHandler->Template('business'));
	}
	function Doteamwork()
	{
		$this->__filter_post('name,phone,email,elsecontat,content,shopName,shopAddress,shopPhone,salesVolume,shopType');
		if ( $this->Post['name'] == '' || $this->Post['phone'] == '' || $this->Post['content'] == '' ) $this->Messager("缺少必要参数，请正确填写！");
		$ary = array(
			'name' => $this->Post['name'],
			'phone' => $this->Post['phone'],
			'elsecontat' => $this->Post['elsecontat'],
			'content' => $this->Post['content'],
			'shopName' => $this->Post['shopName'],
			'email' => $this->Post['email'],
			'shopAddress' => $this->Post['shopAddress'],
			'shopPhone' => $this->Post['shopPhone'],
			'salesVolume' => $this->Post['salesVolume'],
			'shopType' => $this->Post['shopType'],
			'time' => time(),
			'type' => 2,
			'readed' => 0
		);
		$this->MeLogic->UserMsg($ary);
		$this->Messager(__("我们已经记录下您的合作信息，我们将尽快给您回复！"), "?mod=list&code=business");
	}
	function Feedback()
	{ 		$this->Title = __('意见反馈');
		$action = '?mod=index&code=dofeedback';
		include ($this->TemplateHandler->Template('feedback'));
	}
	function Dofeedback()
	{
		$this->__filter_post('name,phone,elsecontat,content');
		if ( $this->Post['name'] == '' || $this->Post['phone'] == '' || $this->Post['content'] == '' ) $this->Messager("缺少必要参数，请正确填写！");
		$ary = array(
			'name' => $this->Post['name'],
			'phone' => $this->Post['phone'],
			'elsecontat' => $this->Post['elsecontat'],
			'content' => $this->Post['content'],
			'time' => time(),
			'type' => 1,
			'readed' => 0
		);
		$this->MeLogic->UserMsg($ary);
		$this->Messager(__("我们已经记录下您的反馈信息，感谢您对本站的支持！"), "?mod=list&code=feedback");
	}
	private function __filter_post($fields)
	{
		$list = explode(',', $fields);
		foreach ($list as $i => $fid)
		{
			$moyoCNT = &$this->Post[$fid];
			$moyoAFS = filter($moyoCNT);
			$moyoAFS && $this->Messager($moyoAFS);
		}
	}
	function Deals()
	{
		$this->Title = __('历史' . TUANGOU_STR);
		$product = logic('product')->GetList(logic('misc')->City('id'), PRO_ACV_No);
		include ($this->TemplateHandler->Template('deals'));
	}


   
	function Newdeals()
	{
		$this->Title = __('精彩' . TUANGOU_STR);
		$product = logic('product')->GetList(logic('misc')->City('id'), PRO_ACV_Yes);
		include ($this->TemplateHandler->Template('newdeals'));
	}


	function Sendemail()
	{
		extract($this->Post);
		if ( ! check_email($email) ) $this->Messager(__("邮箱地址有误！"));
		if ( isset($del) )
		{
			$this->MeLogic->mail($email, $city, 0);
		}
		else
		{
			$this->MeLogic->mail($email, $city, 1);
		}
		$this->Messager(__("操作成功！"), "?");
	}
	function Invite()
	{
		$this->Title = __('邀请有奖');
		if ( MEMBER_ID < 1 )
		{
			$this->Messager(__("请您先注册或登录！"), '?mod=myaccount&code=login');
		}
		$finder = $this->MeLogic->finderList(user()->get('id'));
		include ($this->TemplateHandler->Template("invite"));
	}

	function Ckticket()
	{
		$number = get('number', 'number');
		$this->Title = __('消费卷查询');
		$action = '?mod=list&code=dockticket';
		$sellerid = logic('seller')->U2SID(user()->get('id'));
		$seller_info = logic('seller')->GetOne(null,MEMBER_ID);
		$money = $seller_info['money'];
		$total_money = $seller_info['total_money'];
		$account_money = $seller_info['account_money'];
		$forbid_money = $seller_info['forbid_money'];
		include ($this->TemplateHandler->Template("tttuangou_ckticket"));
	}
	function Dockticket()
	{
		$number = get('number', 'number');
		is_numeric($number) || exit('<font color="red">编号不能为空或者包含其他字符！</font>');
		$do = get('do');
		if ( $do == 'check' )
		{
			$this->coupon_check($number);
		}
		elseif ( $do == 'getname' )
		{
			$this->coupon_getname($number);
		}
		else
		{
			if (MEMBER_ID < 1) {
				$this->Messager ( __ ( '请先登录！' ), '?mod=myaccount&code=login' );
			}
			$seller = logic ( 'seller' )->GetOne ( null,MEMBER_ID);
			$seller_id=$seller['id'];
			$this->coupon_used($number, get('password'), get('morecoupon'),$seller_id,0);
		}
	}

	
	private function coupon_check($number)
	{
		$ticket = logic('coupon')->TicketGet($number);
		if ($ticket)
		{
			if ( $ticket['status'] == TICK_STA_Unused )
			{
				$msg = '<font color="green">该' . TUANGOU_STR . '券可以使用</font>';
				if($ticket['morecoupons']){
					$msg .= '|||<table><tr><td colspan="2"><font color="blue">相关团购券(勾选则一同消费)</font></td></tr>';
					$msg .= '<tr><td>券号</td><td>密码</td></tr>';
					foreach($ticket['morecoupons'] as $k => $v){
						$msg .= '<tr><td><input type="checkbox" name="morecoupons" value="'.$v.'">&nbsp;'.$v.'</td><td>***</td></tr>';
					}
					$msg .= '</table>';
				}
			}
			elseif ( $ticket['status'] == TICK_STA_Used )
			{
				$msg = '<font color="blue">该' . TUANGOU_STR . '券已经被使用，消费时间：' . $ticket['usetime'] . '</font>';
			}
			elseif ( $ticket['status'] == TICK_STA_Invalid )
			{
				$msg = '<font color="red">该' . TUANGOU_STR . '券已失效！</font>';
			}
			else
			{
				$msg = '<font color="red">该' . TUANGOU_STR . '券已过期！</font>';
			}
			exit($msg);
		}
		else
		{
			exit('<font color="red">该' . TUANGOU_STR . '券不存在！</font>');
		}
		
	}
	
	private function coupon_getname($number)
	{
		$product = logic('coupon')->ProductGet($number);
		if ($product)
		{
			$as = '<br/>';
			if (isset($product['coupon']['attrs']) && $product['coupon']['attrs'])
			{
				foreach ($product['coupon']['attrs']['dsp'] as $attr)
				{
					$as .= $attr['name'].'<br/>';
				}
			}
			exit($product['flag'].' X <font color="red"><b>'.$product['coupon']['mutis'].'</b></font> 份'.$as);
		}
		else
		{
			exit('<font color="red">没有找到该产品！</font>');
		}
	}
	
	private function coupon_used($number, $password, $morecoupon = '',$sellerid,$clerkaid)
	{
		$result = logic('coupon')->appMakeUsed($number,$password,$morecoupon,$sellerid,$clerkaid);
		if ($result['error'])
		{
			switch ($result['errcode'])
			{
				case 'not-found' :
					exit('<font color="red">该' . TUANGOU_STR . '券不存在！</font>');
					break;
				case 'be-used' :
					exit('<font color="blue">该' . TUANGOU_STR . '券已经被使用，消费时间：' . $result['coupon']['usetime'] . '</font>');
					break;
				case 'be-overdue' :
					exit('<font color="red">该' . TUANGOU_STR . '券已过期！</font>');
					break;
				case 'be-invalid' :
					exit('<font color="red">该' . TUANGOU_STR . '券已失效！</font>');
					break;
				case 'access-denied' :
					exit('<font color="red">此' . TUANGOU_STR . '券不属于您的产品！</font>');
					break;
				case 'password-wrong' :
					exit('<font color="red">该' . TUANGOU_STR . '券的密码输入错误！</font>');
					break;
			}
		}
		else
		{
			if(isset($result['coupon']['number']) && isset($result['coupon']['password']) && $number == $result['coupon']['number']) {
				exit('<font color="green">已成功消费 </font><font color="red">1</font><font color="green"> 张' . TUANGOU_STR . '券：'.$number.'</font>|||success');
			} else {
				exit('<font color="green">已成功消费 </font><font color="red">'.count($result['coupon']).'</font><font color="green"> 张' . TUANGOU_STR . '券：'.implode(',',$result['coupon']).'</font>|||success');
			}
		}
	}
	
	function appCK(){
		$number = get('number','txt');
		$password = get('password','txt');
		$morecoupon = get('morecoupon','txt');
		$sellerid = get('sellerid','int');
		$clerkaid = get('clerkaid','int');
		$result = logic('coupon')->appMakeUsed($number,$password,$morecoupon,$sellerid,$clerkaid);
		$msg = "";
		$status = "";
		if ($result['error']){
			$status = "01";
			switch ($result['errcode']){
				case 'not-found' :
					$msg='该' . TUANGOU_STR . '券不存在！';
					break;
				case 'be-used' :
					$msg=$result['name'].';该' . TUANGOU_STR . '券已经被使用，消费时间：' . $result['coupon']['usetime'];
					break;
				case 'be-overdue' :
					$msg=$result['name'].';该' . TUANGOU_STR . '券已过期';
					break;
				case 'be-invalid' :
					$msg=$result['name'].';该' . TUANGOU_STR . '券已失效！';
					break;
				case 'access-denied' :
					$msg=$result['name'].';此' . TUANGOU_STR . '券不属于您的产品！';
					break;
				case 'password-wrong' :
					$msg=$result['name'].';该' . TUANGOU_STR . '券的密码输入错误！';
					break;
			}
		}else{
			$status = "00";
			if(isset($result['coupon']['number']) && isset($result['coupon']['password']) && $number == $result['coupon']['number']) {
				$msg=$result['name'].';已成功消费 1张' . TUANGOU_STR . '券：'.$number;
			} else {
				$msg=$result['name'].';已成功消费 '.count($result['coupon']).'张' . TUANGOU_STR . '券：'.implode(',',$result['coupon']);
			}
		}
		$str = json_encode ( array (
					'status' => $status,
					'msg' => $msg
			) );
		$aa = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
		exit($aa);
	}


}

?>