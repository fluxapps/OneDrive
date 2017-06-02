<?php
chdir('../../../../../../../');
require_once('./Services/Init/classes/class.ilInitialisation.php');
ilInitialisation::initILIAS();

require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/class.ilOneDrivePlugin.php');

ilOneDrivePlugin::getInstance()->getExodApp(new exodBearerToken())->getExodAuth()->redirectToObject();
exit;
