<?php

/**
 * 逻辑区：玩广告
 * @copyright (C)2011 Cenwor Inc.
 * @author wzn
 * @package logic
 * @name adplay.logic.php
 * @version 1.0
 */

class ADPlayLogic
{

//region 问题分类
	public function Add_QT($data)
	{
		dbc()->SetTable(table('adplay_qt'));
		$id = dbc()->Insert($data);
        return $id;
	}
    
	public function Update_QT($id, $data)
	{
		dbc()->SetTable(table('adplay_qt'));
		$id = dbc()->Update($data, 'id = '.$id);
        return $id;
	}
    
	public function GetOne_QT($id = null)
	{
		$sql_filter = '1';
        if(!empty($id))$sql_filter.=' AND id='.$id;
		$sql = 'SELECT * FROM '.table('adplay_qt').' WHERE '.$sql_filter.' LIMIT 1';
		return dbc()->Query($sql)->GetRow();
	}
    
	function GetList_QT($where)
	{
		$sql = 'SELECT s.*
		FROM '.table('adplay_qt').' s 
		WHERE 1';
		$sql .= ' ORDER BY `order` desc';
        
        logic('isearcher')->Linker($sql);
        
		$sql = page_moyo($sql);
		return dbc(DBCMax)->query($sql)->done();
	}
    
	function GetList_QT_Select()
	{
		$sql = 'SELECT s.id as value, s.name as display
		FROM '.table('adplay_qt').' s 
		WHERE 1';
		$sql .= ' ORDER BY `order` desc';
        
		$sql = page_moyo($sql);
		return dbc(DBCMax)->query($sql)->done();
	}
    
	public function Del_QT($id)
	{
		dbc(DBCMax)->delete('adplay_qt')->where('id='.$id)->done();
        return $id;
	}
//endregion

//region 问题
	public function Add_Q($data)
	{
		dbc()->SetTable(table('adplay_q'));
		$id = dbc()->Insert($data);
        return $id;
	}
    
	public function Update_Q($id, $data)
	{
		dbc()->SetTable(table('adplay_q'));
		$id = dbc()->Update($data, 'id = '.$id);
        return $id;
	}
    
	public function GetOne_Q($id = null)
	{
		$sql_filter = '1';
        if(!empty($id))$sql_filter.=' AND id='.$id;
		$sql = 'SELECT * FROM '.table('adplay_q').' WHERE '.$sql_filter.' LIMIT 1';
		return dbc()->Query($sql)->GetRow();
	}
    
	function GetList_Q($where)
	{
		$sql = 'SELECT q.*,qt.name as qtname
		FROM '.table('adplay_q').' q 
        left join '.table('adplay_qt').' qt on q.qtid=qt.id
		WHERE 1';
        if(!empty($where['cls']))
        {
            $sql.=' AND q.qtid='.$where['cls'];
        }
		$sql .= ' ORDER BY `order` desc, addtime desc';
        
        logic('isearcher')->Linker($sql);
        
		$sql = page_moyo($sql);
		return dbc(DBCMax)->query($sql)->done();
	}
    
	public function Del_Q($id)
	{
		dbc(DBCMax)->delete('adplay_q')->where('id='.$id)->done();
        return $id;
	}
    
	function GetList_Q_Select($qtid='')
	{
		$sql = 'SELECT s.id as value, s.name as display
		FROM '.table('adplay_q').' s 
		WHERE 1';
        if($qtid!='') $sql.=' AND qtid='.$qtid;
		$sql .= ' ORDER BY `order` desc, addtime desc';
        
		$sql = page_moyo($sql);
		return dbc(DBCMax)->query($sql)->done();
	}
//endregion

//region 问题答案
	public function Add_A($data)
	{
		dbc()->SetTable(table('adplay_a'));
		$id = dbc()->Insert($data);
        return $id;
	}
    
	public function Update_A($id, $data)
	{
		dbc()->SetTable(table('adplay_a'));
		$id = dbc()->Update($data, 'id = '.$id);
        return $id;
	}
    
	public function GetOne_A($id = null)
	{
		$sql_filter = '1';
        if(!empty($id))$sql_filter.=' AND id='.$id;
		$sql = 'SELECT * FROM '.table('adplay_a').' WHERE '.$sql_filter.' LIMIT 1';
		return dbc()->Query($sql)->GetRow();
	}
    
	function GetList_A($where)
	{
		$sql = 'SELECT * 
		FROM '.table('adplay_a').' 
		WHERE 1 '.$where;
		$sql .= ' ORDER BY `order` desc, addtime desc';
        logic('isearcher')->Linker($sql);
        
		$sql = page_moyo($sql);
		return dbc(DBCMax)->query($sql)->done();
	}
    
	public function Del_A($id)
	{
		dbc(DBCMax)->delete('adplay_a')->where('id='.$id)->done();
        return $id;
	}
    
	public function Del_A_ByQ($qid)
	{
		dbc(DBCMax)->delete('adplay_a')->where('qid='.$qid)->done();
        return $id;
	}
//endregion

//region 广告
	public function Play_AD($id, $uid)
	{
        //信息获取
        $user=logic('me')->infoMe($uid);
        $ad=logic('adplay')->GetOne_AD($id);
        $money=$ad['money']/$ad['times'];
        //次数判断
        if(intval($ad['times_played'])>=intval($ad['times']))
        {
            return 'times end';
        }
        //记录添加
        $data['userid']=intval($uid);
        $data['adpid']=$id;
        $data['money']=$money;
        $data['money_bfc']=floatval($user['scores']);
        $data['ip']=$this->GetIP();
        $data['addtime']=time();
        
        $sql="insert into `".table('adplay_log')."` (
	    `adpid`,
	    `userid`,
	    `money`,
	    `money_bfc`,
	    `ip`,
	    `addtime`) 
        VALUES(
	    ".$data['adpid'].",
	    ".$data['userid'].",
	    ".$data['money'].",
	    ".$data['money_bfc'].",
	    '".$data['ip']."',
	    ".$data['addtime'].")";
        dbc()->Query($sql);
        //积分变更
        //$sql="update `".table('members')."` set scores=scores+".$money;
        //dbc()->Query($sql);
        logic ('credit')->add_score ($id, $uid, $money, 'playAD');
        //已玩次数变更
        $sql="update `".table('adplay')."` set times_played=times_played+1 where id=".$id;
        dbc()->Query($sql);
        
        return 'ok';
	}
    
	public function Add_AD($data)
	{
		dbc()->SetTable(table('adplay'));
		$id = dbc()->Insert($data);
        return $id;
	}
    
	public function Update_AD($id, $data)
	{
		dbc()->SetTable(table('adplay'));
		$id = dbc()->Update($data, 'id = '.$id);
        return $id;
	}
    
	public function GetOne_AD($id = null)
	{
		$sql_filter = '1';
        if(!empty($id))$sql_filter.=' AND a.id='.$id;
		$sql = 'SELECT a.*,s.sellername FROM '.table('adplay').' a
				LEFT JOIN '.table('seller').' s on s.id=a.sellerid 
			    WHERE '.$sql_filter.' LIMIT 1';
		return dbc()->Query($sql)->GetRow();
	}
    
	function GetList_AD($where)
	{
		$sql = 'SELECT * 
		FROM '.table('adplay').' 
		WHERE 1 '.$where;
		$sql .= ' ORDER BY `order` desc, addtime desc';
        logic('isearcher')->Linker($sql);
        
		$sql = page_moyo($sql);
		return dbc(DBCMax)->query($sql)->done();
	}
    
	function GetList_AD_ByUser($uid, $state=0, $order='',$cityid=0)
	{
		$sql = 'select a.*, al.userid 
            from ystttuangou_adplay a 
            left join (select id, adpid, userid from ystttuangou_adplay_log where userid='.$uid.') al on a.id=al.adpid 
		    WHERE 1 ';
        if($state!=0)
        {
            switch($state)
            {
                case 1://已玩过
                    $sql.=' and userid='.$uid;
                    break;
                case 2://未玩过
                    $sql.=' and userid is null';
                    break;
            }
        }
        $cityid && $sql.=' and cityid='.$cityid;
        if($order!='')
        {
            switch($order)
            {
                case 'time':
                    $sql.=' ORDER BY `order` desc, addtime desc';
                    break;
                case 'hot'://按玩的次数排序，所以得过滤掉已经结束的
                    $sql.=' and times_played<times';
                    $sql.=' ORDER BY times_played desc';
                    break;
            }
        }
        else
        {
            $sql .= ' ORDER BY `order` desc, addtime desc';
        }
        
		$sql = page_moyo($sql);
		return dbc(DBCMax)->query($sql)->done();
	}
    
	public function Del_AD($id)
	{
		dbc(DBCMax)->delete('adplay')->where('id='.$id)->done();
        return $id;
	}
    
	public function SrcOne_AD($id)
	{
		return dbc(DBCMax)->select('adplay')->where('id='.(int)$id)->limit(1)->done();
	}
    
	public function GetOne_AD_Log($qid, $uid)
	{
		$sql_filter = '1';
        $sql_filter.=' AND adpid='.$qid.' AND userid='.$uid;
		$sql = 'SELECT * FROM '.table('adplay_log').' WHERE '.$sql_filter.' LIMIT 1';
		return dbc()->Query($sql)->GetRow();
	}
	public function addError($data)
	{
		//错误记录
		dbc()->SetTable(table('adplay_error'));
		return dbc()->Insert($data);
	}
	public function getErrorCount($data)
	{
		return dbc ( DBCMax )->select ( 'adplay_error' )->in ( 'COUNT(1) AS errorcount' )->where ($data)->limit ( 1 )->done ();
	}
    public function GetIP()
    {
        if ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]) 
        { 
            $ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]; 
        } 
        elseif ($HTTP_SERVER_VARS["HTTP_CLIENT_IP"]) 
        { 
            $ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"]; 
        }
        elseif ($HTTP_SERVER_VARS["REMOTE_ADDR"]) 
        { 
            $ip = $HTTP_SERVER_VARS["REMOTE_ADDR"]; 
        } 
        elseif (getenv("HTTP_X_FORWARDED_FOR")) 
        { 
            $ip = getenv("HTTP_X_FORWARDED_FOR"); 
        } 
        elseif (getenv("HTTP_CLIENT_IP")) 
        { 
            $ip = getenv("HTTP_CLIENT_IP"); 
        } 
        elseif (getenv("REMOTE_ADDR"))
        { 
            $ip = getenv("REMOTE_ADDR"); 
        } 
        else 
        { 
            $ip = "Unknown"; 
        }
        return $ip;
    }
    
	public function GetNext_AD($id = 0)
	{
		$sql_filter = '1';
        if(!empty($id))$sql_filter.=' AND id>'.$id;
		$sql = 'SELECT * FROM '.table('adplay').' WHERE '.$sql_filter.' LIMIT 1';
		return dbc()->Query($sql)->GetRow();
	}
    
    //region 积分兑换话费
	public function Add_CL($data)
	{
        //兑换记录
		dbc()->SetTable(table('adplay_credit_log'));
		$id = dbc()->Insert($data);
		if(!$id) return 0;
		$info= $data['type']==1 ? '积分兑换话费：':'广告金额提取：';
		logic('credit')->add_score_z(intval($data['userid']), -intval($data['cost']), $info.$data['value'].' 元。');
        return $id;
	}
    
	public function Update_CL($id, $data)
	{
		dbc()->SetTable(table('adplay_credit_log'));
		$id = dbc()->Update($data, 'id = '.$id);
        return $id;
	}
	
	public function updateMember($uid, $money)
	{
		return dbc ( DBCMax )->update ( 'members' )->data ( 'money=money+' . abs($money) )->where ( 'uid=' . ( int ) $uid )->done ();
	}
    
	public function GetOne_CL($id = null)
	{
		$sql_filter = '1';
        if(!empty($id))$sql_filter.=' AND id='.$id;
		$sql = 'SELECT * FROM '.table('adplay_credit_log').' WHERE '.$sql_filter.' LIMIT 1';
		return dbc()->Query($sql)->GetRow();
	}
    
	function GetList_CL($where)
	{
		$sql = dbc(DBCMax)->select('adplay_credit_log')->where($where)->order('addtime.desc');
		$sql = dbc(DBCMax)->sql($sql);
        logic('isearcher')->Linker($sql);
		$sql = page_moyo($sql);
		return dbc(DBCMax)->query($sql)->done();
	}
    
	public function Del_CL($id)
	{
		dbc(DBCMax)->delete('adplay_credit_log')->where('id='.$id)->done();
        return $id;
	}
    
    function GetStateName_CL($state)
    {
        switch(intval($state))
        {
            case 1:
                return '兑换成功';
            case 2:
                return '等待审核';
            case 3:
                return '兑换失败';
        }
    }
    //endregion
    
	public $state_filtes = array (
			0 => array (
					'name' => '全部',
					//'sql' => ' 1 ' 
			),
			1 => array (//已玩过
					'name' => '已玩过',
					//'sql' => ' s.price_avg <= 20 ' 
			),
			2 => array (//未玩过
					'name' => '未玩过',
					//'sql' => ' s.price_avg >= 20 AND s.price_avg <= 50 ' 
			)
	);
	public function state_navigate() {
		$state = get ( 'state', 'int' );
		$state_navs = array ();
		foreach ( $this->state_filtes as $k => $r ) {
			$r ['title'] = ($r ['title'] ? $r ['title'] : $r ['name']);
			$r ['url'] = logic('url')->create_url('?mod=adplay&', 'adplay', array('state' => $k));
			$r ['selected'] = ($state == $k ? true : false);
			$state_navs [$k] = $r;
		}
		include handler ( 'template' )->file ( '@html/adplay/state_navigate' );
	}
}
?>