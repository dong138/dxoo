<?php

/**
 * 逻辑区：评论管理
 * @package logic
 * @name comment.logic.php
 * @version 1.0
 */
class CommentManageLogic {
	public function show_summary($product_id) {
		$summary = $this->front_get_summary ( $product_id );
		$comments = $this->front_get_comments ( $product_id );
		if ($this->if_i_buyed_product ( $product_id )) {
			$i_buyed = true;
			$comment_my = $this->front_get_comment_by_me ( $product_id );
            $canComment = $this->checkCanComment ( $product_id );
		} else {
			$i_buyed = false;
			$comment_my = false;
            $canComment = false;
		}
		include handler ( 'template' )->file ( 'comment_summary' );
	}
	public function front_get_summary($product_id) {
		$query = dbc ( DBCMax )->select ( 'comments' )->in ( 'count(1) as CCNT, avg(score) as CAVG' )->where ( array (
				'product_id' => $product_id,
				'status' => 'approved' 
		) )->limit ( 1 )->done ();
		return array (
				'count' => $query ['CCNT'] ? $query ['CCNT'] : 0,
				'average' => $query ['CAVG'] ? round ( $query ['CAVG'], 1 ) : 0 
		);
	}
	public function front_get_comments($product_id, $user_id = null) {
		$user_id || $user_id = user ()->get ( 'id' );
		$sql = dbc ( DBCMax )->select ( 'comments' )->where ( 'product_id=' . $product_id . ' and (status="approved" or user_id=' . $user_id . ')' )->order ( 'toped.desc' )->order ( 'timestamp_update.desc' )->sql ();
		$sql = page_moyo ( $sql );
		$comments = dbc ( DBCMax )->query ( $sql )->done ();
		return $comments;
	}
    public function checkCanComment($product_id,$user_id=null){
        $user_id || $user_id = user ()->get ( 'id' );
        $sql = 'select orderid
             from ystttuangou_order o
            left join ystttuangou_comments c ON o.orderid=c.order_id
            where c.id is null and userid='.$user_id.' and o.productid='.$product_id.'
            and (select count(t.ticketid) from ystttuangou_ticket t where t.orderid=o.orderid and t.`status`=1)>0
            ORDER BY o.orderid limit 1';
        return dbc ( DBCMax )->query ( $sql )->done ();
    }
	public function front_get_comment_by_me($product_id, $user_id = null) {
		$user_id || $user_id = user ()->get ( 'id' );
		return dbc ( DBCMax )->select ( 'comments' )->where ( array (
				'product_id' => $product_id,
				'user_id' => $user_id
		) )->order('timestamp_update.desc')->done ();
//        return dbc ( DBCMax )->select ( 'comments' )->where ( array (
//            'product_id' => $product_id,
//            'user_id' => $user_id
//        ) )->limit ( 1 )->done ();
	}
	private function if_i_buyed_product($product_id, $user_id = null) {
		$user_id || $user_id = user ()->get ( 'id' );
		if ($user_id > 0) {
			return logic ( 'product' )->AlreadyBuyed ( $product_id, $user_id );
		} else {
			return false;
		}
	}
	public function source_get_one($id) {
		return dbc ( DBCMax )->select ( 'comments' )->where ( array (
				'id' => $id 
		) )->limit ( 1 )->done ();
	}
	public function admin_form_submit($score, $content, $reply = null, $user_name = null, $timestamp_update = null, $product_id = null, $id = null) {
		$data = array (
				'score' => $score,
				'content' => $content 
		);
		$reply && $data ['reply'] = $reply;
		$data ['user_name'] = $user_name ? $user_name : '买家';
		$data ['timestamp_update'] = $timestamp_update;
		$product_id && $data ['product_id'] = $product_id;
		$data['timestamp_update'] = $timestamp_update ? $timestamp_update : time();
		if ($id) {
			$r = dbc ( DBCMax )->update ( 'comments' )->where ( array (
					'id' => $id 
			) )->data ( $data )->done ();
		} else {
			$r = dbc ( DBCMax )->insert ( 'comments' )->data ( $data )->done ();
		}
		return $r ? true : false;
	}
	public function admin_vlist($keyword=1) {
		$sql = 'SELECT c.*,p.name,p.flag
		FROM
			'.table('comments').' c
		LEFT JOIN
			'.table('product').' p
		ON
			p.id = c.product_id
		WHERE
			'.$keyword.'
		ORDER BY
			timestamp_update desc';
		$sql = page_moyo ( $sql );
		$comments = dbc ( DBCMax )->query ( $sql )->done ();
		//var_dump($comments);
		return $comments;
	}
	public function status_sync($id, $status) {
		$sa = array (
				'auditing',
				'approved',
				'denied' 
		);
		if (in_array ( $status, $sa )) {
			$r = dbc ( DBCMax )->update ( 'comments' )->where ( array (
					'id' => $id 
			) )->data ( array (
					'status' => $status 
			) )->done ();
			if ($status == 'approved') {
				$comments = dbc ( DBCMax )->select ( 'comments' )->where ( array (
						'id' => $id 
				) )->limit ( 1 )->done ();
				if ($comments ['product_id'] && $comments ['user_id']) {
					logic ( 'credit' )->add_score ( $comments ['product_id'], $comments ['user_id'], 0, 'reply' );
				}
			}
			return $r ? true : false;
		} else {
			return false;
		}
	}
	public function toped_sync($id, $switch) {
		$sa = array (
				'true',
				'false' 
		);
		if (in_array ( $switch, $sa )) {
			$toped = $switch == 'true' ? 1 : 0;
			$r = dbc ( DBCMax )->update ( 'comments' )->where ( array (
					'id' => $id 
			) )->data ( array (
					'toped' => $toped 
			) )->done ();
			return $r ? true : false;
		} else {
			return false;
		}
	}
	public function delete($id) {
		return dbc ( DBCMax )->delete ( 'comments' )->where ( array (
				'id' => $id 
		) )->done ();
	}
	public function front_user_submit($product_id, $score, $content, $imgs, $user_id = null, $comment_id = null) {
		if (( int ) $score > 0 && $content) {
			$user_id || $user_id = user ()->get ( 'id' );
			if ($this->if_i_buyed_product ( $product_id, $user_id )) {
				if ($comment_id) {
					$comment = $this->source_get_one ( $comment_id );
					$comment || $comment ['user_id'] = - 1;
					if ($comment ['user_id'] != $user_id) {
						return '您无法编辑其他人的评论！';
					} else {
						$up_id = $comment ['id'];
						$data = array (
								'score' => $score,
								'content' => $content 
						);
					}
				} else {
					$up_id = false;
					$data = array (
							'product_id' => $product_id,
							'user_id' => $user_id,
							'user_name' => user ( $user_id )->get ( 'name' ),
							'score' => $score,
							'content' => $content,
							'status' => ini ( 'comment.dstatus' ),
							'imgs' => $imgs,
							'timestamp_update' => time ()
					);
				}
				if ($up_id) {
					return dbc ( DBCMax )->update ( 'comments' )->where ( array (
							'id' => $up_id 
					) )->data ( $data )->done ();
				} else {
                    $orderid = $this->checkCanComment( $product_id, $user_id );

					if (!$orderid) {
						return '消费后才通进行评价！';
					} else {
                        $data['order_id'] = $orderid[0]['orderid'];
						if (ini ( 'comment.dstatus' ) == 'approved') {
							logic ( 'credit' )->add_score ( $product_id, $user_id, 0, 'reply' );
						}
						return dbc ( DBCMax )->insert ( 'comments' )->data ( $data )->done ();
					}
				}
			} else {
				return '您未购买过此产品，无法进行评价！';
			}
		} else {
			return '请选择评分并填写评价内容！';
		}
	}
	
	/**
	 * ***************************************************************************
	 * {
	 * status:0/1 是否有评论
	 * msg:html 显示评论html
	 * total:总量
	 * page:当前页
	 *
	 * }
	 */
	function gt($arr,$cur_page,$cur_type) {
		if(count($arr) == 0){
			return "";
		}
		$html .= '<ul>';
		foreach ( $arr as $a ) {
			$html .= '<li>';
			$html .= '<div class="pl_list_box">';
			$html .= '<div class="pl_list_title">';
			$html .= '<span class="vip_name"> ' . $a ['user_name'] . '</span> <span class="vip_ico2"></span> <span class="vip_time">' . (date ( 'Y-m-d H:i:s', $a ['timestamp_update'] )) . '</span>';
			$html .= '<span class="vip_step0">';
			for($i = 0; $i < $a ['score']; $i ++)
				$html .= '<i></i>';
			$html .= '</span>';
			$html .= '</div>';
			$html .= '<div class="pl_list_main">' . $a ['content'] . '</div>';
			
			if ($a ['imgs']) {
				$EveryImgs = explode ( ',', $a ['imgs'] );
				$html .= '<ul style="width:700px;">';
				$index = 0;
				foreach ( $EveryImgs as $ei ) {
					$html .= '<li style="padding-right:5px;"><a onclick="add_pics('.($index++).',this);return false">';
					$html .= '<img width="105" height="105" src="'.imager($ei, IMG_Original).'" />';
					$html .= '</a></li>';
				}
				$html .= '</ul>';
				
			}
			$html .= '<h3>' . $a ['sellername'] . '</h3>';
			if ($a ['reply']) {
				$html .= '<div class="store_ask">商家回复：' . $a ['reply'] . '</div>';
			}
			$html .= '</div>';
			$html .= '</li>';
		}
		$html .= '</ul>';
		if(count($arr) == COMMENT_PAGESIEZE){
			$html .='<a class="category-floor__foot" style="margin-top:15px" href="javascript:void(0);" onclick="pj('.$cur_type.','.($cur_page + 1).');" id="next_page_'.$cur_type.'">下一页</a>';
		}
		return $html;
	}
	public function getComment($CommentTypeSQL, $productid, $pages,$commentType) {
// 		$sql = 'select count(*) counts from ' . table ( 'comments' ) . ' a,' . table ( 'product' ) . ' b,' . table ( 'seller' ) . ' c where a.product_id=b.id and a.status=\'approved\' and b.sellerid=c.id and a.product_id=' . $productid . $CommentTypeSQL;
// 		$query = mysql_query ( $sql );
// 		$row = mysql_fetch_array ( $query );
// 		$ct = $row ['counts'];
		
		$sql2 = 'select product_id,user_id,sellerid,user_name,timestamp_update,a.score,reply,a.content,a.imgs,sellername from '
			. table ( 'comments' ) . ' a,' . table ( 'product' ) . ' b,'
			. table ( 'seller' )
			. ' c where a.product_id=b.id and a.status=\'approved\' and b.sellerid=c.id and a.product_id='
			. $productid . $CommentTypeSQL . ' order by timestamp_update desc limit '
			. (($pages - 1) * COMMENT_PAGESIEZE) . ','.COMMENT_PAGESIEZE;
		
		$LastComment = dbc ( DBCMax )->query ( $sql2 )->done (); // 每页显示数据
		$html = $this->gt ( $LastComment ,$pages,$commentType);
		$ops = array (
				'status' => '1',
				'msg' => $html
		);
		return (jsonEncode ( $ops ));
	}

    public function add($data){
        return dbc ( DBCMax )->insert ( 'comments' )->data ( $data )->done ();
    }
}

?>