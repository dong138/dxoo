<?php

/**
 * 界面支持：产品展示
 * @package UserInterface
 * @name igos.ui.php
 * @version 1.0
 */
class iGOSUI {
	public function load($product) {
		$style = ini ( 'ui.igos.style' );
		$style || $style = 'lashou';
		if (! in_array ( $style, array (
				'lashou',
				'meituan' 
		) )) {
			$style = 'lashou';
		}
		if (INDEX_DEFAULT === true) {
			include handler ( 'template' )->file ( '@html/igos/' . $style . '/default' );
		} else {
			include handler ( 'template' )->file ( '@html/igos/' . $style . '/index' );
		}
	}
}

?>