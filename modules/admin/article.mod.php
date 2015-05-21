<?php

/**
 * 模块：文章管理
 * @package module
 * @name category.mod.php
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
	public function main()
	{
		$this->CheckAdminPrivs('article');
		$articles = logic('article')->get_list();
		include handler('template')->file('@admin/articles_list');
	}
	function Addimage()
	{
		$this->CheckAdminPrivs('article','ajax');
		$aid = get('aid', 'int');
		$id = get('id', 'int');
		$article = logic('article')->get_one($aid);
		$imgs = explode(',', $article['pic']);
		foreach ($imgs as $i => $iid)
		{
			if ($iid == '' || $iid == 0)
			{
				unset($imgs[$i]);
			}
		}
		$imgs[] = $id;
		$new = implode(',', $imgs);
		logic('article')->Update_Article($aid, array('pic'=>$new));
		exit('ok');
	}
	function Delimage()
	{
		$this->CheckAdminPrivs('article');
		$aid = get('aid', 'int');
		$id = get('id', 'int');
		$article = logic('article')->get_one($aid);
		if ($article['pic'] == '')
		{
			logic('upload')->Delete($id);
		}
		else
		{
			$imgs = explode(',', $list['pic']);
			foreach ($imgs as $i => $iid)
			{
				if ($iid == $id)
				{
					logic('upload')->Delete($id);
					unset($imgs[$i]);
				}
			}
			$new = implode(',', $imgs);
			logic('article')->Update_Article($aid, array('pic'=>$new));
		}
		exit('ok');
	}
	public function create()
	{
		$this->CheckAdminPrivs('article');
		$article = array(
			'writer' => MEMBER_NAME
		);
        $articleTypes = logic('articletype')->get_list();
		include handler('template')->file('@admin/article_mgr');
	}
	
	public function modify()
	{
		$this->CheckAdminPrivs('article');
		$id = get('id', 'int');
		$article = logic('article')->get_one($id);
		if(!empty($article['pic']))
		{
			$article['pic']=explode(',', $article['pic']);
		}
        $articleTypes = logic('articletype')->get_list();
		include handler('template')->file('@admin/article_mgr');
	}
	
	public function delete()
	{
		$this->CheckAdminPrivs('article');
		$id = get('id', 'int');
		logic('article')->delete($id);
		$this->Messager('删除成功！', '?mod=article');
	}
	
	public function save()
	{
		$this->CheckAdminPrivs('article');
		extract ( $this->Get );
		extract ( $this->Post );
		
		if ($pic != '')
		{
			$pic = substr($pic, 0, -1);
		}
		$rebate = array (
				'title' => $title,
                'category'=>$category,
				'writer'=>$writer,
				'source'=>$source,
				'status'=>(int)$status,
				'pic'=>$pic,
				'content' => $content,
				'author_id' => MEMBER_ID
		);
		
		if ($title && $content && $writer)
		{
			$id = post('id', 'int');
			if ($id)
			{
				logic('article')->update($id,$rebate);
			}
			else
			{
				logic('article')->create($rebate);
			}
			$this->Messager('保存成功！', '?mod=article');
		}
		else
		{
			$this->Messager('标题或者内容或作者都不可以为空！', -1);
		}
	}
}

?>