<?php

/**
 * 逻辑区：排序管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name sort.logic.php
 * @version 1.0
 */

class SortManageLogic
{
	
	private $filterPools = array(
		'default' => array(
			'name' => '默认',
			'sql' => 'p.updatetime DESC, p.id DESC' //20150326 cqx  old:p.order DESC
		),
		'recent' => array(
			'name' => '最新',
			'title' => '按照上架时间显示最新',
			'dir' => 'down',
			'sql' => 'p.begintime DESC'
		),
		'sells' => array(
			'name' => '销量',
			'title' => '按照产品销量显示热卖产品',
			'dir' => 'down',
			'sql' => 'p.sells_count+p.virtualnum DESC'
		),
		'price-asc' => array(
			'name' => '价格(最低)',
			'title' => '按照价格从低到高显示',
			'dir' => 'up',
			'sql' => 'p.nowprice ASC'
		),
		'price-desc' => array(
			'name' => '价格(最高)',
			'title' => '按照价格从高到低显示',
			'dir' => 'down',
			'sql' => 'p.nowprice DESC'
		)
	);
	public $sellerPools = array(
		'default' => array(
			'name' => '最新',
			'title' => '按照添加时间显示最新',
			'dir' => 'down',
			'sql' => ' s.id DESC '
		),
		'sells' => array(
			'name' => '销量',
			'title' => '按照产品销量显示商家',
			'dir' => 'down',
			'sql' => ' s.successnum DESC ',
		),
	);
	public $adplayPools = array(
		'time' => array(
			'name' => '最新',
			'title' => '按照添加时间显示最新',
			'dir' => 'down',
			'sql' => ' id DESC '
		),
		'hot' => array(
			'name' => '最热',
			'title' => '按照玩的最多显示广告',
			'dir' => 'down',
			'sql' => ' times_played DESC ',
		),
	);
	
	public function filter_pools()
	{
		return $this->filterPools;
	}
	
	public function set_filter_pool($key, $config)
	{
		$this->filterPools[$key] = $config;
	}
	
	public function product_navigate()
	{
		if(INDEX_DEFAULT === true){
			return '';
		}
		$sortKey = $this->get_sort_key();
		$sorts = $this->filterPools;
		foreach ($sorts as $k => $data)
		{
			$sorts[$k]['url'] = logic('url')->create('product', array('sort' => $k));
			if ($sortKey == $k)
			{
				$sorts[$k]['selected'] = true;
			}
		}
		include handler('template')->file('home_sort_navigate');
	}
	
	public function product_sql_filter()
	{
		return $this->filterPools[$this->get_sort_key()]['sql'];
	}
	
	private function get_sort_key()
	{
		$sk = get('sort', 'string');
		return isset($this->filterPools[$sk]) ? $sk : 'default';
	}
	
	private function seller_sort_key()
	{
		$sk = get('sort', 'string');
		return (isset($this->sellerPools[$sk]) ? $sk : 'default');
	}
	public function seller_sql_filter()
	{		
		return $this->sellerPools[$this->seller_sort_key()]['sql'];
	}
	public function seller_navigate()
	{
		$sortKey = $this->seller_sort_key();
		$sorts = $this->sellerPools;
		foreach ($sorts as $k => $data)
		{
			$sorts[$k]['url'] = logic('url')->create('seller', array('sort' => $k));
			if ($sortKey == $k)
			{
				$sorts[$k]['selected'] = true;
			}
		}
		include handler('template')->file('seller_sort_navigate');
	}
    
	private function adplay_sort_key()
	{
		$sk = get('sort', 'string');
		return (isset($this->adplayPools[$sk]) ? $sk : 'time');
	}
	public function adplay_navigate()
	{
		$sortKey = $this->adplay_sort_key();
		$sorts = $this->adplayPools;
		foreach ($sorts as $k => $data)
		{
			$sorts[$k]['url'] = logic('url')->create_url('?mod=adplay&', 'adplay', array('sort' => $k));
			if ($sortKey == $k)
			{
				$sorts[$k]['selected'] = true;
			}
		}
		include handler('template')->file('adplay_sort_navigate');
	}
}

?>