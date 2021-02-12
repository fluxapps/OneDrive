<?php

use srag\Plugins\OneDrive\EventLog\EventLogger;
use srag\Plugins\OneDrive\EventLog\ObjectType;

/**
 * Class ilOneDriveDeleteGUI
 * @author Theodor Truffer <tt@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy ilOneDriveDeleteGUI : ilObjCloudGUI
 */
class ilOneDriveDeleteGUI extends ilCloudPluginDeleteGUI
{
    public function deleteItem()
    {
        global $DIC;
        $tpl = $DIC['tpl'];
        $lng = $DIC['lng'];

        $response = new stdClass();
        $response->success = null;
        $response->message = null;

        if (true) {
            try {
                $file_tree = ilCloudFileTree::getFileTreeFromSession();
                $node = $file_tree->getNodeFromId($_POST["id"]);
                $file_tree->deleteFromService($node->getId());
                $response->message = $tpl->getMessageHTML($lng->txt("cld_file_deleted"), "success");
                $response->success = true;
                EventLogger::logObjectDeleted(
                    $DIC->user()->getId(),
                    $node->getPath(),
                    ObjectType::fromExodItem(exodItemCache::get($node->getId()))
                );
            } catch (Exception $e) {
                $response->message = $tpl->getMessageHTML($e->getMessage(), "failure");
            }
        }
        echo "<script type='text/javascript'>window.parent.il.CloudFileList.afterDeleteItem(" . ilJsonUtil::encode($response)
            . ");</script>";
        exit;
    }

    public function initDeleteItem()
    {
        global $DIC;
        parent::initDeleteItem();
        $this->gui->setFormAction($DIC->ctrl()->getFormActionByClass(ilCloudPluginDeleteGUI::class));
    }

}
