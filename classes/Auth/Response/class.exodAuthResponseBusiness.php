<?php
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Auth/Response/class.exodAuthResponseBase.php');

/**
 *
 * Class exodAuthResponseBusiness
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class exodAuthResponseBusiness extends exodAuthResponseBase {

	/**
	 * @return exodAuthResponseBusiness
	 */
	public static function getInstance() {
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
