<?php

use srag\Plugins\OneDrive\Input\srChunkedDirectFileUploadInputGUI;

require_once('Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/vendor/autoload.php');
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
        srChunkedDirectFileUploadInputGUI::loadJavaScript($DIC->ui()->mainTemplate());
        $rename_url = $DIC->ctrl()->getLinkTargetByClass([ilObjCloudGUI::class, ilCloudPluginActionListGUI::class], ilOneDriveActionListGUI::CMD_INIT_RENAME, "", false, false);
        $after_upload_url = $DIC->ctrl()->getLinkTargetByClass([ilObjCloudGUI::class, ilCloudPluginUploadGUI::class], ilOneDriveUploadGUI::CMD_AFTER_UPLOAD, "", false, false);
        $this->tpl_file_tree->setVariable(
            'PLUGIN_AFTER_CONTENT',
            '<script type="text/javascript">' .
            'il.OneDriveList = new OneDriveList("' . $rename_url . '", "' . $after_upload_url . '");' .
            '</script>'
        );
    }
}
