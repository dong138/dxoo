<?php
/**
 * @package php
 * @name branch.mod.php
 * @date 2014-09-01 17:24:23
 */
class ModuleObject extends MasterObject {
	function ModuleObject($config) {
		$this->MasterObject ( $config );
		$runCode = Load::moduleCode ( $this );
		$this->$runCode ();
	}
	function Main() {
		$this->CheckAdminPrivs ( 'branch' );
		$keyword = $this->Post ['keyword'] == '' ? $this->Get ['keyword'] : $this->Post ['keyword'];
		$addsql = ' where 1 ';
		if ($keyword != '')
			$addsql .= ' and name like \'%' . $keyword . '%\'  and id like \'%' . $keyword . '%\'';
		$page = intval ( $_REQUEST ['page'] ) == false ? 1 : intval ( $_REQUEST ['page'] );
		$sql = 'SELECT count(*) from ' . TABLE_PREFIX . 'tttuangou_branch ' . $addsql;
		$query = $this->DatabaseHandler->Query ( $sql );
		$num = $query->GetRow ();
		$num = $num ['count(*)'];
		//if ($num == 0 && $addsql != '')
			//$this->Messager ( "无法找到匹配的商家分店" );
		$pagenum = 10;
		$page_arr = page ( $num, $pagenum, $query_link, $_config );
		
		$sql = 'SELECT * from ' . TABLE_PREFIX . 'tttuangou_branch ' . $addsql . ' ORDER BY id DESC' . ' limit ' . ($page - 1) * $pagenum . ',' . $pagenum;
		$query = $this->DatabaseHandler->Query ( $sql );
		$branch = $query->GetAll ();
		include (handler ( 'template' )->file ( '@admin/branch' ));
	}
	function SellerBranch() {
		$this->CheckAdminPrivs('branch');
		$branch_list=logic('branch')->GetList();
		$settings = ConfigHandler::get('product');
		include(handler('template')->file("@admin/branch_list"));
	}
	function seller()
	{
		$this->CheckAdminPrivs('branch');
		$branchid = get('id', 'int');
		$key = get('key', 'txt');
		$branch = dbc(DBCMax)->select('branch')->where(array('id' => $branchid))->limit(1)->done();
		if($key){
			//key存在则是查询product产品用于设置产品为本周精选
			$seller = logic('branch')->seller_list($branchid,$key);
		}else{
			//key不存在则查询cream获取当前城市的本周精选
			$seller = logic('branch')->branch_list($branchid);
		}
		include handler('template')->file('@admin/seller_branch_list');
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
	function Addbranch() {
		$this->CheckAdminPrivs ( 'branch' );
		$action = '?mod=branch&code=Doaddbranch';
		include (handler ( 'template' )->file ( '@admin/branch_mgr' ));
	}
	function Editbranch() {
		$this->CheckAdminPrivs ( 'branch' );
		extract ( $this->Get );
		extract ( $this->Post );
		$sql = 'select * from ' . TABLE_PREFIX . 'tttuangou_branch where id = ' . $id;
		$query = $this->DatabaseHandler->Query ( $sql );
		$branch = $query->GetRow ();
		$action = '?mod=branch&code=doeditseller';
		include (handler ( 'template' )->file ( '@admin/branch_mgr' ));
	}
	function Add_ajax()
	{
		$this->CheckAdminPrivs('branch','ajax');
		$sellerid = get('sellerid', 'int');
		$branchid = get('branchid', 'int');
		$sellerid || $sellerid = 0;
		$branchid || $branchid = 0;
		logic('branch')->Delete($sellerid,$branchid);
		$sellerid && $branchid && $add = logic('branch')->Addseller ( $sellerid,$branchid );
		echo $add>0 ? "0" : "1";//0成功1失败
	}
	function Doeditseller() {
		$this->CheckAdminPrivs ( 'branch' );
		extract ( $this->Post );
		$id = ( int ) $id;
		$ary = array (
				'name' => strip_tags ( $branchname ),
				'upstime' => time (),
		);
		$update = dbc ( DBCMax )->update ( 'branch' )->data ( $ary )->where ( 'id=' . $id )->done ();
		if($update){
			$this->Messager ( "操作成功", '?mod=branch' );
		}else{
			$this->Messager ( "操作失败", '?mod=branch' );
		}
	}
	function Doaddbranch() {
		$this->CheckAdminPrivs ( 'branch' );
		extract ( $this->Get );
		extract ( $this->Post );
		if ($branchname == '') {
			$this->Messager ( "请将参数都填写完整!", - 1 );
		}
		$rebate = array (
				'name' => $branchname
		);
		$sid = logic ( 'branch' )->Add ( $rebate );
		if (! $sid){
			$this->Messager ( '添加失败！请重试', - 1 );
		}
		$this->Messager ( "操作成功", '?mod=branch' );
	}
	function Delete() {
		$this->CheckAdminPrivs ( 'branch' );
		extract ( $this->Get );
		$sinfo = dbc ( DBCMax )->query ( 'select * from ' . table ( 'branch' ) . ' where id=' . $id )->limit ( 1 )->done ();
		if (! $sinfo) {
			$this->Messager ( "删除失败，该商家分店已经不存在！", '?mod=branch' );
		}
		$sql = 'select * from ' . TABLE_PREFIX . 'tttuangou_seller_branch where branchid = ' . intval ( $id );
		$query = $this->DatabaseHandler->Query ( $sql );
		$user = $query->GetAll ();
		if (! empty ( $user ))
			$this->Messager ( "您必须先删除该商家分店！才能删除该分店", '?mod=branch' );
		$this->DatabaseHandler->SetTable ( TABLE_PREFIX . 'tttuangou_branch' );
		$result = $this->DatabaseHandler->Delete ( '', 'id=' . intval ( $id ) );
		if($result){
			$this->Messager ( "删除成功", '?mod=branch' );
		}else{
			$this->Messager ( "删除失败", '?mod=branch' );
		}
	}
	function Del_ajax()
	{
		$this->CheckAdminPrivs('branch','ajax');
		$sellerid = get('sellerid', 'int');
		$branchid = get('branchid', 'int');
		$sellerid || $branchid || exit('false');
		if(logic('branch')->Delete($sellerid,$branchid)){
			exit('0');
		}
		exit('1');
	}
}
?>