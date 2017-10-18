<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/models/Core.php');

class Events extends Core {
	
	public function getEvents() {	
		$query = $this->db->placehold("SELECT DISTINCT *
										FROM events e ORDER BY a.id");
		$this->db->query($query);
		
		return $this->db->results();
	}
	
	public function addEvent($event) {
		$event = (array) $event;
		$query = $this->db->query("INSERT INTO events SET ?%", $event);
		return $this->db->insert_id();
	}
	
	public function deleteAllEvents() {
		$query = $this->db->placehold("DELETE FROM tableName; ALTER TABLE tableName AUTO_INCREMENT = 1");
		$this->db->query($query);
	}
	
}