<?php
require_once('./Modules/Cloud/classes/class.ilCloudPluginInitGUI.php');

/**
 * Class ilOneDriveInitGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilOneDriveInitGUI extends ilCloudPluginInitGUI {

    /**
     *
     */
    public function beforeSetContent()
    {
        global $DIC;
        $DIC->ui()->mainTemplate()->addJavaScript("./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/js/OneDriveList.js");
        require_once 'Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/class.ilOneDriveActionListGUI.php';
        $rename_url = $DIC->ctrl()->getLinkTargetByClass([ilObjCloudGUI::class, ilCloudPluginActionListGUI::class], ilOneDriveActionListGUI::CMD_INIT_RENAME);
        $this->tpl_file_tree->setVariable(
            'PLUGIN_AFTER_CONTENT',
            '<script type="text/javascript">' .
            'il.OneDriveList = new OneDriveList("' . $rename_url . '");' .
            '</script>'
        );
    }
}
