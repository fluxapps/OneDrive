<?php
$splitPos = strpos($_GET["state"], "|");
if ($splitPos === false) {
	//something went wrong
	$state = NULL;
	echo "Error in Auth, state equqals null";
	exit;
} else {
	$state = substr($_GET["state"], $splitPos + 1);
}

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'HTTPS://' : 'HTTP://';

$path = str_replace("Customizing/global/plugins/Modules/Cloud/CloudHook/Dropbox/redirect.php", "", $_SERVER['SCRIPT_NAME']);

$address = $_SERVER['SERVER_NAME'];

if (array_key_exists("code", $_GET)) {
	header('Location: ' . $protocol . $address . $path . htmlspecialchars_decode($state) . '&code=' . $_GET["code"] . '&state=' . $_GET["state"]);
} else {
	header('Location: ' . $protocol . $address . $path . htmlspecialchars_decode($state));
}
?>