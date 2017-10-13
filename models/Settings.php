<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/models/Core.php');

/**
 *  Модель работы с настройками
 */
class Settings extends Core {

	/**
	 *  @brief получить значение настройки по name
	 */
	public function getSetting($name) {
		$query = "SELECT value FROM settings s WHERE s.name='".$name."' LIMIT 1";
		$this->db->query($query);
		return intval($this->db->result()->value);
	}

	/**
	 *  @brief установить значение настройки по name
	 */
	public function setSetting($name, $value) {
		$query = $this->db->placehold("UPDATE settings SET value=? WHERE name='".$name."' LIMIT 1", intval($value));
		$this->db->query($query);
	}

}