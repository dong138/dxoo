<?php
/**
 * @package php
 * @name getseller.mod.php
 * @date 2014-09-01 17:24:22
 */
 

class ModuleObject extends MasterObject
{
	var $Config = array(); 	var $ID;

	function ModuleObject(& $config){
		$this->MasterObject($config);
		$this->initMemberHandler();
		$this->ID=$this->Post['id']?(int)$this->Post['id']:(int)$this->Get['id'];
		Load::moduleCode($this);$this->Execute();
	}

	function Execute(){
		switch ($this->Code){
			case 'linkproduct':
				$this->Linkproduct();
				break;
			case 'getshowtype':
				$this->Showtype();
				break;
			default:
				$this->Showseller();
				break;
		}
	}
	/**
	 * 添加产品的时候商家的加载
	 * max
	 */
	function Showseller(){
		$id=$this->Get['city'];
		$sql='SELECT * FROM '.TABLE_PREFIX.'tttuangou_seller where area = '.intval($id) .' and is_agent=0';
		$query = dbc()->Query($sql);
		$seller=$query->GetAll();
		if(empty($seller)){echo __('<option>暂无商家</option>');exit;}
		echo '<option value="-1" >请选择商家</option>';
		foreach($seller as $i => $value){
			echo '<option value="'.$value['id'].'"';
			if ($_GET['seller'] == $value['id'])
			{
			    echo ' selected="selected"';
			}
			echo '>'.$value['sellername'].'</option>';
		}
		exit;
	}
	
	//显示方式
	function Showtype(){
		$id=$this->Get['sellerid'];
		$sql='SELECT * FROM '.TABLE_PREFIX.'tttuangou_seller where id = '.intval($id) .' limit 1';
		$query = dbc ()->Query ( $sql )->GetRow ();
		if(!$query){echo __('<option>暂无商家</option>');exit;}
		if ($query['agentid']>0){
			echo '<option value="1" >代理商或独立的商家</option>';
			echo '<option value="2" >商家(禁用的商家)</option>';
		}else{
			echo '<option value="1" >代理商或独立的商家</option>';
		}
		exit;
	}

	function Linkproduct(){
		$html = '';
		$id = $this->Get['city'];
		if($id > 0){
			$sellers = dbc(DBCMax)->query("SELECT id,sellername FROM `".table('seller')."` WHERE `enabled`='true' AND area ='".intval($id)."'")->done();
		}
		if($sellers){
			foreach($sellers as $k => $v){
				$html .= '<option value="'.$v['id'].'">'.$v['sellername'].'</option>';
			}
		}else{
			$html .= '<option value="">请选择...</option>';
		}
		echo $html;
		exit;
	}
}
?>