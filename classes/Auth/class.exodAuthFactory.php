<?php
require_once('class.exodAuth.php');
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/App/class.exodAppBusiness.php');

/**
 * Class exodAuthFactory
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodAuthFactory {

	/**
	 * @param exodApp $app
	 *
	 * @return exodAuth
	 */
	public static function getAuthInstance(exodApp $app) {
		return exodAuth::getInstance($app);
	}
}

?>
