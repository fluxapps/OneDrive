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
			'tenant' => array( 'type' => 'ilTextInputGUI', 'info' => 'config_info_tenant', 'subelements' => NULL ),
			'tenant_id' => array( 'type' => 'ilTextInputGUI', 'info' => 'config_info_tenant_id', 'subelements' => NULL ),
			'ssl_v3' => array( 'type' => 'ilCheckboxInputGUI'),
			'ip_resolve_v4' => array( 'type' => 'ilCheckboxInputGUI'),
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