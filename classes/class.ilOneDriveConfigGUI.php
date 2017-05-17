<?php
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/App/class.exodApp.php');
require_once('./Modules/Cloud/classes/class.ilCloudPluginConfigGUI.php');

/**
 * Class ilOneDriveConfigGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ilOneDriveConfigGUI extends ilCloudPluginConfigGUI {

	const IL_CHECKBOX_INPUT_GUI = 'ilCheckboxInputGUI';
	const IL_TEXT_INPUT_GUI = 'ilTextInputGUI';
	const IL_NUMBER_INPUT_GUI = 'ilNumberInputGUI';
	const IL_SELECT_INPUT_GUI = 'ilSelectInputGUI';


	/**
	 * @return array
	 */
	public function getFields() {
		return array(
			exodConfig::F_CLIENT_TYPE    => array(
				'type'        => self::IL_SELECT_INPUT_GUI,
				'options'     => array(
					exodApp::TYPE_BUSINESS => 'OneDrive Business',
					exodApp::TYPE_PUBLIC   => 'OneDrive',
				),
				'info'        => 'config_info_client_type',
				'subelements' => null,
			),
			exodConfig::F_CLIENT_ID      => array(
				'type'        => self::IL_TEXT_INPUT_GUI,
				'info'        => 'config_info_client_id',
				'subelements' => null,
			),
			exodConfig::F_CLIENT_SECRET  => array(
				'type'        => self::IL_TEXT_INPUT_GUI,
				'info'        => 'config_info_client_secret',
				'subelements' => null,
			),
			exodConfig::F_TENANT_NAME    => array(
				'type'        => self::IL_TEXT_INPUT_GUI,
				'info'        => 'config_info_tenant_name',
				'subelements' => null,
			),
			exodConfig::F_TENANT_ID      => array(
				'type'        => self::IL_TEXT_INPUT_GUI,
				'info'        => 'config_info_tenant_id',
				'subelements' => null,
			),
			exodConfig::F_IP_RESOLVE_V_4 => array(
				'type'        => self::IL_CHECKBOX_INPUT_GUI,
				'info'        => 'config_info_ip_resolve_v4',
				'subelements' => null,
			),
			exodConfig::F_SSL_VERSION    => array(
				'type'        => self::IL_SELECT_INPUT_GUI,
				'options'     => array(
					CURL_SSLVERSION_DEFAULT => 'Standard',
					CURL_SSLVERSION_TLSv1   => 'TLSv1',
					CURL_SSLVERSION_SSLv2   => 'SSLv2',
					CURL_SSLVERSION_SSLv3   => 'SSLv3',
				),
				'info'        => 'config_info_ssl_version',
				'subelements' => null,
			)
		);
	}


	public function initConfigurationForm() {
		global $lng, $ilCtrl;

		include_once("./Services/Form/classes/class.ilPropertyFormGUI.php");
		$this->form = new ilPropertyFormGUI();

		foreach ($this->fields as $key => $item) {
			$field = new $item["type"]($this->plugin_object->txt($key), $key);
			if ($item["type"] == self::IL_SELECT_INPUT_GUI) {
				$field->setOptions($item['options']);
			}
			$field->setInfo($this->plugin_object->txt($item["info"]));
			if (is_array($item["subelements"])) {
				foreach ($item["subelements"] as $subkey => $subitem) {
					$subfield = new $subitem["type"]($this->plugin_object->txt($key . "_"
					                                                           . $subkey), $key
					                                                                       . "_"
					                                                                       . $subkey);
					$subfield->setInfo($this->plugin_object->txt($subitem["info"]));
					$field->addSubItem($subfield);
				}
			}

			$this->form->addItem($field);
		}

		$this->form->addCommandButton("save", $lng->txt("save"));

		$this->form->setTitle($this->plugin_object->txt("configuration"));
		$this->form->setFormAction($ilCtrl->getFormAction($this));

		return $this->form;
	}
}

