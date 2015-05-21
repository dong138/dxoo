<?php

/**
 * 模块：评论管理
 * @package module
 * @name comment.mod.php
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
	
	public function vlist()
	{
		$this->CheckAdminPrivs('comments');
		$keyword= get('key','txt');
		$where=1;
		$keyword && $where=' (p.flag like "%'.$keyword.'%" or c.user_name like "%'.$keyword.'%" or c.content like "%'.$keyword.'%")';
		$comments = logic('comment')->admin_vlist($where);
		include handler('template')->file('@admin/comments_list');
	}
	
	public function status_sync()
	{
		$this->CheckAdminPrivs('comments','ajax');
		$id = get('id', 'int');
		$status = get('status', 'txt');
		$r = logic('comment')->status_sync($id, $status);
		exit($r ? 'ok' : 'error');
	}
	
	public function toped_sync()
	{
		$this->CheckAdminPrivs('comments','ajax');
		$id = get('id', 'int');
		$switch = get('switch', 'txt');
		$r = logic('comment')->toped_sync($id, $switch);
		exit($r ? 'ok' : 'error');
	}
	
	public function ajax_modify()
	{
		$this->CheckAdminPrivs('comments','ajax');
		$id = get('id', 'int');
		$comment = logic('comment')->source_get_one($id);
		include handler('template')->file('@admin/comments_mgr_ajax');
	}
	
	public function ajax_submit()
	{
		$this->CheckAdminPrivs('comments','ajax');
		$id = post('id', 'int');
		$product_id = post('product_id', 'int');
		$score = post('score', 'int');
		$content = post('content', 'string');
		$reply = post('reply', 'string');
		$user_name = post('user_name', 'txt');
		$timer = post('timestamp_update', 'txt');
		preg_match_all('/\d/',$timer,$arr);
		$timestamp_update = strtotime(implode('',$arr[0]));
		$r = logic('comment')->admin_form_submit($score, $content, $reply, $user_name, $timestamp_update, $product_id, $id);
		exit($r ? 'ok' : '没有对数据进行任何修改`');
	}
	
	public function ajax_delete()
	{
		$this->CheckAdminPrivs('comments','ajax');
		$id = get('id', 'int');
		logic('comment')->delete($id);
		exit('<script type="text/javascript">comment_delete_result("ok", '.$id.');</script>');
	}
	
	public function ajax_view()
	{
		$this->CheckAdminPrivs('comments','ajax');
		$id = get('id', 'int');
		$comment = logic('comment')->source_get_one($id);
		exit($comment['content']);
	}
	
	public function config()
	{
		$this->CheckAdminPrivs('comments');
		$config = ini('comment');
		include handler('template')->file('@admin/comments_config');
	}
	
	public function config_save()
	{
		$this->CheckAdminPrivs('comments');
		$config = post('config');
		ini('comment', $config);
		$this->Messager('设置保存成功！', '?mod=comment&code=config');
	}
    public function add()
    {
        $this->CheckAdminPrivs('comments');
        $product = logic('product')->GetOne( $_GET['pid']);
        if(!$product){
            $this->Messager ( "url错误！");
        }
        include handler('template')->file('@admin/comments_add');
    }
    public function AddSubmit(){
        $this->CheckAdminPrivs('comments');
        extract($_GET);
        extract($_POST);
        if(!$pid){
            $this->Messager ( "提交数据出误！");
        }
        $user_id = user ()->get ( 'id' );
        $arr = array(
            'product_id'=>$pid,
            'user_id'=>$user_id,
            'status'=>'auditing',
            'score'=>$score,
            'user_name'=>$user_name,
            'content'=>$content,
            'imgs'=>$imgs,
            'timestamp_update' => strtotime($timestamp_update));
//        vdp($arr);
        $result = logic('comment')->add($arr);
        $this->Messager ( "操作成功", "-2");
    }
}

?>