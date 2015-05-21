<?php

/**
 * ZLOG-SYS：操作父类
 * @package zlog
 * @name master.zlog.php
 * @version 1.0
 */

class iMasterZLOG
{
	protected $zlogType = '';
	protected function zlogCreate($index, $name, $extra = '')
	{
		$data = array(
			'type' => $this->zlogType,
			'uid' => user()->get('id'),
			'uip' => ip2long(client_ip()),
			'index' => $index,
			'name' => $name,
			'extra' => $extra,
			'time' => time()
		);
		return dbc(DBCMax)->insert('zlog')->data($data)->done();
	}
}

?>