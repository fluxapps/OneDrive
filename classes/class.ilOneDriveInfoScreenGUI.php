<?php

/**
 * Class ilOneDriveInfoScreenGUI
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class ilOneDriveInfoScreenGUI extends ilCloudPluginInfoScreenGUI
{
    public function getPluginInfo()
    {
        if ($message = $this->getAdminConfigObject()->getValue(exodConfig::F_INFO_MESSAGE)) {
            $this->info->addProperty($this->getPluginHookObject()->txt(exodConfig::F_INFO_MESSAGE), $message);
        }
        parent::getPluginInfo();
    }

}