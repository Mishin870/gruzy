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
							ajaxResponse(true, "Incorrect state! Next state must be greater!");
						}
					}
					$to_days = $this->settings->getSetting('self_cost_to');
					foreach ($ids as $id) {
						$this->products->setState(intval($id), $newState);
						$product = $this->products->getProduct(intval($id));
						$date = date("d h", strtotime($product->date) + 86400 * $to_days);
						send_sms($product->phone, "Груз ".sprintf("TR-UZ-%03d", $product->id)." принят к перевозке, Дата прибытия ".$date.". К оплате $".$product->price);
					}
					ajaxResponse(false, "State changed!");
					break;
				}
				case 'month_stat': {
					$products = $this->products->getMonthProducts();
					$prevProducts = $this->products->getMonthProducts(true);
					$pdays = array();
					$npdays = array();
					$allp = 0; $pallp = 0;
					$allnp = 0; $pallnp = 0;
					$wp = 0; $pwp = 0;
					$wnp = 0; $pwnp = 0;
					foreach ($prevProducts as $product) {
						if ($product->payed == 1) {
							$pallp += $product->price;
							$pwp += $product->weight;
						} else {
							$pallnp += $product->price;
							$pwnp += $product->weight;
						}
					}
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
					$ret = array('payed'=>$pdays, 'npayed'=>$npdays, 'allp'=>$allp, 'allnp'=>$allnp, 'wp'=>$wp, 'wnp'=>$wnp, 'selfCost'=>$selfCost, 'pallp'=>$pallp, 'pallnp'=>$pallnp, 'pwp'=>$pwp, 'pwnp'=>$pwnp);
					ajaxResponse(false, json_encode($ret));
					break;
				}
				case 'week_stat': {
					$products = $this->products->getWeekProducts();
					$prevProducts = $this->products->getMonthProducts(true);
					$pdays = array();
					$npdays = array();
					$allp = 0; $pallp = 0;
					$allnp = 0; $pallnp = 0;
					$wp = 0; $pwp = 0;
					$wnp = 0; $pwnp = 0;
					foreach ($prevProducts as $product) {
						if ($product->payed == 1) {
							$pallp += $product->price;
							$pwp += $product->weight;
						} else {
							$pallnp += $product->price;
							$pwnp += $product->weight;
						}
					}
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
					$ret = array('payed'=>$pdays, 'npayed'=>$npdays, 'allp'=>$allp, 'allnp'=>$allnp, 'wp'=>$wp, 'wnp'=>$wnp, 'selfCost'=>$selfCost, 'pallp'=>$pallp, 'pallnp'=>$pallnp, 'pwp'=>$pwp, 'pwnp'=>$pwnp);
					ajaxResponse(false, json_encode($ret));
					break;
				}
				case 'day_stat': {
					$products = $this->products->getDayProducts();
					$prevProducts = $this->products->getMonthProducts(true);
					$phours = array();
					$nphours = array();
					$allp = 0; $pallp = 0;
					$allnp = 0; $pallnp = 0;
					$wp = 0; $pwp = 0;
					$wnp = 0; $pwnp = 0;
					foreach ($prevProducts as $product) {
						if ($product->payed == 1) {
							$pallp += $product->price;
							$pwp += $product->weight;
						} else {
							$pallnp += $product->price;
							$pwnp += $product->weight;
						}
					}
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
					$ret = array('payed'=>$phours, 'npayed'=>$nphours, 'allp'=>$allp, 'allnp'=>$allnp, 'wp'=>$wp, 'wnp'=>$wnp, 'selfCost'=>$selfCost, 'pallp'=>$pallp, 'pallnp'=>$pallnp, 'pwp'=>$pwp, 'pwnp'=>$pwnp);
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
					$lang = $this->request->post('lang', 'string');
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
						//$date = date("d F, H:i, ", strtotime($product->date));
						$date = $this->locdate->getLocalizedDateTime($lang, strtotime($product->date));
						$name = $product->name;
						$price = $product->price;
						$weight = $product->weight;
						$phone = $product->phone;
						ajaxResponse(false, json_encode(array(
							'from'=>$from,
							'to'=>$to,
							'month'=>$month,
							'day'=>$day,
							'payed'=>$payed,
							'state'=>$state,
							'date'=>$date,
							'name'=>$name,
							'price'=>$price,
							'weight'=>$weight,
							'phone'=>$phone
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
					break;
				}
				case 'get_events': {
					$events = $this->events->getEvents();
					$newStates = array();
					$toSend = array();
					$i = 0;
					foreach ($events as $event) {
						$pid = intval($event->product_id);
						if (!isset($newStates[$pid])) {
							$state = $this->products->getState($pid);
							$newStates[$pid] = $state;
						}
						$prevState = intval($event->current_state);
						if ($newState != $prevState) {
							$sendInfo = new stdClass;
							$sendInfo->id = $i;
							$i++;
							$sendInfo->chatid = $event->chatid;
							$sendInfo->product_id = $event->product_id;
							$toSend[] = $sendInfo;
						}
					}
					//$this->events->deleteAllEvents();
					//$ret = array();
					//var_dump($events);
					ajaxResponse(false, json_encode($toSend));
					break;
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