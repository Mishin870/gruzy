<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/models/Core.php');

/**
 *  Контроллер логина
 */
class LoginController extends Core {
	
	public function ajax() {
		if ($this->request->method('post')) {
			$function = $this->request->post("f", "string");
			switch ($function) {
				case 'login': {
					$login = $this->request->post("login", "string");
					$pass = $this->request->post("pass", "string");	
					if (empty($login) || empty($pass)) {
						ajaxResponse(true, "Please, fill all inputs!");
					} else {
						$admins = $this->admins->getAdmins(array("login" => $login));
						foreach ($admins as $admin) {
							if ($admin->pass === $pass) {
								$_SESSION['user_id'] = $admin->id;
								ajaxResponse(false, "Login ok. id = ".$admin->id);
							}
						}
						die(json_encode(array(
							"err" => "1",
							"msg" => "Incorrect login and/or password!"
						)));
					}
					break;
				}
				case 'logout': {
					unset($_SESSION['user_id']);
					ajaxResponse(false, 'Logout ok');
					break;
				}
			}
			ajaxResponse(true, "Incorrect function!");
		}
		ajaxResponse(true, "Incorrect request method!");
	}
	
	public function show() {
		//��������� ������ �� view �����
		require_once($_SERVER['DOCUMENT_ROOT'].'/views/log-in.html');
	}
	
}