<?php

/**
 * 模块：玩广告问题分类管理
 * @copyright (C)2011 Cenwor Inc.
 * @author wzn
 * @package module
 * @name adplay_ad.mod.php
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
        $this->CheckAdminPrivs('adplay_ad');
        header('Location: ?mod=adplay_ad&code=vlist');
    }
    function vList()
    {
        $this->CheckAdminPrivs('adplay_ad');
        $where='';
        $list = logic('adplay')->GetList_AD($where);
        include handler('template')->file('@admin/adplay_ad_list');
    }
    function Add()
    {
        $this->CheckAdminPrivs('adplay_ad');
        $p = array();
        //选项绑定设置
        $p['qtselect']=logic('adplay')->GetList_QT_Select();
        
        include handler('template')->file('@admin/adplay_ad_mgr');
    }
    function Edit()
    {
        $this->CheckAdminPrivs('adplay_ad');
        $id = get('id', 'int');
        $p = logic('adplay')->GetOne_AD($id);
        
        //选项绑定设置
        $p['qtselect']=logic('adplay')->GetList_QT_Select();
        $qtid='';
        if(!empty($p['qid']))
        {
            $qinfo=logic('adplay')->GetOne_Q($p['qid']);
            if($qinfo) $qtid=$qinfo['qtid'];
        }
        $p['qt_select']=$qtid;
        $p['qselect']= array();
        if($qtid!='') $p['qselect']=logic('adplay')->GetList_Q_Select($qtid);
        
        if(!empty($p['pic']))
        {
            $p['pic']=explode(',', $p['pic']);
        }
        include handler('template')->file('@admin/adplay_ad_mgr');
    }
    function Save()
    {
        $this->CheckAdminPrivs('adplay_ad');
        $data = array();
        $data['name'] = post('name', 'txt');
        $data['order'] = post('order', 'int');
        $data['money'] = post('money', 'float');
        $data['times'] = post('times', 'int');
        $data['qid'] = post('qid', 'int');
		if (post('imgs') != '')
		{
			$data['pic'] = substr(post('imgs', 'txt'), 0, -1);
		}
        foreach ($data as $key => $name)
        {
            if (is_numeric($data[$key])) continue;
            if (!$data[$key])
            {
                $this->Messager('请填写完整数据！', -1);
            }
        }
        $data['urlad'] = post('urlad');
		$data['addtime'] = time();
		$data['content'] = post('content');
		$data['sellerid'] = post('sellerid');
		$data['cityid'] = post('city');
		$data['everybody_times'] = post('everybody_times');
        
        $id = post('id', 'int');
        if ($id == 0)
        {
            $id = logic('adplay')->Add_AD($data);
        }
        else
        {
            logic('adplay')->Update_AD($id, $data);
        }
        $this->Messager('数据更新完成！', '?mod=adplay_ad&code=vlist');
    }
    function Del()
    {
        $this->CheckAdminPrivs('adplay_ad');
        $id = get('id', 'int');
        if ($id == 0)
        {
            $this->Messager('无效的数据！', '?mod=adplay_ad&code=vlist');
            return;
        }
        logic('adplay')->Del_AD($id);
        $this->Messager('删除完成！', '?mod=adplay_ad&code=vlist');
    }
	function Add_Ad_Image()
	{
		$this->CheckAdminPrivs('adplay_ad','ajax');
		$adid = get('adid', 'int');
		$id = get('id', 'int');
		$p = logic('adplay')->SrcOne_AD($adid);
		$imgs = explode(',', $p['pic']);
		foreach ($imgs as $i => $iid)
		{
			if ($iid == '' || $iid == 0)
			{
				unset($imgs[$i]);
			}
		}
		$imgs[] = $id;
		$new = implode(',', $imgs);
		logic('adplay')->Update_AD($adid, array('pic'=>$new));
		exit('ok');
	}
    function Del_Ad_Image()
	{
		$this->CheckAdminPrivs('adplay_ad','ajax');
		$adid = get('adid', 'int');
		$id = get('id', 'int');
		$p = logic('adplay')->SrcOne_AD($adid);
		if ($p['pic'] == '')
		{
		    logic('upload')->Delete($id);
		}
		else
		{
			$imgs = explode(',', $p['pic']);
			foreach ($imgs as $i => $iid)
			{
				if ($iid == $id)
				{
					logic('upload')->Delete($id);
					unset($imgs[$i]);
				}
			}
			$new = implode(',', $imgs);
		    logic('adplay')->Update_AD($adid, array('pic'=>$new));
		}
		exit('ok');
	}
}

?>