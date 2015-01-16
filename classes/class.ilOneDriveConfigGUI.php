<?php

require_once('./Modules/Cloud/classes/class.ilCloudPluginConfigGUI.php');

/**
 * Class ilOneDriveConfigGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ilOneDriveConfigGUI extends ilCloudPluginConfigGUI {

	/**
	 * @return array
	 */
	public function getFields() {
		return array(
			'app_name' => array( 'type' => 'ilTextInputGUI', 'info' => 'config_info_app_name', 'subelements' => NULL ),
			'app_key' => array( 'type' => 'ilTextInputGUI', 'info' => 'config_info_app_key', 'subelements' => NULL ),
			'app_secret' => array( 'type' => 'ilTextInputGUI', 'info' => 'config_info_app_secret', 'subelements' => NULL ),
			/*'config_max_file_size' => array( 'type' => 'ilCheckboxInputGUI', 'info' => 'config_info_config_max_upload_size', 'subelements' => NULL ),
			'default_max_file_size' => array( 'type' => 'ilNumberInputGUI', 'info' => 'config_info_default_max_upload_size', 'subelements' => NULL ),
			'default_allow_public_links' => array(
				'type' => 'ilCheckboxInputGUI',
				'info' => 'default_info_config_allow_public_links',
				'subelements' => array(
					'config_allow_public_links' => array(
						'type' => 'ilCheckboxInputGUI',
						'info' => 'config_default_config_allow_public_links_info',
						'subelements' => NULL
					)
				)
			),*/
		);
	}
}

?>