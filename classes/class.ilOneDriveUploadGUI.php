<?php

use srag\Plugins\OneDrive\Input\srChunkedDirectFileUploadInputGUI;
use srag\Plugins\OneDrive\EventLog\EventLogger;

/**
 * Class ilOneDriveUploadGUI
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy ilOneDriveUploadGUI : ilObjCloudGUI
 */
class ilOneDriveUploadGUI extends ilCloudPluginUploadGUI
{

    const CMD_AFTER_UPLOAD = 'afterUpload';
    const CMD_ASYNC_GET_RESUMABLE_UPLOAD_URL = 'asyncGetResumableUploadUrl';
    const CMD_UPLOAD_ABORTED = 'uploadAborted';
    const CMD_UPLOAD_FAILED = 'uploadFailed';

    /**
     * onedrive forbids some special chars which will be removed here
     *
     * @param $name
     * @return string
     */
    protected function sanitizeFileName($name) : string
    {
        return str_replace(["/", "\\", "*", "<", ">", "?", ":", "|", "#", "%"], "-", $name);
    }

    public function asyncUploadFile()
    {
        global $DIC;
        $ilTabs = $DIC['ilTabs'];

        $ilTabs->activateTab("content");
        $this->initUploadForm();
        echo $this->form->getHTML();

        $_SESSION["cld_folder_id"] = $_POST["folder_id"];

        exit;
    }

    public function initUploadForm()
    {
        global $DIC;
        $ilCtrl = $DIC['ilCtrl'];
        $lng = $DIC['lng'];

        $this->form = new ilPropertyFormGUI();
        $this->form->setId("upload");
        $this->form->setMultipart(true);
        $this->form->setHideLabels();

        $file = new srChunkedDirectFileUploadInputGUI(
            $this->form,
            $this->getPluginHookObject(),
            $DIC->ctrl()->getLinkTargetByClass(ilCloudPluginUploadGUI::class, self::CMD_ASYNC_GET_RESUMABLE_UPLOAD_URL, "", true, false),
            $lng->txt("cld_upload_files")
        );
        $file->setAfterUploadJsCallback('il.OneDriveList.afterUpload');
        $file->setUploadFailedUrl($DIC->ctrl()->getLinkTargetByClass(
            ilCloudPluginUploadGUI::class, self::CMD_UPLOAD_FAILED, "", true, false));
        $file->setUploadAbortedUrl($DIC->ctrl()->getLinkTargetByClass(
            ilCloudPluginUploadGUI::class, self::CMD_UPLOAD_ABORTED, "", true, false));
        $file->setRequired(true);
        $this->form->addItem($file);

        $this->form->addCommandButton("uploadFiles", $lng->txt("upload"));
        $this->form->addCommandButton("cancelAll", $lng->txt("cancel"));

        $this->form->setTableWidth("100%");
        $this->form->setTitle($lng->txt("upload_files_title"));
//        $this->form->setTitleIcon(ilUtil::getImagePath('icon_file.gif'), $lng->txt('obj_file'));
        $this->form->setTitleIcon(ilUtil::getImagePath('icon_dcl_file.svg'), $lng->txt('obj_file'));

        $this->form->setTitle($lng->txt("upload_files"));
        $this->form->setFormAction($ilCtrl->getFormAction($this, "uploadFiles"));
        $this->form->setTarget("cld_blank_target");
        $this->form->setFormAction($ilCtrl->getFormAction(new ilCloudPluginUploadGUI($this), "uploadFiles"));
    }

    /**
     * @param $file_upload
     * @return stdClass
     * @deprecated currently not used due to chunked/resumable upload
     */
    function handleFileUpload($file_upload)
    {
        $_POST["title"] = exodFile::formatRename($_POST["title"], $file_upload["name"]);

        return parent::handleFileUpload($file_upload);
    }

    protected function afterUpload()
    {
        global $DIC;
        $name = filter_input(INPUT_POST, 'filename', FILTER_SANITIZE_STRING);
        $name = $this->sanitizeFileName($name);
        $parent_id = $_SESSION["cld_folder_id"];
        EventLogger::logUploadComplete(
            $DIC->user()->getId(),
            ilCloudFileTree::getFileTreeFromSession()->getNodeFromId($parent_id)->getPath() . '/' . $name
        );
    }

    protected function asyncGetResumableUploadUrl()
    {
        global $DIC;
        if (!$DIC->access()->checkAccess('upload', '', $_GET['ref_id']) ) {
            echo $this->getJsonError(403, "Permission Denied.");
            exit;
        }
        $name = filter_input(INPUT_POST, 'filename', FILTER_SANITIZE_STRING);
        $name_sanitized = $this->sanitizeFileName($name);
        $parent_id = $_SESSION["cld_folder_id"];
        $file_path = ilCloudFileTree::getFileTreeFromSession()->getNodeFromId($parent_id)->getPath() . '/' . $name_sanitized;
        try {
            $upload_url = $this->getService()->getClient()->getResumableUploadUrl($parent_id, $name_sanitized);
        } catch (ilCloudException $e) {
            EventLogger::logUploadFailed(
                $DIC->user()->getId(),
                $file_path,
                $e->getMessage()
            );
            echo $this->getJsonError(500, $e->getMessage());
            exit;
        }
        EventLogger::logUploadStarted(
            $DIC->user()->getId(),
            $file_path,
            $name_sanitized === $name ? '' : $name
        );
        http_response_code(200);
        echo $upload_url->toJson();
        exit;
    }

    protected function uploadFailed()
    {
        global $DIC;
        $name = filter_input(INPUT_POST, 'filename', FILTER_SANITIZE_STRING);
        $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
        $parent_id = $_SESSION["cld_folder_id"];
        $file_path = ilCloudFileTree::getFileTreeFromSession()->getNodeFromId($parent_id)->getPath() . '/' . $name;
        EventLogger::logUploadFailed(
            $DIC->user()->getId(),
            $file_path,
            $message ?? ''
        );
    }

    protected function uploadAborted()
    {
        global $DIC;
        $name = filter_input(INPUT_POST, 'filename', FILTER_SANITIZE_STRING);
        $parent_id = $_SESSION["cld_folder_id"];
        $file_path = ilCloudFileTree::getFileTreeFromSession()->getNodeFromId($parent_id)->getPath() . '/' . $name;
        EventLogger::logUploadAborted(
            $DIC->user()->getId(),
            $file_path
        );
    }

    protected function getJsonError(int $status_code, string $message) : string
    {
        http_response_code($status_code);
        return json_encode([
            "error" => [
                "code" => $status_code,
                "message" => $message,
            ],
        ]);
    }

    /**
     * @return ilOneDrive
     */
    public function getPluginObject() {
        return parent::getPluginObject();
    }


    /**
     * @return ilOneDriveService
     */
    public function getService() {
        return parent::getService();
    }

}
