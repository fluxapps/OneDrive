<?php
require_once './Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Util/SingletonTrait.php';
require_once 'OneDriveEmailBuilderInterface.php';

/**
 * Class StdOneDriveEmailBuilder
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class StdOneDriveEmailBuilder implements OneDriveEmailBuilderInterface
{

    use SingletonTrait;


    /**
     * @param ilObjUser $user
     *
     * @return string
     * @throws ilCloudPluginConfigException
     */
    public function getOneDriveEmailForUser(ilObjUser $user)
    {
        $config = new exodConfig();
        $mapping_field = $config->getO365Mapping();
        switch ($mapping_field) {
            case 'email':
                return $user->getEmail();
            case 'ext_account':
                return $user->getExternalAccount();
            case null:
                return null;
            default:
                $ud_data = $user->getUserDefinedData();
                return $ud_data[$mapping_field] ?: null;
        }
    }
}