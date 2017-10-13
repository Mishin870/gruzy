<?php

/**
 *  Ядро для всех моделей. Модели наследуются от него
 */
class Core {
	//Использую кодстайл Java. Статичные переменные сверху
	//Инициализированные объекты моделей
	private static $objects = array();
	//База: название_модели => класс
	private $models = array(
		'request'	=> 'Request',
		'admins'	=> 'Admins',
		'products'	=> 'Products',
		'db'		=> 'Database',
		'cfg'		=> 'Cfg',
		'settings'  => 'Settings',
		'locdate'	=> 'LocDate'
	);
	
	public function __construct() {}

    /**
     * Метод получения определенной модели
     * @param $name название модели
     * @return mixed|null модель
     */
	public function __get($name) {
		if(isset(self::$objects[$name])) return(self::$objects[$name]);
		if(!array_key_exists($name, $this->models)) return null;
		
		$class = $this->models[$name];
		include_once(dirname(__FILE__).'/'.$class.'.php');
		self::$objects[$name] = new $class();
		
		return self::$objects[$name];
	}
}