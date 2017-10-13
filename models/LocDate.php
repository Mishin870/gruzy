<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/models/Core.php');

/**
 *  Модель работы с локализованной датой
 */
class LocDate extends Core {
	
	public function __construct() {
		$this->months = array(
			'ru' => array('янв', 'фев', 'мар', 'апр', 'май', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек'),
			'tr' => array('qqq', 'qqq', 'qqq', 'qqq', 'qqq', 'qqq', 'qqq', 'qqq', 'qqq', 'qqq', 'qqq', 'qqq')
		);
	}
	
	/**
	 *  @brief получить дату в коротком формате 
	 */
	public function getLocalizedShortDate($lang, $date_int) {
		$date = getdate($date_int);
		$month = $date['mon'];
		$day = $date['mday'];
		return $day . ' ' . $months[$lang][$month];
	}

}