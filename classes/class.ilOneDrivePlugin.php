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
	 * @return string
	 */
	public function getPluginName() {
		return self::PLUGIN_NAME;
	}
	/**
	 * @return exodAppBusiness
	 */
	public function getApp() {
		return new exodAppBusiness();
	}

	/**
	 * @param $a_var
	 *
	 * @return mixed|string
	 */
	public function txt($a_var, $original = false) {
		if ($original) {
			return parent::txt($a_var);
		}

		return ilDynamicLanguageOD::getInstance($this, ilDynamicLanguageOD::MODE_DEV)->txt($a_var);
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
