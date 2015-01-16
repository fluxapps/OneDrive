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
		'type' => 'boolean',
	),
	'max_file_size' => array(
		'type' => 'text',
		'length' => 256
	),
	'valid_through' => array(
		'type' => 'integer',
		'length' => 8
	),
);
global $ilDB;
$ilDB->createTable($plugin_object->getPluginTableName(), $fields);
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
