<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/models/Core.php');

/**
 * Контроллер статистики
 */
class StatisticsController extends Core {

	public function ajax() {
		if ($this->request->method('post')) {
			ajaxResponse(true, "Incorrect function!");
		}
		ajaxResponse(true, "Incorrect request method!");
	}

	public function show() {
		global $langCode;
		require_once($_SERVER['DOCUMENT_ROOT'].'/views/'.$langCode.'/statistics.html');
	}

}