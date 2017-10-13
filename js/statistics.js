//var dates = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30"];
//var minuses = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//var pluses =  [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
var dates, minuses, pluses;
//createDiagramm(dates, minuses, pluses);
function createDiagramm(dates, minuses, pluses) {
	$('.statisticstable').html('');
	var maxHeightLineMinuses=0;
	var maxHeightLinePluses=0;
	var lengthmass = dates.length;
	for (var i=0; i<lengthmass; i++){
		if (maxHeightLineMinuses<minuses[i]) {maxHeightLineMinuses=minuses[i];}
		if (maxHeightLinePluses<pluses[i]) {maxHeightLinePluses=pluses[i];}
		console.log(maxHeightLineMinuses+', '+maxHeightLinePluses);
	}
	
	for (var i = 0; i < lengthmass; i++) {
		if (maxHeightLinePluses > maxHeightLineMinuses) {
			maxHeightLinePluses = maxHeightLineMinuses;
		} else {
			maxHeightLineMinuses = maxHeightLinePluses;
		}
		var text = '<div class="one-day" id="'+i+'">';
		if (maxHeightLineMinuses != 0) text += '<div class="gray-line" style="height:'+minuses[i]/maxHeightLineMinuses*195+'px"></div>';
		if (maxHeightLinePluses != 0) text += '<div class="blue-line" style="height:'+pluses[i]/maxHeightLinePluses*100+'px"></div>';
		text += '<div class="dateofoneday">'+dates[i]+'</div></div>';
		$('.statisticstable').html($('.statisticstable').html()+text);
	}
}
$(".month").click(function(){
	month();
});
$(".week").click(function(){
	week();
});
$(".day").click(function(){
	day();
});
function clearActive() {
	$("li").each(function() {
		$(this).removeClass("active");
	});
}
function month() {
	clearActive();
	$("li.month").addClass("active");
	ajaxToCore('common', 'month_stat', {}, function (data) {
		var json = JSON.parse(data);
		if (json.err == '0') {
			minuses = new Array(31);
			pluses = new Array(31);
			dates = new Array(31);
			for (var i = 0; i < 31; i++) {
				minuses[i] = 0;
				pluses[i] = 0;
				dates[i] = i.toString();
			}
			var sub = JSON.parse(json.msg);
			var allp = parseInt(sub.allp);
			var allnp = parseInt(sub.allnp);
			var selfCost = parseInt(sub.selfCost);
			var wp = parseInt(sub.wp);
			var wnp = parseInt(sub.wnp);
			$(".textprice3").html("$" + allnp);
			$(".textprice4").html("$" + allp);
			$(".textprice2").html("$" + (allp + allnp));
			$(".textprice1").html("$" + (allp + allnp - selfCost * (wp + wnp)));
			for (var day in sub.npayed) {
				var n = parseInt(day);
				minuses[n] = sub.npayed[day];
			}
			for (var day in sub.payed) {
				var n = parseInt(day);
				pluses[n] = sub.payed[day];
			}
			createDiagramm(dates, minuses, pluses);
		}
	});
}
function week() {
	clearActive();
	$("li.week").addClass("active");
	ajaxToCore('common', 'week_stat', {}, function (data) {
		var json = JSON.parse(data);
		if (json.err == '0') {
			minuses = new Array(7);
			pluses = new Array(7);
			dates = new Array(7);
			for (var i = 0; i < 7; i++) {
				minuses[i] = 0;
				pluses[i] = 0;
				dates[i] = i.toString();
			}
			var sub = JSON.parse(json.msg);
			var allp = parseInt(sub.allp);
			var allnp = parseInt(sub.allnp);
			var selfCost = parseInt(sub.selfCost);
			var wp = parseInt(sub.wp);
			var wnp = parseInt(sub.wnp);
			$(".textprice3").html("$" + allnp);
			$(".textprice4").html("$" + allp);
			$(".textprice2").html("$" + (allp + allnp));
			$(".textprice1").html("$" + (allp + allnp - selfCost * (wp + wnp)));
			for (var day in sub.npayed) {
				var n = parseInt(day);
				minuses[n] = sub.npayed[day];
			}
			for (var day in sub.payed) {
				var n = parseInt(day);
				pluses[n] = sub.payed[day];
			}
			createDiagramm(dates, minuses, pluses);
		}
	});
}
function day() {
	clearActive();
	$("li.day").addClass("active");
	ajaxToCore('common', 'day_stat', {}, function (data) {
		var json = JSON.parse(data);
		if (json.err == '0') {
			minuses = new Array(24);
			pluses = new Array(24);
			dates = new Array(24);
			for (var i = 0; i < 24; i++) {
				minuses[i] = 0;
				pluses[i] = 0;
				dates[i] = i.toString() + "h";
			}
			var sub = JSON.parse(json.msg);
			var allp = parseInt(sub.allp);
			var allnp = parseInt(sub.allnp);
			var selfCost = parseInt(sub.selfCost);
			var wp = parseInt(sub.wp);
			var wnp = parseInt(sub.wnp);
			$(".textprice3").html("$" + allnp);
			$(".textprice4").html("$" + allp);
			$(".textprice2").html("$" + (allp + allnp));
			$(".textprice1").html("$" + (allp + allnp - selfCost * (wp + wnp)));
			for (var day in sub.npayed) {
				var n = parseInt(day);
				minuses[n] = sub.npayed[day];
			}
			for (var day in sub.payed) {
				var n = parseInt(day);
				pluses[n] = sub.payed[day];
			}
			createDiagramm(dates, minuses, pluses);
		}
	});
}

month();