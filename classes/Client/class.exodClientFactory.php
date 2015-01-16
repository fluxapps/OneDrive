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
	 * @param exodApp         $app
	 * @param exodBearerToken $token
	 *
	 * @return exodClientBusiness
	 */
	public static function getClientInstance(exodApp $app, exodBearerToken $token) {
		switch ($app->getType()) {
			case exodApp::TYPE_BUSINESS:
				return new exodClientBusiness($app, $token);
		}
	}
}

?>
