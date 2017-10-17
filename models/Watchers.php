<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/models/Core.php');

/**
 *  Слушатели грузов (чаты)
 */
class Watchers extends Core {
	
	public function getWatchers($filter = array()) {	
		$chatidFilter = '';
		$productIdFilter = '';
		
		if (isset($filter['product_id'])) $productIdFilter = $this->db->placehold('AND w.product_id=?', intval($filter['product_id']));
		if (isset($filter['chatid'])) $chatidFilter = $this->db->placehold('AND w.chatid=?', intval($filter['chatid']));
		
		$query = $this->db->placehold("SELECT DISTINCT *
										FROM watchers w WHERE 1 $chatidFilter $productIdFilter ORDER BY w.id");
		$this->db->query($query);
		
		return $this->db->results();
	}
	
	public function getWatcher($id) {
		if (is_int($id)) {
			$filter = $this->db->placehold('w.id = ?', $id);
		} else {
			$filter = $this->db->placehold('w.id = -1');
		}
		$query = "SELECT *
								 FROM watchers w WHERE $filter LIMIT 1";
		$this->db->query($query);
		return $this->db->result();
	}
	
	public function addWatcher($watcher) {
		$watcher = (array) $watcher;
		$query = $this->db->query("INSERT INTO watchers SET ?%", $watcher);
		return $this->db->insert_id();
	}
	
	public function updateWatcher($id, $watcher) {
		$query = $this->db->placehold("UPDATE watchers SET ?% WHERE id=? LIMIT 1", $watcher, intval($id));
		$this->db->query($query);
		return $id;
	}
	
	public function deleteWatcher($id) {
		if (!empty($id)) {
			$query = $this->db->placehold("DELETE FROM watchers WHERE id=? LIMIT 1", $id);
			$this->db->query($query);		
		}
	}
	
}