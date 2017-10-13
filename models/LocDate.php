<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/models/Core.php');

/**
 *  Модель работы с локализованной датой
 */
class LocDate extends Core {
	
	public function __construct() {
		$this->months = array(
			'ru' => array('Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'),
			'tr' => array('Qqq', 'Qqq', 'Qqq', 'Qqq', 'Qqq', 'Qqq', 'Qqq', 'Qqq', 'Qqq', 'Qqq', 'Qqq', 'Qqq')
		);
	}
	
	/**
	 *  @brief получить дату в коротком формате 
	 */
	public function getLocalizedShortDate($lang, $date_int) {
		$date = getdate($date_int);
		$month = $date['mon'];
		$day = $date['mday'];
		return $day . ' ' . $this->months[$lang][intval($month)];
	}

}