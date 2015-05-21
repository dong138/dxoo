<?php
/**
 * @package php
 * @name howdfoot.function.php
 * @date 2014-09-01 17:24:22
 */
 




function ad_config_save_parser_howdfoot(&$data)
{
	if (count($data['list']) < 1) return;
	$orders = array();
	foreach ($data['list'] as $id => $cfg)
	{
		$orders[$id] = $cfg['order'];
		$fid = 'file_'.$id;
		if (isset($_FILES[$fid]) && is_array($_FILES[$fid])){
			logic('upload')->Save($fid, ROOT_PATH.$data['list'][$id]['image']);
		}
	}
	arsort($orders);
	$dn = array();
	foreach ($orders as $id => $order)
	{
		$dn[$id] = $data['list'][$id];
	}
	$data['list'] = $dn;
	$data['fu'] = true;
}

?>
