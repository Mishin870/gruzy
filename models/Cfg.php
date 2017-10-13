<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/models/Core.php');

class Cfg extends Core {
	private $vars = array();
	
	public function __construct() {
		$this->vars['db_server']		= 'localhost';
		$this->vars['db_user']			= 'root';
		$this->vars['db_name']			= 'u0345590_sys';
		$this->vars['db_password']		= 'uzglobal!@12';
	}
	
	public function __get($name) {
		return isset($this->vars[$name]) ? $this->vars[$name] : null;
	}
	
	public function __set($name, $value) {
		$this->vars[$name] = $value;
	}
	
}
