<#1>
<?php
include_once("./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/class.ilOneDrivePlugin.php");
$plugin_object = ilOneDrivePlugin::getInstance();

$fields = array(
	'id' => array(
		'type' => 'integer',
		'length' => 8,
		'notnull' => true
	),
	'access_token' => array(
		'type' => 'text',
		'length' => 2000
	),
	'refresh_token' => array(
		'type' => 'text',
		'length' => 2000
	),
	'public_link' => array(
        'type'   => 'text',
        'length' => 2000,
	),
	'max_file_size' => array(
		'type' => 'text',
		'length' => 256
	),
	'valid_through' => array(
		'type' => 'integer',
		'length' => 8
	),
	'validation_user_id' => array(
		'type' => 'integer',
		'length' => 8
	),
);
global $ilDB;
$ilDB->createTable($plugin_object->getPluginTableName(), $fields, true);
$ilDB->addPrimaryKey($plugin_object->getPluginTableName(), array( "id" ));
?>
<#2>
<?php
include_once("./Modules/Cloud/classes/class.ilCloudPluginConfig.php");
include_once("./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/class.ilOneDrivePlugin.php");
$plugin_object = ilOneDrivePlugin::getInstance();
$config_object = new ilCloudPluginConfig($plugin_object->getPluginConfigTableName());
$config_object->initDB();
?>
<#3>
<?php
global $DIC;
$ilDB = $DIC['ilDB'];

$ilDB->manipulate(
		"UPDATE il_cld_data SET " .
        " auth_complete = 0"
);
?>
<#4>
<?php
//Adding a new Permission ("Open in Office Online")
require_once("./Services/Migration/DBUpdate_3560/classes/class.ilDBUpdateNewObjectType.php");
$orgu_type_id = ilDBUpdateNewObjectType::getObjectTypeId('cld');

if ($orgu_type_id) {
    $offering_admin = ilDBUpdateNewObjectType::addCustomRBACOperation(ilOneDrivePlugin::getInstance()->getPrefix() . '_asl_open_msoffice', 'open ms office', 'object', 280);
    if ($offering_admin) {
        ilDBUpdateNewObjectType::addRBACOperation($orgu_type_id, $offering_admin);
    }
}
?>
