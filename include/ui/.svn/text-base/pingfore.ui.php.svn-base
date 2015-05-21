<?php

/**
 * 界面支持：前端心跳包
 * @package UserInterface
 * @name pingfore.ui.php
 * @version 1.0
 */
class PingforeUI {
	public function html() {
		$isCheck = rand ( 1, 13 );
		if ($isCheck == 13) {
			$lcks = array (
					'logic.push.running.mix',
					'logic.push.running.sms',
					'logic.push.running.mail' 
			);
			foreach ( $lcks as $i => $lck ) {
				$this->doCheckLockFile ( $lck );
			}
		}
		return ui ( 'loader' )->js ( '@pingfore' );
	}
	private function doCheckLockFile($name) {
		$file = driver ( 'lock' )->file ( $name );
		if (! is_file ( $file ))
			return;
		$mtime = filemtime ( $file );
		if (time () - $mtime > 60) {
			unlink ( $file );
		}
	}
}

?>