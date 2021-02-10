<?php

require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Client/class.exodPath.php');

/**
 * Class ilOneDriveSettingsGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           1.0.0
 *
 * @ilCtrl_IsCalledBy ilOneDriveSettingsGUI : ilObjCloudGUI
 * @ingroup           ModulesCloud
 */
class ilOneDriveSettingsGUI extends ilCloudPluginSettingsGUI {

    const SUBTAB_GENERAL = 'general';
    const SUBTAB_LOGS = 'logs';

	/**
	 * @var ilPropertyFormGUI
	 */
	protected $form;


    protected function initPluginSettings()
    {
        $this->form->getItemByPostVar('root_folder')->setDisabled(true);
    }


    public function updateSettings() {
		global $DIC;
		$ilCtrl = $DIC['ilCtrl'];
		$this->initSubtabs(self::SUBTAB_GENERAL);

		try {
			$this->initSettingsForm();

			if ($this->form->checkInput()) {
				$_POST['title'] = exodPath::validateBasename($this->form->getInput("title"));
			}

			parent::updateSettings();
		} catch (Exception $e) {
			ilUtil::sendFailure($e->getMessage(), true);
			$ilCtrl->redirect($this, 'editSettings');
		}

	}


    function editSettings()
    {
        global $DIC;
        $ilCtrl = $DIC['ilCtrl'];
        $lng = $DIC['lng'];

        $this->initSubtabs(self::SUBTAB_GENERAL);

        $cloud_object_changed = false;

        // On object creation set cloud root id
        if (isset($_GET["root_id"])) {
            $this->applyRootId();
            $cloud_object_changed = true;
            ilUtil::sendSuccess($lng->txt("cld_object_added"), true);
        }

        $service = ilCloudConnector::getServiceClass($this->cloud_object->getServiceName(), $this->cloud_object->getId());

        if ($service->updateRootFolderPosition($this->cloud_object->getRootId())) {
            $cloud_object_changed = true;
        }

        if ($cloud_object_changed) {

            $ilCtrl->redirectByClass("ilCloudPluginSettingsGUI", "editSettings");
        }

        parent::editSettings();
    }


    protected function applyRootId() {
        $this->cloud_object->setRootId($_GET["root_id"]);
        $this->cloud_object->update();
    }


    public function initSettingsForm()
    {
        parent::initSettingsForm();

        $item = $this->form->getItemByPostVar("root_folder");
        $item->setTitle($this->txt("root_folder"));

    }

    protected function showLogs()
    {
        global $DIC;
        $DIC->tabs()->activateTab('settings');
        $this->initSubtabs(self::SUBTAB_LOGS);

    }

    protected function initSubtabs(string $active)
    {
        global $DIC;
        $DIC->tabs()->addSubTab(
            self::SUBTAB_GENERAL,
            $this->txt('subtab_' . self::SUBTAB_GENERAL),
            $DIC->ctrl()->getLinkTargetByClass(parent::class, 'editSettings'));
        $DIC->tabs()->addSubTab(
            self::SUBTAB_LOGS,
            $this->txt('subtab_' . self::SUBTAB_LOGS),
            $DIC->ctrl()->getLinkTargetByClass(parent::class, 'showLogs'));
        $DIC->tabs()->setSubTabActive($active);
    }


    public function txt($var = "")
    {
        return parent::txt('settings_' . $var);
    }


    /**
	 * @return ilOneDrive
	 */
	public function getPluginObject() {
		return parent::getPluginObject();
	}


    protected function getMakeOwnPluginSection()
    {
        return false;
    }
}

