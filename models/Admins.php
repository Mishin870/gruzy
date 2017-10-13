<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/models/Core.php');

/**
 *  Модель работы с администраторами
 */
class Admins extends Core {
	
	/**
	 *  @brief получить администраторов
	 *  
	 *  @return выборка из БД
	 */
	public function getAdmins($filter = array()) {	
		$idFilter = '';
		$loginFilter = '';
		$levelFilter = '';
		
		if(isset($filter['id'])) $idFilter = $this->db->placehold('AND a.id=?', intval($filter['id']));
		if(!empty($filter['login'])) {
			$login = $this->db->escape(trim($filter['login']));
			if ($login !== '') $loginFilter = $this->db->placehold("AND a.login='$login'");
		}
		if(isset($filter['level'])) $levelFilter = $this->db->placehold('AND a.level=?', intval($filter['level']));
		
		$query = $this->db->placehold("SELECT DISTINCT *
										FROM admins a WHERE 1 $idFilter $loginFilter $levelFilter ORDER BY a.id");
		$this->db->query($query);
		
		return $this->db->results();
	}
	
	/**
	 *  @brief получить администратора по id
	 */
	public function getAdmin($id) {
		if (is_int($id)) {
			$filter = $this->db->placehold('a.id = ?', $id);
		} else {
			$filter = $this->db->placehold('a.id = -1');
		}
		$query = "SELECT *
								 FROM admins a WHERE $filter LIMIT 1";
		$this->db->query($query);
		return $this->db->result();
	}
	
	/**
	 *  @brief добавить админа
	 *  
	 *  @param [in] $admin Object со всеми нужными параметрами
	 */
	public function addAdmin($admin) {
		//Превращаем объект в ассоциативный массив для подачи в MySql
		$admin = (array) $admin;
		//Устанавливаем поля в запросе на основании полей массива $admin
		$query = $this->db->query("INSERT INTO admins SET ?%", $admin);
		return $this->db->insert_id();
	}
	
	/**
	 *  @brief обновить админа по id
	 */
	public function updateAdmin($id, $admin) {
		$query = $this->db->placehold("UPDATE admins SET ?% WHERE id=? LIMIT 1", $admin, intval($id));
		$this->db->query($query);
		return $id;
	}

	/**
	 * Установить язык у юзера
	 */
	public function setLang($id, $lang) {
		$query = $this->db->placehold("UPDATE admins SET lang='".$lang."' WHERE id=? LIMIT 1", intval($id));
		$this->db->query($query);
	}
	
	/**
	 *  @brief удалить админа по id
	 *  
	 *  @param [in] @id число-id
	 */
	public function deleteAdmin($id) {
		if (!empty($id)) {
			$query = $this->db->placehold("DELETE FROM admins WHERE id=? LIMIT 1", $id);
			$this->db->query($query);		
		}
	}
	
}