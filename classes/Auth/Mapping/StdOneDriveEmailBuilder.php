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
     */
    public function getOneDriveEmailForUser(ilObjUser $user)
    {
        return $user->getEmail();
    }
}