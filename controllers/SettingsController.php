<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/models/Core.php');

/**
 * Контроллер настроек
 */
class SettingsController extends Core {

	public function ajax() {
		/*if ($this->request->method('post')) {
			ajaxResponse(true, "Incorrect function!");
		}*/
		//ajaxResponse(true, "Incorrect request method!");
		ajaxResponse(true, "Error! This module does not accept ajax!");
	}

	public function show() {
		if ($this->request->method('post')) {
			$this->settings->setSetting('send_cost', $this->request->post('send_cost', 'string'));
			$this->settings->setSetting('self_cost', $this->request->post('self_cost', 'string'));
			$this->settings->setSetting('self_cost_from', $this->request->post('self_cost_from', 'string'));
			$this->settings->setSetting('self_cost_to', $this->request->post('self_cost_to', 'string'));
		}
		global $langCode;
		require_once($_SERVER['DOCUMENT_ROOT'].'/views/'.$langCode.'/settings.html');
	}

}