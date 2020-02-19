<?php

/**
 * Class ilOneDriveFileTreeGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy ilOneDriveFileTreeGUI : ilObjCloudGUI
 */
class ilOneDriveFileTreeGUI extends ilCloudPluginFileTreeGUI
{

    public function getItemHtml(ilCloudFileNode $node, ilObjCloudGUI $gui_class, $delete_files = false, $delete_folder = false, $download = false)
    {
        global $DIC;
        $ilCtrl = $DIC['ilCtrl'];

        $item = new ilTemplate("tpl.container_list_item.html", true, true, "Services/Container/");

        $action_list_gui = ilCloudConnector::getActionListGUIClass($this->getService());
        $item->setVariable("COMMAND_SELECTION_LIST", $action_list_gui->getSelectionListItemsHTML($delete_files, $delete_folder, $node));

        $item->setVariable("DIV_CLASS", "ilContainerListItemOuter");
        $item->touchBlock("d_1");

        include_once('./Services/Calendar/classes/class.ilDate.php');
        $modified = ilDatePresentation::formatDate(new ilDateTime($node->getModified(), IL_CAL_UNIX));

        if ($node->getIconPath() != "") {
            $item->setVariable("SRC_ICON", $node->getIconPath());
        }

        // Folder with content
        if ($node->getIsDir()) {
            if ($node->getIconPath() == "") {
                $item->setCurrentBlock('icon_link_s');
                $item->setVariable('ICON_HREF', $this->getLinkToFolder($node));
                $item->parseCurrentBlock();
                $item->setVariable("SRC_ICON", ilUtil::getImagePath('icon_dcl_fold.svg'));
                $item->touchBlock('icon_link_e');
            }
            $item->setVariable("TXT_TITLE_LINKED", $this->basenameify($node->getPath()));
            $item->setVariable("HREF_TITLE_LINKED", $this->getLinkToFolder($node));
        } // File
        else {
            if ($node->getIconPath() == "") {
                //				$item->setVariable("SRC_ICON", "./Modules/Cloud/templates/images/icon_file_b.png");
                $item->setVariable("SRC_ICON", ilUtil::getImagePath('icon_dcl_file.svg'));
            }

            $item->setVariable("TXT_DESC",
                $this->formatBytes($node->getSize()) . "&nbsp;&nbsp;&nbsp;" . $modified);
            if ($download) {
                $item->setVariable("TXT_TITLE_LINKED", $this->basenameify($node->getPath()));
                $item->setVariable("HREF_TITLE_LINKED", $ilCtrl->getLinkTarget($gui_class, "getFile") . "&id=" . $node->getId());
            } else {
                $item->setVariable("TXT_TITLE", $this->basenameify($node->getPath()));
            }
        }

        $this->setItemVariablePlugin($item, $node);

        return $item->get();
    }


    public function getLocatorHtml(ilCloudFileNode $node)
    {
        static $ilLocator;

        if ($node == $this->getFileTree()->getRootNode()) {
            $ilLocator = new ilLocatorGUI();
            $ilLocator->addItem($this->getPluginObject()->getCloudModulObject()->getTitle(), ilCloudPluginFileTreeGUI::getLinkToFolder($node));
        } else {
            $this->getLocatorHtml($this->getFileTree()->getNodeFromId($node->getParentId()));
            $ilLocator->addItem($this->basenameify($node->getPath()), $this->getLinkToFolder($node));
        }

        return "<DIV class='xcld_locator' id='xcld_locator_" . $node->getId() . "'>" . $ilLocator->getHTML() . "</DIV>";
    }


    /**
     * basename() replacement, as basename doesn't properly work with umlauts at the beginning
     *
     * @param string $path
     *
     * @return string
     */
    private function basenameify($path) {
        $pathContents = explode("/", $path);
        return $pathContents[count($pathContents) - 1];
    }
}