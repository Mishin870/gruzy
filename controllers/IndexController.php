<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/models/Core.php');

/**
 * Контроллер главной страницы
 */
class IndexController extends Core {
	
	public function ajax() {
		if ($this->request->method('post')) {
			$function = $this->request->post('f', 'string');
			switch ($function) {
				case 'set_state': {
					$id = $this->request->post('id', 'integer');
					$state = $this->products->getState($id);
					$newState = $this->request->post('state', 'integer');
					if ($newState > $state) {
						$this->products->setState($id, $newState);
						echo 'q1';
						$to_days = $this->settings->getSetting('self_cost_to');
						echo 'q2';
						$product = $this->products->getProduct($id);
						echo 'q3';
						$date = date("d h", strtotime($product->date) + 86400 * $to_days);
						echo 'q4';
						send_sms($product->phone, "Груз ".sprintf("TR-UZ-%03d", $product->id)." принят к перевозке, Дата прибытия ".$date.". К оплате $".$product->price);
						echo 'q5';
						ajaxResponse(false, 'State changed!');
					} else {
						ajaxResponse(true, 'Incorrect state! Next state must be greater!');
					}
					break;
				}
				case 'add_product': {
					$name = $this->request->post('name', 'string');
					$phone = $this->request->post('phone', 'string');
					$address = $this->request->post('address', 'string');
					$payed = $this->request->post('payed', 'integer');
					$note = $this->request->post('note', 'string');
					$weight = floatval($this->request->post('weight'));
					if (empty($name) || empty($phone) || empty($address) || empty($weight)) {
						ajaxResponse(true, "Error! Please, fill all fileds!");
					} else {
						$pricePerWeight = $this->settings->getSetting('send_cost');
						$product = new stdClass;
						$product->name = $name;
						$product->phone = $phone;
						$product->address = $address;
						$product->payed = $payed;
						$product->note = $note;
						$product->weight = $weight;
						$product->price = $weight * $pricePerWeight;
						$product->active = 1;
						$product->state = 0;
						$product->admin_id = intval($_SESSION['user_id']);
						$date = date('Y-m-d H:i:s');
						$product->date = $date;
						$product->date_get = $date;
						$this->products->addProduct($product);
						ajaxResponse(false, print_r($product, true));
					}
					break;
				}
			}
			ajaxResponse(true, "Incorrect function!");
		}
		ajaxResponse(true, "Incorrect request method!");
	}
	
	public function show() {
		global $langCode;
		require_once($_SERVER['DOCUMENT_ROOT'].'/views/'.$langCode.'/index.html');
	}
	
}