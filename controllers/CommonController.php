<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/models/Core.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/models/SMSApi.php');

/**
 * Контроллер общих действий
 */
class CommonController extends Core {

	public function ajax() {
		if ($this->request->method('post')) {
			$function = $this->request->post("f", "string");
			switch ($function) {
				case 'setlang': {
					$langCode = $this->request->post('lang', 'string');
					$this->admins->setLang($_SESSION['user_id'], $langCode);
					ajaxResponse(false, "Set lang ok");
					break;
				}
				case 'archive_sel': {
					$ids = $this->request->post('ids');
					foreach ($ids as $id) {
						$this->products->setActive(intval($id), 0);
					}
					ajaxResponse(false, "Set archive ok");
					break;
				}
				case 'delete_sel': {
					$ids = $this->request->post('ids');
					foreach ($ids as $id) {
						$this->products->deleteProduct(intval($id));
					}
					ajaxResponse(false, "Delete ok");
					break;
				}
				case 'state_sel': {
					$ids = $this->request->post('ids');
					$newState = $this->request->post('state', 'integer');
					foreach ($ids as $id) {
						$state = $this->products->getState(intval($id));
						if ($newState <= $state) {
							ajaxResponse(true, "Неверное состояние груза! Новое состояние должно быть больше предыдущего!");
						}
					}
					$to_days = $this->settings->getSetting('self_cost_to');
					foreach ($ids as $id) {
						$this->products->setState(intval($id), $newState);
						$product = $this->products->getProduct(intval($id));
						$date = date("d h", strtotime($product->date) + 86400 * $to_days);
						send_sms($product->phone, "Груз ".sprintf("TR-UZ-%03d", $product->id)." принят к перевозке, Дата прибытия ".$date.". К оплате $".$product->price);
					}
					ajaxResponse(false, "Состояние изменено!");
					break;
				}
				case 'month_stat': {
					$products = $this->products->getMonthProducts();
					$pdays = array();
					$npdays = array();
					$allp = 0;
					$allnp = 0;
					$wp = 0;
					$wnp = 0;
					foreach ($products as $product) {
						$day = intval($product->day);
						if ($product->payed == 1) {
							if (isset($pdays[$day])) {
								$pdays[$day] += $product->price;
							} else {
								$pdays[$day] = $product->price;
							}
							$allp += $product->price;
							$wp += $product->weight;
						} else {
							if (isset($npdays[$day])) {
								$npdays[$day] += $product->price;
							} else {
								$npdays[$day] = $product->price;
							}
							$allnp += $product->price;
							$wnp += $product->weight;
						}
					}
					$selfCost = $this->settings->getSetting("self_cost");
					$ret = array('payed'=>$pdays, 'npayed'=>$npdays, 'allp'=>$allp, 'allnp'=>$allnp, 'wp'=>$wp, 'wnp'=>$wnp, 'selfCost'=>$selfCost);
					ajaxResponse(false, json_encode($ret));
					break;
				}
				case 'week_stat': {
					$products = $this->products->getWeekProducts();
					$pdays = array();
					$npdays = array();
					$allp = 0;
					$allnp = 0;
					$wp = 0;
					$wnp = 0;
					foreach ($products as $product) {
						$day = intval($product->day);
						if ($product->payed == 1) {
							if (isset($pdays[$day])) {
								$pdays[$day] += $product->price;
							} else {
								$pdays[$day] = $product->price;
							}
							$allp += $product->price;
							$wp += $product->weight;
						} else {
							if (isset($npdays[$day])) {
								$npdays[$day] += $product->price;
							} else {
								$npdays[$day] = $product->price;
							}
							$allnp += $product->price;
							$wnp += $product->weight;
						}
					}
					$selfCost = $this->settings->getSetting("self_cost");
					$ret = array('payed'=>$pdays, 'npayed'=>$npdays, 'allp'=>$allp, 'allnp'=>$allnp, 'wp'=>$wp, 'wnp'=>$wnp, 'selfCost'=>$selfCost);
					ajaxResponse(false, json_encode($ret));
					break;
				}
				case 'day_stat': {
					$products = $this->products->getDayProducts();
					$phours = array();
					$nphours = array();
					$allp = 0;
					$allnp = 0;
					$wp = 0;
					$wnp = 0;
					foreach ($products as $product) {
						$hour = intval($product->hour);
						if ($product->payed == 1) {
							if (isset($phours[$hour])) {
								$phours[$hour] += $product->price;
							} else {
								$phours[$hour] = $product->price;
							}
							$allp += $product->price;
							$wp += $product->weight;
						} else {
							if (isset($nphours[$hour])) {
								$nphours[$hour] += $product->price;
							} else {
								$nphours[$hour] = $product->price;
							}
							$allnp += $product->price;
							$wnp += $product->weight;
						}
					}
					$selfCost = $this->settings->getSetting("self_cost");
					$ret = array('payed'=>$phours, 'npayed'=>$nphours, 'allp'=>$allp, 'allnp'=>$allnp, 'wp'=>$wp, 'wnp'=>$wnp, 'selfCost'=>$selfCost);
					ajaxResponse(false, json_encode($ret));
					break;
				}
				case 'full_stat': {
					$products = $this->products->getMonthProducts();
					$pdays = array();
					$npdays = array();
					foreach ($products as $product) {
						$day = intval($product->day);
						if ($product->payed == 1) {
							if (isset($pdays[$day])) {
								$pdays[$day] += $product->price;
							} else {
								$pdays[$day] = $product->price;
							}
						} else {
							if (isset($npdays[$day])) {
								$npdays[$day] += $product->price;
							} else {
								$npdays[$day] = $product->price;
							}
						}
					}
					$payedProducts = $this->products->getFullPayedProducts();
					$npayedProducts = $this->products->getFullNPayedProducts();
					$allp = $payedProducts->p;
					$allnp = $npayedProducts->p;
					$wp = $payedProducts->w;
					$wnp = $npayedProducts->w;
					$selfCost = $this->settings->getSetting("self_cost");
					$ret = array('payed'=>$pdays, 'npayed'=>$npdays, 'allp'=>$allp, 'allnp'=>$allnp, 'wp'=>$wp, 'wnp'=>$wnp, 'selfCost'=>$selfCost);
					ajaxResponse(false, json_encode($ret));
					break;
				}
				case 'product_info': {
					$id = $this->request->post('id', 'integer');
					$from = $this->settings->getSetting('self_cost_from');
					$to = $this->settings->getSetting('self_cost_to');
					$product = $this->products->getProduct($id);
					if (empty($product)) {
						ajaxResponse(true, "Product not found!");
					} else {
						$date = getdate(strtotime($product->date) + 86400 * $to);
						$month = $date['mon'];
						$day = $date['mday'];
						$payed = $product->payed;
						$state = $product->state;
						$date = date("d F, H:i, ", strtotime($product->date));
						$name = $product->name;
						$price = $product->price;
						ajaxResponse(false, json_encode(array(
							'from'=>$from,
							'to'=>$to,
							'month'=>$month,
							'day'=>$day,
							'payed'=>$payed,
							'state'=>$state,
							'date'=>$date,
							'name'=>$name,
							'price'=>$price
						)));
					}
					break;
				}
				case 'common_info': {
					$weight = $this->request->post('weight', 'integer');
					$from = $this->settings->getSetting('self_cost_from');
					$to = $this->settings->getSetting('self_cost_to');
					$selfCost = $this->settings->getSetting('self_cost');
					$price = $weight * $selfCost;
					ajaxResponse(false, json_encode(array(
						'from'=>$from,
						'to'=>$to,
						'selfCost'=>$selfCost,
						'price'=>$price
					)));
					break;
				}
				case 'add_product_bot': {
					$name = $this->request->post('name', 'string');
                    $phone = $this->request->post('phone', 'string');
                    $weight = $this->request->post('weight', 'integer');
                    $payed = $this->request->post('payed', 'integer');
                    $address = $this->request->post('address', 'string');
                    $note = $this->request->post('note', 'string');

					if (empty($name) || empty($phone) || empty($address) || empty($note) || empty($weight)) {
						ajaxResponse(true, "Error! Please, fill all fileds!");
					} else {
						$selfCost = $this->settings->getSetting('self_cost');
						$price = $selfCost * $weight;
						$product = new stdClass;
						$product->name = $name;
						$product->phone = $phone;
						$product->address = $address;
						$product->payed = $payed;
						$product->note = $note;
						$product->weight = $weight;
						$product->price = $price;
						$product->active = 1;
						$product->state = 0;
						$product->admin_id = -1;
						$date = date('Y-m-d H:i:s');
						$product->date = $date;
						$product->date_get = $date;
						$this->products->addProduct($product);
						ajaxResponse(false, print_r($product, true));
					}
				}
			}
			ajaxResponse(true, "Incorrect function!");
		}
		ajaxResponse(true, "Incorrect request method!");
	}

	public function show() {
		die("ERROR!");
	}

}