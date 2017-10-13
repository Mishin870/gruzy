<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/models/Core.php');

class Database extends Core {
	private $mysqli;
	private $res;
	
	public function __construct() {
		parent::__construct();
		$this->connect();
	}
	
	public function __destruct() {
		$this->disconnect();
	}

	public function getError() {
		return $mysqli->errno . ' :: ' . $mysqli->error;
	}
	
	public function connect() {
		if(!empty($this->mysqli)) {
			return $this->mysqli;
		} else {
			$this->mysqli = new mysqli($this->cfg->db_server, $this->cfg->db_user, $this->cfg->db_password, $this->cfg->db_name);
			$this->mysqli->set_charset("utf8");
		}
		if ($this->mysqli->connect_error) {
			trigger_error("Could not connect to the database: ".$this->mysqli->connect_error, E_USER_WARNING);
			return false;
		} else {
			//подумой
			/*if($cfg["db_charset"])
				$this->mysqli->query('SET NAMES '.$this->config->db_charset);		
			if($cfg["db_sql_mode"])		
				$this->mysqli->query('SET SESSION SQL_MODE = "'.$this->config->db_sql_mode.'"');
			if($this->config->db_timezone)
				$this->mysqli->query('SET time_zone = "'.$this->config->db_timezone.'"');*/
		}
		return $this->mysqli;
	}
	
	public function disconnect() {
		if(!@$this->mysqli->close()) {
			return true;
		} else {
			return false;
		}
	}
	
	public function query() {
		if (is_object($this->res)) $this->res->free();
		$args = func_get_args();
		$q = call_user_func_array(array($this, 'placehold'), $args);		
 		return $this->res = $this->mysqli->query($q);
	}
	
	public function escape($str) {
		return $this->mysqli->real_escape_string($str);
	}
	
	public function placehold() {
		$args = func_get_args();	
		$tmpl = array_shift($args);
		// Заменяем все __ на префикс, но только необрамленные кавычками
		//$tmpl = preg_replace('/([^"\'0-9a-z_])__([a-z_]+[^"\'])/i', "\$1".$this->config->db_prefix."\$2", $tmpl);
		if(!empty($args)) {
			$result = $this->sql_placeholder_ex($tmpl, $args, $error); 
			if ($result === false) {
				$error = "Placeholder substitution error. Diagnostics: \"$error\""; 
				trigger_error($error, E_USER_WARNING); 
				return false; 
			}
			return $result;
		} else {
			return $tmpl;
		}
	}
	
	public function results($field = null) {
		$results = array();
		if (!$this->res) {
			trigger_error($this->mysqli->error, E_USER_WARNING); 
			return false;
		}
		
		if($this->res->num_rows == 0) return array();
		
		while($row = $this->res->fetch_object()) {
			if(!empty($field) && isset($row->$field)) {
				array_push($results, $row->$field);
			} else {
				array_push($results, $row);
			}
		}
		return $results;
	}
	
	public function result($field = null) {
		$result = array();
		if (!$this->res) {
			$this->error_msg = "Could not execute query to database";
			return 0;
		}
		$row = $this->res->fetch_object();
		if (!empty($field) && isset($row->$field)) {
			return $row->$field;
		} elseif(!empty($field) && !isset($row->$field)) {
			return false;
		} else {
			return $row;
		}
	}
	
	public function insert_id() {
		return $this->mysqli->insert_id;
	}
	
	public function num_rows() {
		return $this->res->num_rows;
	}
	
	public function affected_rows() {
		return $this->mysqli->affected_rows;
	}
	
	private function sql_compile_placeholder($tmpl) { 
		$compiled = array(); 
		$p = 0;	 // текущая позиция в строке 
		$i = 0;	 // счетчик placeholder-ов 
		$has_named = false; 
		while (false !== ($start = $p = strpos($tmpl, "?", $p))) {
			// Определяем тип placeholder-а. 
			switch ($c = substr($tmpl, ++$p, 1)) {
				case '%': case '@': case '#': 
					$type = $c; ++$p; break; 
				default: 
					$type = ''; break; 
			}
			// Проверяем, именованный ли это placeholder: "?keyname" 
			if (preg_match('/^((?:[^\s[:punct:]]|_)+)/', substr($tmpl, $p), $pock)) { 
				$key = $pock[1]; 
				if ($type != '#')
					$has_named = true; 
				$p += strlen($key); 
			} else { 
				$key = $i; 
				if ($type != '#') $i++; 
			} 
			// Сохранить запись о placeholder-е. 
			$compiled[] = array($key, $type, $start, $p - $start); 
		} 
		return array($compiled, $tmpl, $has_named); 
	} 
	
	private function sql_placeholder_ex($tmpl, $args, &$errormsg) { 
		// Запрос уже разобран?.. Если нет, разбираем. 
		if (is_array($tmpl)) {
			$compiled = $tmpl; 
		} else {
			$compiled = $this->sql_compile_placeholder($tmpl);
		}
		
		list ($compiled, $tmpl, $has_named) = $compiled; 
		
		// Если есть хотя бы один именованный placeholder, используем 
		// первый аргумент в качестве ассоциативного массива. 
		if ($has_named) $args = @$args[0]; 
		
		// Выполняем все замены в цикле. 
		$p	 = 0;				// текущее положение в строке 
		$out = '';			// результирующая строка 
		$error = false; // были ошибки? 
		
		foreach ($compiled as $num=>$e) { 
			list ($key, $type, $start, $length) = $e; 
			
			// Pre-string. 
			$out .= substr($tmpl, $p, $start - $p); 
			$p = $start + $length; 
	
			$repl = '';		// текст для замены текущего placeholder-а 
			$errmsg = ''; // сообщение об ошибке для этого placeholder-а 
			do { 
				// Это placeholder-константа? 
				if ($type === '#') { 
					$repl = @constant($key); 
					if (NULL === $repl)	 
						$error = $errmsg = "UNKNOWN_CONSTANT_$key"; 
					break; 
				}
				// Обрабатываем ошибку. 
				if (!isset($args[$key])) { 
					$error = $errmsg = "UNKNOWN_PLACEHOLDER_$key"; 
					break; 
				}
				// Вставляем значение в соответствии с типом placeholder-а. 
				$a = $args[$key]; 
				if ($type === '') {
					// Скалярный placeholder. 
					if (is_array($a))
					{ 
						$error = $errmsg = "NOT_A_SCALAR_PLACEHOLDER_$key"; 
						break; 
					} 
					$repl = is_int($a) || is_float($a) ? str_replace(',', '.', $a) : "'".addslashes($a)."'"; 
					break; 
				} 
				// Иначе это массив или список.
				if (is_object($a)) $a = get_object_vars($a);
				
				if (!is_array($a)) {
					$error = $errmsg = "NOT_AN_ARRAY_PLACEHOLDER_$key"; 
					break; 
				}
				if ($type === '@') { 
					// Это список. 
					foreach ($a as $v) {
						if(is_null($v)) {
							$r = "NULL";
						} else {
							$r = "'".@addslashes($v)."'";
						}
						$repl .= ($repl===''? "" : ",").$r; 
					}
				} elseif ($type === '%') { 
					// Это набор пар ключ=>значение. 
					$lerror = array(); 
					foreach ($a as $k=>$v) {
						if (!is_string($k)) {
							$lerror[$k] = "NOT_A_STRING_KEY_{$k}_FOR_PLACEHOLDER_$key"; 
						} else {
							$k = preg_replace('/[^a-zA-Z0-9_]/', '_', $k); 
						}
						if(is_null($v)) {
							$r = "=NULL";
						} else {
							$r = "='".@addslashes($v)."'";
						}
						$repl .= ($repl===''? "" : ", ").$k.$r; 
					} 
					// Если была ошибка, составляем сообщение. 
					if (count($lerror)) { 
						$repl = ''; 
						foreach ($a as $k=>$v) {
							if (isset($lerror[$k])) {
								$repl .= ($repl===''? "" : ", ").$lerror[$k]; 
							} else { 
								$k = preg_replace('/[^a-zA-Z0-9_-]/', '_', $k); 
								$repl .= ($repl===''? "" : ", ").$k."=?"; 
							} 
						} 
						$error = $errmsg = $repl; 
					} 
				} 
			} while (false); 
			if ($errmsg) $compiled[$num]['error'] = $errmsg; 
			if (!$error) $out .= $repl; 
		} 
		$out .= substr($tmpl, $p); 
	
		// Если возникла ошибка, переделываем результирующую строку 
		// в сообщение об ошибке (расставляем диагностические строки 
		// вместо ошибочных placeholder-ов). 
		if ($error) {
			$out = ''; 
			$p	 = 0; // текущая позиция 
			foreach ($compiled as $num=>$e) { 
				list ($key, $type, $start, $length) = $e; 
				$out .= substr($tmpl, $p, $start - $p); 
				$p = $start + $length; 
				if (isset($e['error'])) {
					$out .= $e['error']; 
				} else { 
					$out .= substr($tmpl, $start, $length); 
				} 
			} 
			// Последняя часть строки. 
			$out .= substr($tmpl, $p); 
			$errormsg = $out; 
			return false; 
		} else { 
			$errormsg = false; 
			return $out; 
		} 
	}
	
}