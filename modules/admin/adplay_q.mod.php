<?php

/**
 * 模块：玩广告问题分类管理
 * @copyright (C)2011 Cenwor Inc.
 * @author wzn
 * @package module
 * @name adplay_q.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
    function ModuleObject( $config )
    {
        $this->MasterObject($config);
        $runCode = Load::moduleCode($this);
        $this->$runCode();
    }
    function Main()
    {
        $this->CheckAdminPrivs('adplay_q');
        header('Location: ?mod=adplay_q&code=vlist');
    }
    function vList()
    {
        $this->CheckAdminPrivs('adplay_q');
        $where=array();
        if(isset($_GET['cls'])){
            $cls = get('cls', 'int');
			is_numeric($cls) && $where['cls']=$cls;
        }
        $list = logic('adplay')->GetList_Q($where);
        include handler('template')->file('@admin/adplay_q_list');
    }
    function Add()
    {
        $this->CheckAdminPrivs('adplay_q');
        $p = array();
        $p['qtselect']=logic('adplay')->GetList_QT_Select();
        $p['ansData']='{}';
        include handler('template')->file('@admin/adplay_q_mgr');
    }
    function Edit()
    {
        $this->CheckAdminPrivs('adplay_q');
        $id = get('id', 'int');
        $p = logic('adplay')->GetOne_Q($id);
        
        $p['qtselect']=logic('adplay')->GetList_QT_Select();
        $p['ansData'] = json_encode(logic('adplay')->GetList_A(' and qid='.$id));
        if($p['ansData']=='')$p['ansData']='{}';
        
        include handler('template')->file('@admin/adplay_q_mgr');
    }
    function Save()
    {
        $this->CheckAdminPrivs('adplay_q');
        $data = array();
        $data['name'] = post('name', 'txt');
        $data['order'] = post('order', 'int');
        //正确答案检测
        if($_POST['ansright']=='')
        {
            $this->Messager('请选择一个正确答案！', -1);
        }
        
        foreach ($data as $key => $name)
        {
            if (is_numeric($data[$key])) continue;
            if (!$data[$key])
            {
                $this->Messager('【'.$name.'】不能为空！', -1);
            }
        }
        $data['qtid'] = post('qtid', 'int');
        if(empty($data['qtid']))
        {
            $this->Messager('请选择分类！', -1);
        }
        $id = post('id', 'int');
        if ($id == 0)
        {
			$data['addtime'] = time();
            $id = logic('adplay')->Add_Q($data);
        }
        else
        {
            logic('adplay')->Update_Q($id, $data);
        }
        //答案处理
        logic('adplay')->Del_A_ByQ($id);
        $ansright=$_POST['ansright'];
        foreach ($_POST['answer'] as $key=>$value)
        {
            if($value=='') continue;
            $data_a['qid']=$id;
            $data_a['name']=$value;
            $data_a['order']=count($_POST['answer']) - $key;
            $data_a['isright']=intval($ansright)==$key?1:0;
			$data_a['addtime'] = time();
            logic('adplay')->Add_A($data_a);
        }
        
        $this->Messager('数据更新完成！', '?mod=adplay_q&code=vlist');
    }
    function Del()
    {
        $this->CheckAdminPrivs('adplay_q');
        $id = get('id', 'int');
        if ($id == 0)
        {
            $this->Messager('无效的数据！', '?mod=adplay_q&code=vlist');
            return;
        }
        logic('adplay')->Del_QT($id);
        $this->Messager('删除完成！', '?mod=adplay_q&code=vlist');
    }
    
    function GetList_Select()
	{
		$this->CheckAdminPrivs('adplay_q','ajax');
		$qtid = get('qtid', 'int');
		if (empty($qtid))
		{
		    exit('{}');
		}
        $infoes = logic('adplay')->GetList_Q_Select($qtid);
        exit(json_encode($infoes));
	}
}

?>