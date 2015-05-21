<?php
class ModuleObject extends MasterObject {
	function ModuleObject($config) {
		$this->MasterObject ( $config );
		Load::logic ( 'Pinyin' );
		$this->PinyinLogic = new PinyinLogic ();
		
		$runCode = Load::moduleCode ( $this, $this->Code );
		$this->$runCode ();
	}
	
	function main(){
        include handler ( 'template' )->file ( 'test' );
	}
	
	function ajaxSeller(){
		$keyword= get('q','txt');
		$filter.='id like "%'.$keyword.'%" or sellername like "%'.$keyword.'%" or allpinyin like "%'.$keyword.'%" or firstpinyin like "%'.$keyword.'%"';
		$list=logic('search')->GetLike($filter,'seller','id,sellername');
	}
	
	function tree(){
		$list=logic('catalog')->GetList(0);
		$ret = array();
		foreach ($list as $i => $one)
		{
			$ret[] = array(
					"id" => $one['id'],
					"text" => $one['name'],
					"value" => $one['procount'],
					"showcheck" => true,
					"complete" => false,
					"isexpand" => false,
					"checkstate" => 0,
					"hasChildren" => true
			);
		}
		echo jsonEncode($ret);
	}
	
	function pinyin(){
//		$list=logic('search')->GetOne(92);
// 		echo $list['sellername'].'====';
// 		echo $this->PinyinLogic->pinyinAll($list['sellername']).'====';
// 		echo strtoupper($this->PinyinLogic->getFirst('合作商家dd22U'));
	
// 		logic('search')->Update($list['id'],
// 		array(
// 		'allpinyin'=>$this->PinyinLogic->pinyinAll($list['sellername']),
// 		'firstpinyin'=>$this->PinyinLogic->getFirst($list['sellername'])
// 		)
// 		);
		
//      return;

		$list=logic('search')->GetList();
		foreach ( $list as $i => $value ) { //更新拼音码
			if (!$value['allpinyin'] && !$value['firstpinyin']){
// 				logic('search')->Update($value['id'],
// 					array(
// 					'allpinyin'=>$this->PinyinLogic->pinyinAll($value['sellername']),
// 					'firstpinyin'=>$this->PinyinLogic->getFirst($value['sellername'])
// 					)
// 				);
			}
		}
		echo '更新成！';
	}
}