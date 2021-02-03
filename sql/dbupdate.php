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
//Adding a new Permission ("Open in Online Editor")
require_once("./Services/Migration/DBUpdate_3560/classes/class.ilDBUpdateNewObjectType.php");
$cld_type_id = ilDBUpdateNewObjectType::getObjectTypeId('cld');

if ($cld_type_id) {
    $open_online_editor = ilDBUpdateNewObjectType::addCustomRBACOperation('edit_in_online_editor', 'edit in online editor', 'object', 280);
    if ($open_online_editor) {
        ilDBUpdateNewObjectType::addRBACOperation($cld_type_id, $open_online_editor);
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
<#7>
<?php
global $DIC;
$DIC->database()->modifyTableColumn(
    'cld_cldh_exod_token',
    'access_token',
    [
        'type'   => 'text',
        'length' => 4000,
    ]
);
$DIC->database()->modifyTableColumn(
    'cld_cldh_exod_token',
    'refresh_token',
    [
        'type'   => 'text',
        'length' => 4000,
    ]
);
?>
<#8>
<?php
// rename rbac operation
global $DIC;
$query = $DIC->database()->query('SELECT * FROM rbac_operations WHERE operation = "cld_cldh_exod_asl_open_msoffice"');
if ($res = $DIC->database()->fetchAssoc($query)) {
    require_once("./Services/Migration/DBUpdate_3560/classes/class.ilDBUpdateNewObjectType.php");
    if (ilDBUpdateNewObjectType::getCustomRBACOperationId('edit_in_online_editor')) {
        $DIC->database()->query('DELETE FROM rbac_operations WHERE ops_id = ' . $res['ops_id']);
    } else {
        $DIC->database()->query('UPDATE rbac_operations SET operation = "edit_in_online_editor", description = "edit in online editor" WHERE ops_id = ' . $res['ops_id']);
    }
}
?>
