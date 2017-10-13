<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/models/Core.php');

/**
 *  Модель работы с локализованной датой
 */
class Settings extends Core {

	/**
	 *  @brief получить дату в формате 
	 */
	public function getSetting($name) {
		$query = "SELECT value FROM settings s WHERE s.name='".$name."' LIMIT 1";
		$this->db->query($query);
		return intval($this->db->result()->value);
	}

}