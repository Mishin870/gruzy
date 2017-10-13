<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/models/Core.php');
session_start();

$controllers = array(
	'index'		    => 'IndexController',
	'login'		    => 'LoginController',
	'contacts'      => 'ContactsController',
	'print'         => 'PrintController',
	'settings'      => 'SettingsController',
	'statistics'    => 'StatisticsController'
);

function redirectToModule($moduleName) {
	$_SESSION['redirect'] = $moduleName;
	require_once('redirect.html');
	die();
}
$log = isset($_SESSION['user_id']);

$core = new Core();
$module = $core->request->get("m", "string");
if ($log && $module == 'login') {
	redirectToModule('index');
} else if (!$log && ($module != 'login')) {
	redirectToModule('login');
} else if (!isset($_GET['m'])) {
	redirectToModule('index');
}
if (!array_key_exists($module, $controllers)) die("Error! Module not found!");

if ($log) {
	$admins = $core->admins;
	$admin = $admins->getAdmin(intval($_SESSION['user_id']));
}
$langCode = $admin != null ? $admin->lang : "ru";

$moduleName = $module;
$module = $controllers[$moduleName];
require_once($_SERVER['DOCUMENT_ROOT']."/controllers/".$module.".php");
//$msg = "none";
$controller = new $module();
$controller->show();