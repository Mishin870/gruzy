<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/models/Core.php');
session_start();

$controllers = array(
	'index'		=> 'IndexController',
	'login'		=> 'LoginController',
	'common'    => 'CommonController'
);
$core = new Core();
$module = $core->request->post("m", "string");

function ajaxResponse($err, $msg) {
	die(json_encode(array(
		"err" => ($err === true ? "1" : "0"),
		"msg" => utf8_encode($msg)
	)));
}

if (!array_key_exists($module, $controllers)) ajaxResponse(true, "Error! Module not found!");
$module = $controllers[$module];

require_once($_SERVER['DOCUMENT_ROOT']."/controllers/".$module.".php");
$controller = new $module();
$controller->ajax();