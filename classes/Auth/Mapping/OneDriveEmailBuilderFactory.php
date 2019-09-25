<?php
require_once './Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/class.exodConfig.php';
require_once './Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Util/SingletonTrait.php';
require_once 'StdOneDriveEmailBuilder.php';
require_once 'exodEmailBuilderException.php';
require_once 'OneDriveEmailBuilderInterface.php';

/**
 * Class EmailMappingFactory
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class OneDriveEmailBuilderFactory
{
    use SingletonTrait;


    /**
     * @return OneDriveEmailBuilderInterface
     * @throws ilCloudPluginConfigException
     * @throws exodEmailBuilderException
     */
    public function getEmailBuilder()
    {
        $config = new exodConfig();
        if (!$config->getValue(exodConfig::F_EMAIL_MAPPING_HOOK_ACTIVE)) {
            return StdOneDriveEmailBuilder::getInstance();
        }

        $path = $config->getValue(exodConfig::F_EMAIL_MAPPING_HOOK_ACTIVE . '_' . exodConfig::F_EMAIL_MAPPING_HOOK_PATH);
        $class = $config->getValue(exodConfig::F_EMAIL_MAPPING_HOOK_ACTIVE . '_' . exodConfig::F_EMAIL_MAPPING_HOOK_CLASS);

        if (!is_file($path)) {
            throw new exodEmailBuilderException('no file found for path ' . $path);
        }
        require_once $path;

        if (!(is_subclass_of($class, OneDriveEmailBuilderInterface::class))) {
            throw new exodEmailBuilderException('class ' . $class . ' must implement OneDriveEmailBuilderInterface');
        }
        return $class::getInstance();
    }
}