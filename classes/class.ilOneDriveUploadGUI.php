<?php

/**
 * Class ilOneDriveUploadGUI
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy ilOneDriveUploadGUI : ilObjCloudGUI
 */
class ilOneDriveUploadGUI extends ilCloudPluginUploadGUI
{

    public function initUploadForm()
    {
        global $DIC;
        $ilCtrl = $DIC['ilCtrl'];
        parent::initUploadForm();
        $this->form->setFormAction($ilCtrl->getFormAction(new ilCloudPluginUploadGUI($this), "uploadFiles"));
    }


    function handleFileUpload($file_upload)
    {
        $_POST["title"] = exodFile::formatRename($_POST["title"], $file_upload["name"]);

        return parent::handleFileUpload($file_upload);
    }
}