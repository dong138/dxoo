<?php
/**
 * @package php
 * @name tag.mod.php
 * @date 2014-09-01 17:24:23
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
		exit('ok');
	}

	function save()
	{
		if (true === ENC_IS_GBK && ($_REQUEST['in_ajax'] || true === X_IS_AJAX)) {
			$_POST = array_iconv('UTF-8', 'GBK', $_POST);
		}
		logic('tag')->save();
	}

	function view()
	{
		$product_id = get('product_id', 'int');
		//是不是添加产品
		$addf = get('addf', 'txt');
		if($addf){
			$eager = false;
		}else{
			$eager = true;
		}

		$ret = '';
		$list = logic('product_tag')->get_list($product_id, $eager);
		if(is_array($list) && count($list)) {
			foreach($list as $r) {
				$ret .= " <input type='hidden' name='tag_ids[]' value='{$r['id']}' /> <span title='{$r['desc']}'>{$r['name']}</span> ";
			}
		}

		exit($ret ? $ret : '暂时还没有设置标签');
	}

	function delete()
	{
		$product_id = get('product_id', 'int');
		$tag_id = get('tag_id', 'int');

		logic('product_tag')->delete($product_id, $tag_id);
	}

}
?>