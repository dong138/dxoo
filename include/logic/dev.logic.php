<?php

/**
 * 逻辑区：开发管理
 * @package logic
 * @name dev.logic.php
 * @version 1.1
 */
class DevelopmentLogic {
	public function debugSwitch($do, $class, $data = null) {
		if ($do == 'get') {
			return $this->debugSwitch_get ( $class );
		} else {
			$debug = $data == 'true' ? true : false;
			return $this->debugSwitch_set ( $class, $debug );
		}
	}
	private function debugSwitch_get($class) {
		return is_file ( DATA_PATH . 'debug.' . $class . '.signal' ) ? true : false;
	}
	private function debugSwitch_set($class, $debug) {
		$file = DATA_PATH . 'debug.' . $class . '.signal';
		if ($debug) {
			return file_put_contents ( $file, '' );
		} else {
			return unlink ( $file );
		}
	}
}

?>