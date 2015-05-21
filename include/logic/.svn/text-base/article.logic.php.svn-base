<?php

/**
 * 逻辑区：文章管理
 * @package logic
 * @name article.logic.php
 * @version 1.0
 */

class ArticleManageLogic
{
	
	public function get_list($limit = 1, $filter = 1)
	{
		if ($limit) {
//            $limit = $limit-1 * 12;
//			$sql = dbc(DBCMax)->select('article')->where($filter)->order('addtime.desc')->limit($limit);
            $sql = 'select y_a.*,y_at.typename from ystttuangou_article as y_a
                    left join ystttuangou_articletype as y_at on y_a.category=y_at.id';
//            ; LIMIT '.$limit.',12';
		}else{
//			$sql = dbc(DBCMax)->select('article')->where($filter)->order('addtime.desc');
            $sql = 'select y_a.*,y_at.typename from ystttuangou_article as y_a
                    left join ystttuangou_articletype as y_at on y_a.category=y_at.id ';
		}
//		$sql = dbc(DBCMax)->sql($sql);
		if (!$limit) $sql = page_moyo($sql);
		$results = dbc(DBCMax)->query($sql)->done();
		return $results;
	}
	
	public function get_one($id)
	{
		return dbc(DBCMax)->select('article')->where(array('id' => $id))->limit(1)->done();
	}
	public function Update_Article($id, $data)
	{
		dbc()->SetTable(table('article'));
		$id = dbc()->Update($data, 'id = '.$id);
		return $id;
	}
	public function create($rebate = array()) {
		extract ( $rebate );
		$data = array (
            'category' => $category,
			'title' => $title,
			'writer'=>$writer,
			'source'=>$source,
			'status'=>$status,
			'pic'=>$pic,
			'content' => $content,
			'author_id' => MEMBER_ID,
			'addtime' => time()
		);
		return dbc(DBCMax)->insert('article')->data($data)->done();
	}
	
	public function update($id,$rebate = array()) {
		extract ( $rebate );
		$data = array (
			'title' => $title,
            'category' => $category,
			'writer'=>$writer,
			'source'=>$source,
			'status'=>$status,
			'content' => $content,
			'author_id' => MEMBER_ID,
			'updatetime' => time()
		);
		return dbc(DBCMax)->update('article')->data($data)->where(array('id' => $id))->done();
	}
	
	public function delete($id)
	{
		return dbc(DBCMax)->delete('article')->where(array('id' => $id))->done();
	}
}

?>