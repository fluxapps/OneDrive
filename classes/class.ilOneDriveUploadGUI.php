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
        $finalFileName = $_POST["title"];
        $dotAmount = substr_count($_POST["title"], ".");

        if ($dotAmount == 0) {
            $path_parts = pathinfo($file_upload["name"]);
            $extension = $path_parts['extension'];
            $finalFileName .= "." . $extension;
        }

        $_POST["title"] = $finalFileName;

        return parent::handleFileUpload($file_upload);
    }
}