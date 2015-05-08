<?php
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Auth/Response/class.exodAuthResponseBase.php');

/**
 *
 * Class exodAuthResponsePublic
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class exodAuthResponsePublic extends exodAuthResponseBase {

	/**
	 * @return int
	 */
	public function getExpiresOn() {
		return (int)$this->getExpiresIn() + time();
	}


	/**
	 * @return exodAuthResponse
	 */
	public static function getInstance() {
		if (! isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}


?>
