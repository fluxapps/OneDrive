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
<#5>
<?php
include_once("./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/class.ilOneDrivePlugin.php");
$plugin_object = ilOneDrivePlugin::getInstance();
global $DIC;
$DIC->database()->modifyTableColumn(
	$plugin_object->getPluginTableName(),
	'public_link',
    [
        'type'   => 'text',
        'length' => 2000,
    ]
);
?>
<#6>
<?php
require_once 'Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Auth/class.exodBearerToken.php';
exodBearerToken::updateDB();
/** @var $ilDB ilDBPdo */
if ($ilDB->tableColumnExists('cld_cldh_exod_props', 'access_token')) {
	$res = $ilDB->query('SELECT p.*, d.owner FROM cld_cldh_exod_props p inner join object_data d on d.obj_id = p.id');
	while ($rec = $ilDB->fetchAssoc($res)) {
		if ($rec['access_token']) {
			$token = exodBearerToken::findOrGetInstanceForUser($rec['owner']);
			if ($token->getValidThrough() < $rec['valid_through']) {
				$token->setAccessToken($rec['access_token']);
				$token->setRefreshToken($rec['refresh_token']);
				$token->setValidThrough($rec['valid_through']);
				$token->store();
			}
		}
	}
	$ilDB->dropTableColumn('cld_cldh_exod_props', 'access_token');
	$ilDB->dropTableColumn('cld_cldh_exod_props', 'refresh_token');
	$ilDB->dropTableColumn('cld_cldh_exod_props', 'valid_through');
	$ilDB->dropTableColumn('cld_cldh_exod_props', 'validation_user_id');
}
?>
