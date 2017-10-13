var allcheckon=0;
var i=0;
var b=0;

function recalcSelectionCount() {
	$("#selectionCounter").html($("tr.bgtable").length);
}
function getSelectedIds() {
	var ids = [];
	$("tr.bgtable").each(function() {
		//var id = parseInt($($(this).find("td").get(1)).html());
		var id = parseInt($(this).attr("pid"));
		ids.push(id);
	});
	return ids;
}
$(".checkboxtable + label").click(function(){
	$(this).siblings("input").trigger('click');
	$(this).parent().parent().toggleClass("bgtable");
	$(".checkboxtable:checked").each(function(){
		b++;
	});
	if (allcheckon==0 && b==1) {
		$(".checkboxtable + label").addClass("active");
		$('.functionscheck').show();
		$('.linkadd-button').hide();
	}
	if (b<1) {
		$(".checkboxtable + label").removeClass("active");
		$('.functionscheck').hide();
		$('.linkadd-button').show();
	}
	b=0;
	recalcSelectionCount();
});
$("label.checkall").click(function(){
	if (allcheckon==0)	{
		$(".checkboxtable").prop('checked', true);
		$(".checkboxtable + label").addClass("active");
		$(".content tr").addClass("bgtable");
		$('.functionscheck').show();
		$('.linkadd-button').hide();
		allcheckon=1;
	}
	else {
		$(".checkboxtable").prop('checked', false);
		$(".checkboxtable + label").removeClass("active");
		$(".content tr").removeClass("bgtable");
		$('.functionscheck').hide();
		$('.linkadd-button').show();
		allcheckon=0;
	}
	recalcSelectionCount();
});
$(".opencargo").click(function(){
	$(".right-sidebar").toggleClass("active");
});
$(".backcargo").click(function(){
	$(".right-sidebar").toggleClass("active");
});
$(".border-avatar").click(function(){
	$(".user-menu").toggle();
});
$(".content tr").each(function(){
	$(this).attr('id', i);
	i++;
});
$(".workertable").click(function(){
	n=parseInt($(this).attr('id'))+1;
	//$('#'+n).html('<td colspan="5"><div class="leftinfoblock"><div class="printlabel"><a href="#" class="printbuttonlink"><div class="printbutton">Печать наклейки</div></a><div class="datetext">Добавлен 21 сентября, 12:03</div></div><div class="textinfogruz"><span class="namefield">Адрес доставки</span>ул. Новослободская 21, Ташкент</div><div class="textinfogruz"><span class="namefield">Характер груза</span>Женская одежда и текстиль</div><div class="textinfogruz"><span class="namefield">Примечание</span>Груз упакован. Спецпометка. </div></div><div class="rightinfoblock"><div class="textinfogruz textinfogruzright">Принято в головном офисе<span class="namefield dateglav">25 сентябрь, 11:03, Стамбул</span></div><div class="makeedit"><a href="#" class="makeeditbuttonlink"><div class="makeeditbutton">Внести</div></a>Прибыло на территорию Узбекистана</div><div class="makeedit"><a href="#" class="makeeditbuttonlink"><div class="makeeditbutton">Внести</div></a>Доставлено получателю</div></div></td>');
	$('#'+n).toggle();
});
$(".chradio2").click(function(){
	$(".chradio2").addClass("active");
	$(".chradio1").removeClass("active");
	$(".radiodzen1").trigger('click');
});
$(".chradio1").click(function(){
	$(".chradio1").addClass("active");
	$(".chradio2").removeClass("active");
	$(".radiodzen2").trigger('click');
});






//=================функции выделения=====================
$("#funcArchive").click(function() {
	var ids = getSelectedIds();
	ajaxToCore('common', 'archive_sel', {'ids': ids}, function (data) {
		var json = JSON.parse(data);
		if (json.err == '1') {
			alert('Ошибка! ' + json.msg);
		} else {
			window.location.reload();
		}
	});
});
$("#funcDelete").click(function() {
	var ids = getSelectedIds();
	ajaxToCore('common', 'delete_sel', {'ids': ids}, function (data) {
		var json = JSON.parse(data);
		if (json.err == '1') {
			alert('Ошибка! ' + json.msg);
		} else {
			window.location.reload();
		}
	});
});
function stateSel(state) {
	var ids = getSelectedIds();
	ajaxToCore('common', 'state_sel', {'ids': ids, 'state': state}, function (data) {
		var json = JSON.parse(data);
		if (json.err == '1') {
			alert('Ошибка! ' + json.msg);
		} else {
			window.location.reload();
		}
	});
}
$("#funcState1").click(function() {
	stateSel(1);
});
$("#funcState2").click(function() {
	stateSel(2);
});