<?php
require_once('./Modules/Cloud/classes/class.ilCloudPluginActionListGUI.php');
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Client/Item/class.exodItemCache.php');
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/class.ilOneDrivePlugin.php');
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Auth/Mapping/OneDriveEmailBuilderFactory.php');

/**
 * Class ilOneDriveActionListGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 *
 * @ilCtrl_IsCalledBy ilOneDriveActionListGUI : ilObjCloudGUI
 */
class ilOneDriveActionListGUI extends ilCloudPluginActionListGUI {

    const CMD_INIT_RENAME = 'initRename';
    const CMD_RENAME = 'rename';

    const CMD_OPEN_IN_OFFICE_ONLINE = 'openInOfficeOnline';
    const GET_ID = 'id';

    const POST_TITLE = 'title';
    const ITEM_ID = 'item_id';

    /**
	 * @var ilAdvancedSelectionListGUI
	 */
	public $selection_list;


    /**
     * @return bool
     */
    protected function checkHasAction()
    {
        global $DIC;
        if ($DIC->access()->checkAccess('write', '', $_GET['ref_id']) ) {
            return true;
        }
        // Check if this is a file that can be opened in Office Online
        if (!$this->node->getIsDir() && $this->checkOpenInOfficePerm()) {
            $file = $this->fetchExoFileByNodeId($this->node->getId());

            return !is_null($file->getMsURL());
        }

        return false;
    }


    /**
     *
     */
    protected function addItemsBefore()
    {
        $access = new ilObjCloudAccess();
        $obj_id = $this->getPluginObject()->getObjId();
        $references = ilObject::_getAllReferences($obj_id);
        $ref_id = array_shift($references);
        if ($access->_checkAccess('', 'edit_settings', $ref_id, $obj_id)) {
            $path_parts = explode('/', $this->node->getPath());
            $title = end($path_parts);
            $this->selection_list->addItem(
                ilOneDrivePlugin::getInstance()->txt('asl_rename'),
                'rn',
                "javascript:il.OneDriveList.rename(\'" . $this->node->getId() . "\', \'" . $title . "\');"
            );
        }
    }


    /**
     * @return bool
     * @throws ilCloudException
     */
    protected function addItemsAfter() {
        global $DIC;
        if (!$this->node->getIsDir() && $this->checkOpenInOfficePerm()) {
            $file = $this->fetchExoFileByNodeId($this->node->getId());

            if (!is_null($file->getMsURL())) {
                $DIC->ctrl()->setParameterByClass(ilCloudPluginActionListGUI::class, self::ITEM_ID, $file->getId());
                $this->selection_list->addItem(
                    ilOneDrivePlugin::getInstance()->txt('asl_open_msoffice'),
                    'ms',
                    $DIC->ctrl()->getLinkTargetByClass([ilObjCloudGUI::class, ilCloudPluginActionListGUI::class], self::CMD_OPEN_IN_OFFICE_ONLINE),
                    '',
                    '',
                    '_blank'
                );
            }

        }

        return true;
    }


    /**
     * @param int $node_id
     *
     * @return exodFile|exodItem|null
     * @throws ilCloudException
     */
    protected function fetchExoFileByNodeId($node_id)
    {
        $exoFile = exodItemCache::get($node_id);

        if (!$exoFile instanceof exodFile) {
            $exoFile = $this->getService()->getClient()->getFileObject($this->node->getId());
        }

        return $exoFile;
    }


    /**
     * @return bool
     */
    private function checkOpenInOfficePerm()
    {
        global $DIC;
        $user = $DIC->user();
        $user_id = $user->getId();
        $ref_id = $_GET["ref_id"];

        return $DIC->access()->checkAccessOfUser($user_id, 'edit_in_online_editor', "", $ref_id);
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


    /**
     *
     */
	protected function initRename()
    {
        $response = new stdClass();
        $response->success = true;
        $response->message = null;
        $response->content = '<div id="cld_rename">' . $this->buildForm(filter_input(INPUT_POST, self::POST_TITLE, FILTER_SANITIZE_STRING))->getHTML() . '</div>';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }


    /**
     *
     */
    protected function rename()
    {
        $response = new stdClass();
        // TODO: access check
        $form = $this->buildForm();
        if (!$form->checkInput()) {
            $response->success = false;
            $response->message = ilUtil::getSystemMessageHTML($this->txt('msg_invalid_input'), "failure");
            echo json_encode($response);
            exit;
        }

        $id = filter_input(INPUT_GET, self::GET_ID, FILTER_SANITIZE_STRING);
        $title = $form->getInput(self::POST_TITLE);

        $exoFile = exodItemCache::get($id);
        if ($exoFile instanceof exodFile) {
            $title = exodFile::formatRename($title, $exoFile->getName());
        }

        try {
            $this->getService()->getClient()->renameItemById($id, $title);

            $response->message = ilUtil::getSystemMessageHTML($this->txt("msg_renamed"), "success");
            $response->success = true;
        } catch (Exception $e) {
            $response->message = ilUtil::getSystemMessageHTML($e->getMessage(), "failure");
        }

        $response->id = $id;
        $response->title = $title;

        echo "<script type='text/javascript'>window.parent.il.OneDriveList.afterRenamed(" . json_encode($response)
            . ");</script>";
        exit;
    }



    /**
     * @param string $title
     *
     * @return ilPropertyFormGUI
     */
    protected function buildForm($title = '')
    {
        global $DIC;
        $form = new ilPropertyFormGUI();
        $form->setTitle($this->txt('form_title_rename'));
        $id = filter_input(INPUT_POST, self::GET_ID, FILTER_SANITIZE_STRING);
        $DIC->ctrl()->setParameterByClass(ilCloudPluginActionListGUI::class, self::GET_ID, $id);
        // $form->setFormAction($DIC->ctrl()->getLinkTargetByClass([ilObjCloudGUI::class, ilCloudPluginActionListGUI::class], self::CMD_RENAME));
        $form->setFormAction($DIC->ctrl()->getFormActionByClass([ilObjCloudGUI::class, ilCloudPluginActionListGUI::class]));
        $form->setTarget("cld_blank_target");

        $input = new ilTextInputGUI($this->txt('title'), self::POST_TITLE);
        if ($title !== '') {
            $input->setValue($title);
        }
        $form->setItems([
            $input
        ]);
        $form->addCommandButton('rename', $this->txt('save'));
        $form->addCommandButton('cancel', $this->txt('cancel'));
        return $form;
    }

    /**
     * Update properties
     */
    protected function cancel() {
        $response = new stdClass();
        $response->status = "cancel";

        echo "<script language='javascript' type='text/javascript'>window.parent.il.OneDriveList.afterRenamed(" . json_encode($response)
            . ");</script>";
        exit;
    }


    /**
     * @throws exodEmailBuilderException
     * @throws ilCloudException
     * @throws ilCloudPluginConfigException
     */
    protected function openInOfficeOnline()
    {
        global $DIC;

        $item_id = filter_input(INPUT_GET, self::ITEM_ID);
        $exoFile = exodItemCache::get($item_id);
        if (!$exoFile instanceof exodFile) {
            $exoFile = $this->getService()->getClient()->getFileObject($item_id);
        }

        $od_email = OneDriveEmailBuilderFactory::getInstance()->getEmailBuilder()->getOneDriveEmailForUser($DIC->user());
        if (!is_null($od_email) && $od_email !== '') {
            $response = $this->getService()->getClient()->addWritePermissionToFile(
                $item_id,
                $od_email
            );
        }

        Header('Location: ' . $exoFile->getMsURL());
    }

}
