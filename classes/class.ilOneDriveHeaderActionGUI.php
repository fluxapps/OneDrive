<?php

/**
 * Class ilOneDriveHeaderActionGUI
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy ilOneDriveHeaderActionGUI : ilObjCloudGUI
 */
class ilOneDriveHeaderActionGUI extends ilCloudPluginHeaderActionGUI
{

    public function addCustomHeaderAction(ilObjectListGUI $lg)
    {
        if ($this->checkOpenInOfficePerm()) {
            $lg->addCustomCommand(
                $this->getPluginObject()->getPublicLink(),
                ilOneDrivePlugin::getInstance()->getPrefix() . "_open_in_onedrive",
                "_blank"
            );
        }
    }


    /**
     * @return bool
     */
    protected function checkOpenInOfficePerm()
    {
        global $DIC;
        $user = $DIC->user();
        $user_id = $user->getId();
        $ref_id = $_GET["ref_id"];

        return $DIC->access()->checkAccessOfUser($user_id, 'edit_in_online_editor', "", $ref_id);
    }


    /**
     * Add custom commands to the object on the repository view
     *
     * @return array
     */
    public function getCustomListActions()
    {
        $customActionList = [];

        // If authenticated and a share link has been created
        if (ilObjCloudAccess::checkAuthStatus($this->getPluginObject()->getObjId()) && !empty($this->getPluginObject()->getPublicLink())) {
            $customActionList = [
                [
                    "permission" => "read",
                    "cmd"        => "fileManagerLaunch",
                    "lang_var"   => ilOneDrivePlugin::getInstance()->getPrefix() . "_open_in_onedrive",
                    "custom_url" => $this->getPluginObject()->getPublicLink(),
                ],
            ];
        }

        return $customActionList;
    }
}
