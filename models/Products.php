<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/models/Core.php');

/**
 *  Модель работы с продуктами
 *  поля: id, name, admin_id, date, state, address, phone, weight, payment, note
 */
class Products extends Core {

	public function getProducts($filter = array()) {
		$idFilter = '';
		$admin_idFilter = '';
		$stateFilter = '';
		$paymentFilter = '';
		$activeFilter = '';
		$payedFilter = '';

		if (isset($filter['id'])) $idFilter = $this->db->placehold('AND p.id=?', intval($filter['id']));
		if (isset($filter['admin_id'])) $admin_idFilter = $this->db->placehold('AND p.admin_id=?', intval($filter['admin_id']));
		if (isset($filter['state'])) $stateFilter = $this->db->placehold('AND p.state=?', intval($filter['state']));
		if (isset($filter['payed'])) $payedFilter = $this->db->placehold('AND p.payed=?', intval($filter['payed']));
		if (isset($filter['payment'])) $paymentFilter = $this->db->placehold('AND p.payment=?', intval($filter['payment']));
		if (isset($filter['active'])) $activeFilter = $this->db->placehold('AND p.active=?', intval($filter['active']));

		$query = $this->db->placehold("SELECT DISTINCT *
									FROM products p WHERE 1
									$payedFilter $idFilter $admin_idFilter $stateFilter $paymentFilter $activeFilter ORDER BY p.id");
		$this->db->query($query);

		return $this->db->results();
	}

	public function getProductsByTrack($track) {
		$trackFilter = $this->db->placehold("AND p.track_id='".$track."'");
		$query = $this->db->placehold("SELECT DISTINCT *
									FROM products p WHERE 1
									$trackFilter LIMIT 1");
		$this->db->query($query);

		return $this->db->result();
	}

	public function getContacts() {
		$query = $this->db->placehold("SELECT name, MAX(track_id) as track_id, MAX(phone) as phone FROM products p GROUP BY p.name");
		$this->db->query($query);
		return $this->db->results();
	}

	public function count($filter) {
		$idFilter = '';
		$admin_idFilter = '';
		$stateFilter = '';
		$paymentFilter = '';
		$activeFilter = '';
		$payedFilter = '';

		if (isset($filter['id'])) $idFilter = $this->db->placehold('AND p.id=?', intval($filter['id']));
		if (isset($filter['admin_id'])) $admin_idFilter = $this->db->placehold('AND p.admin_id=?', intval($filter['admin_id']));
		if (isset($filter['state'])) $stateFilter = $this->db->placehold('AND p.state=?', intval($filter['state']));
		if (isset($filter['payed'])) $payedFilter = $this->db->placehold('AND p.payed=?', intval($filter['payed']));
		if (isset($filter['payment'])) $paymentFilter = $this->db->placehold('AND p.payment=?', intval($filter['payment']));
		if (isset($filter['active'])) $activeFilter = $this->db->placehold('AND p.active=?', intval($filter['active']));

		$query = $this->db->placehold("SELECT DISTINCT COUNT(*) as count
									FROM products p WHERE 1
									$payedFilter $idFilter $admin_idFilter $stateFilter $paymentFilter $activeFilter ORDER BY p.id");
		$this->db->query($query);
		return intval($this->db->result()->count);
	}
	
	public function getProduct($id) {
		if (is_int($id)) {
			$filter = $this->db->placehold('p.id = ?', $id);
		} else {
			$filter = $this->db->placehold('p.id = -1');
		}
		$query = "SELECT *
						FROM products p WHERE $filter LIMIT 1";
		$this->db->query($query);
		return $this->db->result();
	}
	
	public function addProduct($product) {
		$product = (array) $product;
		$query = $this->db->query("INSERT INTO products SET ?%", $product);
		return $this->db->insert_id();
	}
	
	public function updateProduct($id, $product) {
		$query = $this->db->placehold("UPDATE products SET ?% WHERE id=? LIMIT 1", $product, intval($id));
		$this->db->query($query);
		return $id;
	}
	
	public function deleteProduct($id) {
		if(!empty($id)) {
			$query = $this->db->placehold("DELETE FROM products WHERE id=? LIMIT 1", $id);
			$this->db->query($query);		
		}
	}

	public function getState($id) {
		$query = $this->db->placehold("SELECT state FROM products p WHERE p.id=? LIMIT 1", intval($id));
		$this->db->query($query);
		return intval($this->db->result()->state);
	}

	public function setState($id, $state) {
		$query = $this->db->placehold("UPDATE products SET state=? WHERE id=? LIMIT 1", intval($state), intval($id));
		$this->db->query($query);
	}

	public function setActive($id, $active) {
		$query = $this->db->placehold("UPDATE products SET active=? WHERE id=? LIMIT 1", intval($active), intval($id));
		$this->db->query($query);
	}

	public function getMonthProducts($prev = false) {
		if ($prev) {
			$query = $this->db->placehold("SELECT DISTINCT DAY(p.date) as day, price, payed, weight FROM products p WHERE
										MONTH(p.date) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH)
										AND YEAR(p.date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)
										AND p.active = 1
									");
		} else {
			$query = $this->db->placehold("SELECT DISTINCT DAY(p.date) as day, price, payed, weight FROM products p WHERE
										MONTH(p.date) = MONTH(CURRENT_DATE())
										AND YEAR(p.date) = YEAR(CURRENT_DATE())
										AND p.active = 1
									");
		}
		$this->db->query($query);
		return $this->db->results();
	}

	public function getWeekProducts($prev = false) {
		if ($prev) {
			$query = $this->db->placehold("SELECT DISTINCT WEEKDAY(p.date) as day, price, payed, weight FROM products p WHERE
										YEARWEEK(p.date, 1) = YEARWEEK(CURDATE() - INTERVAL 7 DAY, 1)
										AND YEAR(p.date) = YEAR(CURRENT_DATE() - INTERVAL 7 DAY)
										AND p.active = 1
									");
		} else {
			$query = $this->db->placehold("SELECT DISTINCT WEEKDAY(p.date) as day, price, payed, weight FROM products p WHERE
										YEARWEEK(p.date, 1) = YEARWEEK(CURDATE(), 1)
										AND YEAR(p.date) = YEAR(CURRENT_DATE())
										AND p.active = 1
									");
		}
		$this->db->query($query);
		return $this->db->results();
	}

	public function getDayProducts($prev = false) {
		if ($prev) {
			$query = $this->db->placehold("SELECT DISTINCT HOUR(p.date) as hour, price, payed, weight FROM products p WHERE
										MONTH(p.date) = MONTH(CURDATE() - INTERVAL 1 DAY)
										AND DAY(p.date) = DAY(CURDATE() - INTERVAL 1 DAY)
										AND YEAR(p.date) = YEAR(CURRENT_DATE() - INTERVAL 1 DAY)
										AND p.active = 1
									");
		} else {
			$query = $this->db->placehold("SELECT DISTINCT HOUR(p.date) as hour, price, payed, weight FROM products p WHERE
										MONTH(p.date) = MONTH(CURDATE())
										AND DAY(p.date) = DAY(CURDATE())
										AND YEAR(p.date) = YEAR(CURRENT_DATE())
										AND p.active = 1
									");
		}
		$this->db->query($query);
		return $this->db->results();
	}

	public function getFullPayedProducts() {
		$query = $this->db->placehold("SELECT DISTINCT SUM(price) as p, SUM(weight) as w FROM products p
										WHERE active = 1 AND payed = 1");
		$this->db->query($query);
		return $this->db->result();
	}

	public function getFullNPayedProducts() {
		$query = $this->db->placehold("SELECT DISTINCT SUM(price) as p, SUM(weight) as w FROM products p
										WHERE active = 1 AND payed = 0");
		$this->db->query($query);
		return $this->db->result();
	}
	
}