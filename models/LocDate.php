<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/models/Core.php');

/**
 *  Модель работы с локализованной датой
 */
class LocDate extends Core {
	
	public function __construct() {
		$this->months = array(
			'ru' => array('Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'),
			'tr' => array('ocak', 'şubat', 'mart', 'nisan', 'mayıs', 'haziran', 'temmuz', 'ağustos', 'eylül', 'ekim', 'kasım', 'aralık')
		);
		$this->fullMonths = array(
			'ru' => array("Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"),
			'tr' => array('Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık')
		);
	}
	
	/**
	 *  @brief получить дату в коротком формате 
	 */
	public function getLocalizedShortDate($lang, $date_int) {
		$date = getdate($date_int);
		$month = $date['mon'];
		$day = $date['mday'];
		return $day . ' ' . $this->months[$lang][intval($month) - 1];
	}
	
	/**
	 *  @brief получить дату в длинном формате + время
	 */
	public function getLocalizedDateTime($lang, $date_int) {
		$date = getdate($date_int);
		$month = $date['mon'];
		$day = $date['mday'];
		$minutes = $date['minutes'];
		$seconds = $date['seconds'];
		return $day . ' ' . $this->fullMonths[$lang][intval($month) - 1] . ', ' . $minutes . ':' . $seconds;
	}

}