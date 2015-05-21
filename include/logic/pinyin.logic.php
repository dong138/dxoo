<?php
/**
*
* 逻辑区：汉字转拼音类
* @Author : Cqx
* @Date   : 2014-03-16
*
*/
class PinyinLogic {
	
	//utf-8中国汉字集合
	private $ChineseCharacters;
	
	public function __construct(){
		if( empty($this->ChineseCharacters) ){
			$this->ChineseCharacters = file_get_contents('data/chinesecharacters.dat');
		}
	}
	
	/**
	 * @功能: 最全的PHP汉字转拼音函数 （共25961字，包括 20902基本字 + 5059生僻字）
	 * @版本: 1.0.0
	 */
	function pinyinAll($str, $isfirst = false) {
		static $pinyins;
		
		$str = trim ( $str );
		$len = strlen ( $str );
		if ($len < 3)
			return $str;
		
		if (! isset ( $pinyins )) {
			$data = $this->ChineseCharacters;
			$a1 = explode ( '|', $data );
			$pinyins = array ();
			foreach ( $a1 as $v ) {
				$a2 = explode ( ':', $v );
				$pinyins [$a2 [0]] = $a2 [1];
			}
		}
		
		$rs = '';
		for($i = 0; $i < $len; $i ++) {
			$o = ord ( $str [$i] );
			if ($o < 0x80) {
				if (($o >= 48 && $o <= 57) || ($o >= 97 && $o <= 122)) {
					$rs .= $str [$i]; // 0-9 a-z
				} elseif ($o >= 65 && $o <= 90) {
					$rs .= strtolower ( $str [$i] ); // A-Z
				} else {
					$rs .= '_';
				}
			} else {
				$z = $str [$i] . $str [++ $i] . $str [++ $i];
				if (isset ( $pinyins [$z] )) {
					$rs .= $isfirst ? $pinyins [$z] [0] : $pinyins [$z];
				} else {
					$rs .= '_';
				}
			}
		}
		return $rs;
	}
	function getFirst($str) {
		static $pinyins;
		
		$str = trim ( $str );
		$len = strlen ( $str );
		if ($len < 3)
			return $str;
		
		if (! isset ( $pinyins )) {
			$data = $this->ChineseCharacters;
			$a1 = explode ( '|', $data );
			$pinyins = array ();
			foreach ( $a1 as $v ) {
				$a2 = explode ( ':', $v );
				$pinyins [$a2 [0]] = $a2 [1];
			}
		}
		
		$rs = '';
		for($i = 0; $i < $len; $i ++) {
			$o = ord ( $str [$i] );
			if ($o < 0x80) {
				if (($o >= 48 && $o <= 57) || ($o >= 97 && $o <= 122)) {
					$rs .= $str [$i]; // 0-9 a-z
				} elseif ($o >= 65 && $o <= 90) {
					$rs .= strtolower ( $str [$i] ); // A-Z
				} else {
					$rs .= '_';
				}
			} else {
				$z = $str [$i] . $str [++ $i] . $str [++ $i];
				if (isset ( $pinyins [$z] )) {
					$rs .= $isfirst ? substr($pinyins [$z] [0],0,1) : substr($pinyins [$z],0,1);
				} else {
					$rs .= '_';
				}
			}
		}
		return $rs;
	}
}