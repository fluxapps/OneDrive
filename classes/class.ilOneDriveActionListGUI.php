<?php
require_once('./Modules/Cloud/classes/class.ilCloudPluginActionListGUI.php');

/**
 * Class ilOneDriveActionListGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilOneDriveActionListGUI extends ilCloudPluginActionListGUI {

	/**
	 * @var ilAdvancedSelectionListGUI
	 */
	public $selection_list;


	/**
	 * @return bool
	 */
	protected function addItemsAfter() {
		// $this->selection_list->addItem('Mein Link');
		return true;
	}
}

?>
