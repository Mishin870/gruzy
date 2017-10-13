/**
 * Функция-оболочка для отправки ajax запроса на сервер
 * @param module идентификатор модуля-слушателя
 * @param func функция-слушатель
 * @param args параметры к функции
 * @param callBack
 */
function ajaxToCore(module, func, args, callBack) {
	args["m"] = module;
	args["f"] = func;
	$.ajax({
		type: "post",
		data: args,
		url: "http://5.63.154.237/ajax/ajaxCore.php",
		success: function(data, textStatus) {
			callBack.call(this, data);
		}
	});
}

/**
 * Редиректит юзера на страницу с нужным модулем
 * @param module идентификатор модуля-слушателя
 */
function redirectToModule(module) {
	window.location = "http://5.63.154.237/?m=" + module;
}
