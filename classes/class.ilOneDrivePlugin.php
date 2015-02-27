<?php
require_once('./Modules/Cloud/classes/class.ilCloudHookPlugin.php');
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/class.ilDynamicLanguage.php');

/**
 * Class ilOneDrivePlugin
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilOneDrivePlugin extends ilCloudHookPlugin implements ilDynamicLanguageInterfaceOD {

	const PLUGIN_NAME = 'OneDrive';
	/**
	 * @var exodAppBusiness
	 */
	protected static $app_instance;

	/**
	 * @return string
	 */
	public function getPluginName() {
		return self::PLUGIN_NAME;
	}


	/**
	 * @param exodBearerToken $exodBearerToken
	 *
	 * @return exodAppBusiness
	 */
	public function getExodApp(exodBearerToken $exodBearerToken) {
		$exodTenant = new exodTenant();
		$exodTenant->setTenantId('***REMOVED***');
		$exodTenant->setTenantName('***REMOVED***');

		$client_id = '***REMOVED***';
		$client_secret = '***REMOVED***';

		$exodAppBusiness = exodAppBusiness::getInstance($exodBearerToken, $client_id, $client_secret, $exodTenant);

		return $exodAppBusiness;
	}


	/**
	 * @var ilOneDrivePlugin
	 */
	protected static $instance;


	/**
	 * @return ilOneDrivePlugin
	 */
	public static function getInstance() {
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * @return string
	 */
	public function getCsvPath() {
		return './Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/lang/lang.csv';
	}


	/**
	 * @return string
	 */
	public function getAjaxLink() {
		return NULL;
	}
}

?>
