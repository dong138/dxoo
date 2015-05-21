<?php

/**
 * 模块：评论相关
 * @package module
 * @name comment.mod.php
 * @version 1.1
 */
class ModuleObject extends MasterObject {
	function ModuleObject($config) {
		$this->MasterObject ( $config );
		$runCode = Load::moduleCode ( $this, false, false );
		$this->$runCode ();
	}
	public function submit() {
		$product_id = get ( 'pid', 'int' );
		$score = post ( 'score', 'int' );
		$content = post ( 'content', 'txt' );
		$imgs = post('imgs', 'txt');
		$result = logic ( 'comment' )->front_user_submit ( $product_id, $score, $content, $imgs );
		if (is_numeric ( $result )) {
			exit ( 'ok' );
		} else {
			exit ( $result );
		}
	}
	/**
	 * 
	 * @param unknown $commentType 评价类型1:全部2:好评3：中评4：差评5：有图
	 * @param unknown $productId 产品ID
	 * @param unknown $page 当前页
	 */
	public function getComment(){
		$commentType = post ( 'commentType', 'int');
		$productId = post ( 'productId', 'int');
		$page = post ( 'page', 'int');
		
		$commentTypeSQL = "";
		if($commentType == 2){
			$commentTypeSQL = " and a.score = 5";
		}
		if($commentType == 3){
			$commentTypeSQL = " and a.score between 2 and 4";
		}
		if($commentType == 4){
			$commentTypeSQL = " and a.score = 1";
		}
		if($commentType == 5){
			$commentTypeSQL = " and a.imgs is not null and a.imgs != ''";
		}
		exit(logic ('comment')->getComment($commentTypeSQL,$productId,$page,$commentType));
	}
}

?>