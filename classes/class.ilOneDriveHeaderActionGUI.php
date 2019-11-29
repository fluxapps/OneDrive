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
        $perm = ilOneDrivePlugin::getInstance()->getPrefix() . '_asl_open_msoffice';
        $ref_id = $_GET["ref_id"];

        return $DIC->access()->checkAccessOfUser($user_id, $perm, "", $ref_id);
    }
}