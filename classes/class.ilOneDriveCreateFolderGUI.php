<?php

require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Client/class.exodPath.php');

/**
 * Class ilOneDriveCreateFolderGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy ilOneDriveCreateFolderGUI : ilObjCloudGUI
 */
class ilOneDriveCreateFolderGUI extends ilCloudPluginCreateFolderGUI {

    public function initCreateFolder()
    {
        global $DIC;
        $ilCtrl = $DIC['ilCtrl'];
        parent::initCreateFolder();
        $this->form->setFormAction($ilCtrl->getFormAction(new ilCloudPluginCreateFolderGUI($this)));
    }


    public function createFolder()
    {
        try {
            exodPath::validateBasename($_POST["folder_name"]);
            parent::createFolder();
        } catch (Exception $e) {
            echo "<script language='javascript' type='text/javascript'>window.parent.il.CloudFileList.afterCreateFolder(" . ilJsonUtil::encode($e->getMessage()) . ");</script>";
            exit;
        }
    }
}