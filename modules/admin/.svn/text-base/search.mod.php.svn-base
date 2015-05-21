<?php
class ModuleObject extends MasterObject {
	function ModuleObject($config) {
		$this->MasterObject ( $config );
		Load::logic ( 'Pinyin' );
		$this->PinyinLogic = new PinyinLogic ();
		$runCode = Load::moduleCode ( $this, $this->Code );
		$this->$runCode ();
	}

	function ajaxSeller(){
		$keyword= get('q','txt');
		$cityid= get('cityid','int');
		$filter=1;
		$cityid && $filter.= ' and area='.$cityid;
		$filter.=' and (id like "%'.$keyword.'%" or sellername like "%'.$keyword.'%" or allpinyin like "%'.$keyword.'%" or firstpinyin like "%'.$keyword.'%")';
		$list=logic('search')->GetLike($filter,'seller','id,sellername');
		echo jsonEncode($list);
	}
	
	function ajaxPinyin(){
		$title= get('title','txt');
		$title || exit(jsonEncode(array('status'=>'0')));
		$list=array(
				'firstpinyin'=>$this->PinyinLogic->getFirst($title),
				'allpinyin'=>$this->PinyinLogic->pinyinAll($title),
				
		);
		echo jsonEncode($list);
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
		
	    return;

		$list=logic('search')->GetList();
		foreach ( $list as $i => $value ) { //更新拼音码
			if (!$value['allpinyin'] && !$value['firstpinyin']){
				logic('search')->Update($value['id'],
					array(
					'allpinyin'=>$this->PinyinLogic->pinyinAll($value['sellername']),
					'firstpinyin'=>$this->PinyinLogic->getFirst($value['sellername'])
					)
				);
			}
		}
		echo '更新成！';
	}
}