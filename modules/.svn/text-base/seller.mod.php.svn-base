<?php

/**
 * 模块：商家后台
 * @package module
 * @name seller.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
	private $uid = 0;
	private $sid = 0;
	
	private function iniz()
	{
		$this->uid = user()->get('id');
		if ($this->uid < 0)
		{
			$this->Messager('请先登录！', '?mod=myaccount&code=login');
		}
		$this->sid = logic('seller')->U2SID($this->uid);
		if ($this->sid < 0)
		{
			if($this->uid == 1){
				$this->Messager('请您先去后台，添加自己的商家信息！', 0);
			}else{
				$this->Messager('您不是商家，无法查看商家后台！', 0);
			}
		}else{
			$sellerinfo = dbc(DBCMax)->query('select * from '.table('seller').' where id='.$this->sid)->limit(1)->done();
			if($sellerinfo['enabled']=='false'){
				$this->Messager('您的商家身份未通过审核！', 'index.php');
			}
		}
	}
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	public function main()
	{
		$seller = logic('seller')->GetList(logic('misc')->City('id'), logic('catalog')->Filter(get('catalog'), 'seller'));
		$this->Title = "商家列表";
		include handler('template')->file('seller');
	}
	public function view()
	{
		$id = get('id', 'int');
		$seller = logic('seller')->GetOne($id);
		$this->Title = $seller['sellername'];
		include handler('template')->file('seller_view');
	}
	public function manage()
	{
		$this->iniz();
		header('Location: '.rewrite('?mod=seller&code=product&op=list'));
	}
	
	public function product_list()
	{
		$this->iniz();
		$filter = 'p.sellerid='.$this->sid;
		if(isset($_GET['prosta'])){
			$prosta = get('prosta', 'int');
			is_numeric($prosta) && $filter .= ' AND p.status='.$prosta;
		}
		if(isset($_GET['prodsp'])){
			$prodsp = get('prodsp', 'int');
			is_numeric($prodsp) && $filter .= ' AND p.display='.$prodsp;
		}
		$products = logic('product')->GetList(-1, null, $filter);
		logic('seller')->AVParser($products);
		$seller_info = logic('seller')->GetOne(null,MEMBER_ID);
		$money = $seller_info['money'];
		$total_money = $seller_info['total_money'];
		$account_money = $seller_info['account_money'];
		$forbid_money = $seller_info['forbid_money'];
		include handler('template')->file('seller_product_list');
	}
	
	/**
	 * 员工管理
	 */
	public function clerka_list()
	{
		$this->iniz();
		$seller_info = logic('seller')->GetOne(null,MEMBER_ID);
		$query = $this->DatabaseHandler->Query ( "select * from ".table('clerka')." where sellerid = ".$seller_info['id']);
		$clerkas = $query->GetAll ();
		$money = $seller_info['money'];
		$total_money = $seller_info['total_money'];
		$account_money = $seller_info['account_money'];
		$forbid_money = $seller_info['forbid_money'];
		include handler('template')->file('seller_clerka_list');
	}
	/**
	 * 员工添加
	 */
	public function clerka_add()
	{
		$this->iniz();
		$seller_info = logic('seller')->GetOne(null,MEMBER_ID);
		$money = $seller_info['money'];
		$total_money = $seller_info['total_money'];
		$account_money = $seller_info['account_money'];
		$forbid_money = $seller_info['forbid_money'];
		include handler('template')->file('seller_clerka_add');
	}
	/**
	 * 员工修改
	 */
	public function clerka_update()
	{
		$this->iniz();
		$seller_info = logic('seller')->GetOne(null,MEMBER_ID);
		$money = $seller_info['money'];
		$total_money = $seller_info['total_money'];
		$account_money = $seller_info['account_money'];
		$forbid_money = $seller_info['forbid_money'];
		$id = get('id','int');
		$query = $this->DatabaseHandler->Query ( "select * from ".table('clerka')." where id = ".$id);
		$clerka = $query->GetRow ();
		
		include handler('template')->file('seller_clerka_add');
	}
	
	public function clerka_dosave()
	{
		$this->iniz();
		$id = post('id','int');
		$name = post('name','txt');
		if(strlen($name) <= 0){
			$this->Messager('姓名不允许为空');
		}
		$age = post('age','txt');
		if($age < 10 || $age > 100){
			$this->Messager('年龄不合法');
		}
		$phone = post('phone','txt');
		if(!check_phone($phone)){
			$this->Messager('手机号不正确');
		}
		$username = post('username','txt');
		if(strlen($username) < 6 || strlen($username) > 12){
			$this->Messager('用户名6-12为');
		}
		$data = array(
			"number"=>post('number','txt'),
			"name"=>$name,
			"age"=>$age,
			"gender"=>post('gender','int'),
			"phone"=>$phone,
			"username"=>$username,
			"type"=>post('type','int')
		);
		if($id == 0){
			if(!$this->check_clerka(0,$username,$name,$data['number'],$phone,1)){
				$this->Messager('请检查姓名、用户名、员工编号是否重复');
			}
			$data['sellerid'] = $this->sid;
			//添加
			$pwd =  post('password','txt');
			if(strlen($pwd) < 6  || strlen($pwd) > 32){
				$this->Messager('密码不正确');
			}
			$data['password'] = md5($pwd);
			$this->DatabaseHandler->SetTable ( table('clerka') );
			$this->DatabaseHandler->Insert( $data);
			//插入
			$this->Messager('添加成功！', '?mod=seller&code=clerka&op=list');
		}else{
			if(!$this->check_clerka($id,$username,$name,$data['number'],$phone,2)){
				$this->Messager('请检查姓名、用户名、员工编号是否重复');
			}
			//修改
			$pwd =  post('password','txt');
			if(strlen($pwd) <= 0){
				
			}else if(strlen($pwd) < 6 || strlen($pwd) > 32){
				$this->Messager('密码不正确');
			}else{
				$data['password'] = md5($pwd);
			}
			$this->DatabaseHandler->SetTable ( table('clerka') );
			$this->DatabaseHandler->Update ( $data, 'id = ' .$id );
			$this->Messager('修改成功！', '?mod=seller&code=clerka&op=list');
		}
	}
	/**
	 * 员工删除
	 */
	public function clerka_delete()
	{
		$this->iniz();
		$id = get('id','int');
		$query = $this->DatabaseHandler->Query ("delete from ".table('clerka')." where id = ".$id);
		$this->Messager('删除成功！', '?mod=seller&code=clerka&op=list');
		include handler('template')->file('seller_clerka_list');
	}
	/**
	 * 检查员工用户名
	 */
	public function ckUn(){
		$id = get("id","int");
		$un = post("param");
		$where = " where username = '".$un."'";
		$query = $this->DatabaseHandler->Query ( "select * from ".table('clerka').$where);
		$cs = $query->GetAll ();
		if($cs){
			if($id == 0){
				//添加
				echo '{
						"info":"验证失败！",
						"status":"n"
					 }';
				return false;
			}else{
				//修改
				if($cs[0]['id'] == $id){
					echo '{
						"info":"验证通过！",
						"status":"y"
					 }';
					return true;
				}else{
					echo '{
						"info":"验证失败！",
						"status":"n"
					 }';
					return false;
				}
			}
		}else{
			echo '{
				"info":"验证通过！",
				"status":"y"
			}';
			return true;
		}
	}
	/**
	 * 检查员工手机号
	 */
	public function ckPhone(){
		$id = get("id","int");
		$phone = post("param","number");
		$where = " where phone = '".$phone."'";
		$query = $this->DatabaseHandler->Query ( "select * from ".table('clerka').$where);
		$cs = $query->GetAll ();
		if($cs){
			if($id == 0){
				//添加
				echo '{
						"info":"验证失败！",
						"status":"n"
					 }';
				return false;
			}else{
				//修改
				if($cs[0]['id'] == $id){
					echo '{
						"info":"验证通过！",
						"status":"y"
					 }';
					return true;
				}else{
					echo '{
						"info":"验证失败！",
						"status":"n"
					 }';
					return false;
				}
			}
		}else{
			echo '{
				"info":"验证通过！",
				"status":"y"
			}';
			return true;
		}
	}
	/**
	 * 检查员工编号
	 */
	public function ckNumber(){
		$seller_info = logic('seller')->GetOne(null,MEMBER_ID);
		$id = get("id","int");
		$number = post("param","number");
		$where = " where number = '".$number."'"." and sellerid = ".$seller_info['id'];
		$query = $this->DatabaseHandler->Query ( "select * from ".table('clerka').$where);
		$cs = $query->GetAll ();
		if($cs){
			if($id == 0){
				//添加
				echo '{
						"info":"验证失败！",
						"status":"n"
					 }';
				return false;
			}else{
				//修改
				if($cs[0]['id'] == $id){
					echo '{
						"info":"验证通过！",
						"status":"y"
					 }';
					return true;
				}else{
					echo '{
						"info":"验证失败！",
						"status":"n"
					 }';
					return false;
				}
			}
		}else{
			echo '{
				"info":"验证通过！",
				"status":"y"
			}';
			return true;
		}
	}
	
	private function check_clerka($id,$username,$name,$number,$phone,$optype)
	{
		$seller_info = logic('seller')->GetOne(null,MEMBER_ID);
		$where = " where username = '".$username."'"." or (number = '".$number."' and sellerid = ".$seller_info['id'].") or phone = '".$phone."'";
		$query = $this->DatabaseHandler->Query ( "select * from ".table('clerka').$where);
		$cs = $query->GetAll ();
		if($cs){
			if($optype == 1){
				//添加
				return false;
			}else{
				//修改
				if($cs[0]['id'] == $id){
					return true;
				}else{
					return false;
				}
			}
		}else{
			return true;
		}
	}

	
	public function ticket_list()
	{
		$this->iniz();
		if(isset($_GET['coupsta'])){
			$coupSTA = get('coupsta', 'int');
		}else{
			$coupSTA = TICK_STA_ANY;
		}
		$fpids = '';
		$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
		$fpids = 0;
		if($pids){
			$fpids = implode(',',$pids);
		}
        $tickets = logic('coupon')->GetList(USR_ANY, ORD_ID_ANY, $coupSTA, false, $fpids);
		$seller_info = logic('seller')->GetOne(null,MEMBER_ID);
		$money = $seller_info['money'];
		$total_money = $seller_info['total_money'];
		$account_money = $seller_info['account_money'];
		$forbid_money = $seller_info['forbid_money'];
		include handler('template')->file('seller_product_ticket');
	}
	
	public function order_list()
	{
		$this->iniz();
		if(isset($_GET['ordsta'])){
			$ordSTA = get('ordsta', 'int');
		}else{
			$ordSTA = ORD_STA_ANY;
		}
		$ordPROC = get('ordproc', 'string');
		if ($ordPROC == '__PAY_YET__') {
			$ordPROC = 'pay > 0 and paytime > 0';
		}elseif($ordPROC == 'WAIT_BUYER_PAY'){
			$ordPROC = 'pay = 0 and paytime = 0';
		}else{
			$ordPROC = $ordPROC ? ('process="'.$ordPROC.'"') : '1';
		}
		$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
		$asql = 0;
		if($pids){
			$asql = implode(',',$pids);
		}
		$ordPROC .=  ' AND productid IN('.$asql.')';
		$orders = logic('order')->GetList(0, $ordSTA, ORD_PAID_ANY, $ordPROC);
		$seller_info = logic('seller')->GetOne(null,MEMBER_ID);
		$money = $seller_info['money'];
		$total_money = $seller_info['total_money'];
		$account_money = $seller_info['account_money'];
		$forbid_money = $seller_info['forbid_money'];
		include handler('template')->file('seller_product_order');
	}
	
	public function delivery_list()
	{
		$this->iniz();
		$ordPROC = get('ordproc', 'string');
		$dlvPROC = $ordPROC ? ('o.process="'.$ordPROC.'"') : '1';
		$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
		$asql = 0;
		if($pids){
			$asql = implode(',',$pids);
		}
		$dlvPROC .= ' AND o.productid IN('.$asql.') ';
        $deliveries = logic('delivery')->GetList($ordPROC,$dlvPROC);
		$seller_info = logic('seller')->GetOne(null,MEMBER_ID);
		$money = $seller_info['money'];
		$total_money = $seller_info['total_money'];
		$account_money = $seller_info['account_money'];
		$forbid_money = $seller_info['forbid_money'];
		include handler('template')->file('seller_product_delivery');
	}
	
	public function delivery_single()
	{
		$this->iniz();
		$order = logic('order')->SrcOne(get('oid', 'number'));
		if ($order)
		{
			$product = logic('product')->SrcOne($order['productid']);
			if ($product['sellerid'] == $this->sid)
			{
				if(strlen(get('no','txt')) > 8){
					logic('delivery')->Invoice(get('oid', 'number'), get('no', 'txt')) && exit('ok');
				}else{
					exit('error');
				}
			}
		}
		exit('error');
	}
	
	public function ajax_alert()
	{
		$this->iniz();
		$id = get('id', 'int');
		$c = logic('coupon')->GetOne($id);
		logic('notify')->Call($c['uid'], 'admin.mod.coupon.Alert', $c);
		exit('ok');
	}

    public function recommendUser(){
        $seller_info = logic('seller')->GetOne(null,MEMBER_ID);
        $sid = $seller_info['id'];
        $stime = strtotime(get('stime'));
        $etime = strtotime(get('etime'));
        if($etime) $etime = strtotime('+1 day',$etime);

        $users = logic('profit')->getUsersBySellerId($sid,$stime,$etime);
        include handler('template')->file('seller_recommend_User');
    }

    public function recommendorder(){
        $months = array(1,2,3,4,5,6,7,8,9,10,11,12);
        $years = array();
        $minYear = 2015;
        $maxYear = date('Y');
        for($i=0;$minYear<=$maxYear;$i++,$minYear++){
            $years[$i]=$minYear;
        }
//        $userid = $_GET['userid'];
        $year = $_GET['year'];
        $month = $_GET['month'];

        $seller_info = logic('seller')->GetOne(null,MEMBER_ID);
        $sellerid = $seller_info['id'];
//        $orders = logic('profit')->getListBySID($sid,$_GET['userid']);
        $orders =  logic('profit')->getList($sellerid,$year,$month);
        $sums = logic('profit')->querySum($sellerid);
        include handler('template')->file('seller_recommend_order');
    }
}

?>