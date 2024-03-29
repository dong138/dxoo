<?php
/**
 * @package php
 * @name howdom.function.php
 * @date 2014-09-01 17:24:22
 */
 




function ad_config_save_parser_howdom(&$data)
{
	if (count($data['list']) < 1) return;
	$orders = array();
	$ic = 0;
	foreach ($data['list'] as $id => $cfg)
	{
		$orders[$id] = $cfg['order'];
		$fid = 'file_'.$id;
		if (isset($_FILES[$fid]) && is_array($_FILES[$fid]))
		{
			logic('upload')->Save($fid, ROOT_PATH.$data['list'][$id]['image']);
		}
		$ic++;
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
