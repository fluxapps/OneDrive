<?php
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/App/class.exodAppBusiness.php');
require_once('class.exodClientBusiness.php');

/**
 * Class exodClientFactory
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodClientFactory {

	/**
	 * @param exodApp $exodApp
	 *
	 * @return exodClientBusiness|exodClientPublic
	 */
	public static function getInstance(exodApp $exodApp) {
		switch ($exodApp->getType()) {
			case exodApp::TYPE_BUSINESS:
				return new exodClientBusiness($exodApp);
			default:
				return new exodClientPublic($exodApp);
		}
	}
}
