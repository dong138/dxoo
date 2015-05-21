<?php

/**
 * 广告位列表
 * @package ad
 * @name ad.list.php
 * @version 1.0
 */

return array(
	'howdo' => array (
		'name' => '首页单图广告位',
		'enabled' => false,
		'config' => array (
			'image' => '',
			'linker' => 'javascript:;',
			'close_allow' => 'no',
			'auto_hide_time' => '5',
			'auto_hide_allow' => 'on',
			'reshow_delay_time' => '1'
		)
	),
	'howdfoot' => array (
			'name' => '手机登陆foot',
			'enabled' => false,
			'config' => array (
			'image' => '',
			'linker' => 'javascript:;',
			'close_allow' => 'no',
			'auto_hide_time' => '5',
			'auto_hide_allow' => 'on',
			'reshow_delay_time' => '1'
		)
	),
	'howdbody' => array (
			'name' => '手机登陆body',
			'enabled' => false,
			'config' => array (
					'image' => '',
					'linker' => 'javascript:;',
					'close_allow' => 'no',
					'auto_hide_time' => '5',
					'auto_hide_allow' => 'on',
					'reshow_delay_time' => '1'
			)
	),
	'howdslide' => array (
			'name' => '手机登陆slide',
			'enabled' => false,
			'config' => array (
			'image' => '',
			'linker' => 'javascript:;',
			'close_allow' => 'no',
			'auto_hide_time' => '5',
			'auto_hide_allow' => 'on',
			'reshow_delay_time' => '1'
		)
	),
	'howdot' => array(
		'name' => '首页两图广告位',
		'enabled' => false,
		'config' => array(
			'list' => array (
			)
		)
	),
	'howdof' => array(
		'name' => '首页四图广告位',
		'enabled' => false,
		'config' => array(
			'list' => array (
			)
		)
	),
	'howdos' => array(
		'name' => '首页六图广告位',
		'enabled' => false,
		'config' => array(
			'list' => array (
			)
		)
	),
	'howdom' => array(
		'name' => '首页多图轮换广告位',
		'enabled' => false,
		'config' => array(
			'list' => array (
			),
			'dsp' => array (
				'time' => '3',
				'switchPath' => 'left',
				'showText' => 'false',
				'showButtons' => 'true',
			),
		)
	),
	'howparallel' => array(
		'name' => '首页对联广告位',
		'enabled' => false,
		'config' => array(
			'list' => array (
			)
		)
	),
);

?>