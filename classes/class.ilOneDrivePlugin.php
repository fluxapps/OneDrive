<?php
require_once('./Modules/Cloud/classes/class.ilCloudHookPlugin.php');
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/class.exodConfig.php');
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/App/class.exodAppBusiness.php');
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/App/class.exodAppPublic.php');

/**
 * Class ilOneDrivePlugin
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilOneDrivePlugin extends ilCloudHookPlugin  {

	const PLUGIN_NAME = 'OneDrive';
	/**
	 * @var exodAppBusiness
	 */
	protected static $app_instance;


	/**
	 * @return string
	 */
	public function getPluginName() {
		return self::PLUGIN_NAME;
	}


    public function updateLanguages($a_lang_keys = null)
    {
        global $DIC;
        parent::updateLanguages($a_lang_keys);
        $lang_entries_de = [];
        $lang_entries_en = [];
        $lang_entries_de['cld_cldh_exod_asl_open_msoffice'] = 'In Office Online öffnen';
        $lang_entries_en['cld_cldh_exod_asl_open_msoffice'] = 'Open in Office Online';
        $lang_entries_de['cld_cld_cldh_exod_asl_open_msoffice'] = 'Benutzer können Dokumente in Office Online bearbeiten';
        $lang_entries_en['cld_cld_cldh_exod_asl_open_msoffice'] = 'Users can edit documents in Office Online';

        foreach ($lang_entries_de as $identifier => $value) {
            ilObjLanguage::replaceLangEntry('rbac', $identifier, 'de', $value);
        }

        foreach ($lang_entries_en as $identifier => $value) {
            ilObjLanguage::replaceLangEntry('rbac', $identifier, 'en', $value);
        }

        $q = "SELECT * FROM lng_modules WHERE " .
            " lang_key = 'de'" .
            " AND module = 'rbac'";
        $set = $DIC->database()->query($q);
        $row = $DIC->database()->fetchAssoc($set);
        $arr2 = unserialize($row["lang_array"]);
        if (is_array($arr2)) {
            $lang_entries_de = array_merge($arr2, $lang_entries_de);
        }
        ilObjLanguage::replaceLangModule('de', 'rbac', $lang_entries_de);

        $q = "SELECT * FROM lng_modules WHERE " .
            " lang_key = 'en'" .
            " AND module = 'rbac'";
        $set = $DIC->database()->query($q);
        $row = $DIC->database()->fetchAssoc($set);
        $arr2 = unserialize($row["lang_array"]);
        if (is_array($arr2)) {
            $lang_entries_en = array_merge($arr2, $lang_entries_en);
        }
        ilObjLanguage::replaceLangModule('en', 'rbac', $lang_entries_en);
    }


    /**
	 * @param exodBearerToken $exodBearerToken
	 *
	 * @return exodAppBusiness|exodAppPublic
	 */
	public function getExodApp(exodBearerToken $exodBearerToken) {
		$exodConfig = new exodConfig();
		$exodConfig->checkComplete();

		exodCurl::setSslVersion($exodConfig->getSSLVersion());
		exodCurl::setIpV4($exodConfig->getResolveIpV4());

		if ($exodConfig->getClientType() == exodApp::TYPE_BUSINESS) {
			$exodTenant = new exodTenant();
			$exodTenant->setTenantId($exodConfig->getTentantId());
			$exodTenant->setTenantName($exodConfig->getTenantName());

			$app = exodAppBusiness::getInstance($exodBearerToken, $exodConfig->getClientId(), $exodConfig->getClientSecret(), $exodTenant);
			$app->setIpResolveV4($exodConfig->getResolveIpV4());
		} elseif ($exodConfig->getClientType() == exodApp::TYPE_PUBLIC) {
			$app = exodAppPublic::getInstance($exodBearerToken, $exodConfig->getClientId(), $exodConfig->getClientSecret());
			$app->setIpResolveV4($exodConfig->getResolveIpV4());
		}

		return $app;
	}


	/**
	 * @var ilOneDrivePlugin
	 */
	protected static $instance;


	/**
	 * @return ilOneDrivePlugin
	 */
	public static function getInstance() {
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}


    /**
     * @param $user ilObjUser
     *
     * @return string|null
     * @throws ilCloudPluginConfigException
     */
	public function getOneDriveEmailForUser($user)
    {
        OneDriveEmailBuilderFactory::getInstance()->getEmailBuilder()->getOneDriveEmailForUser($user);

    }

	/**
	 * @return string
	 */
	public function getCsvPath() {
		return './Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/lang/lang.csv';
	}


	/**
	 * @return string
	 */
	public function getAjaxLink() {
		return null;
	}
}