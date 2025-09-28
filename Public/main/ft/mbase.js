//document.writeln("<script src=\"/style/layer/layer.js\"></script>");

function goTop(times) {
	if ( !! !times) {
		$(window).scrollTop(0);
		return;
	}

	var sh = $('body').scrollTop(); //移动总距离
	var inter = 13.333; //ms,每次移动间隔时间
	var forCount = Math.ceil(times / inter); //移动次数
	var stepL = Math.ceil(sh / forCount); //移动步长
	var timeId = null;

	function ani() { !! timeId && clearTimeout(timeId);
		timeId = null;
		//console.log($('body').scrollTop());
		if ($('body').scrollTop() <= 0 || forCount <= 0) { //移动端判断次数好些，因为移动端的scroll事件触发不频繁，有可能检测不到有<=0的情况
			$('body').scrollTop(0);
			return;
		}
		forCount--;
		sh -= stepL;
		$('body').scrollTop(sh);
		timeId = setTimeout(function() {
			ani();
		}, inter);
	}
	ani();
}
(function() {
	var a = Math.max(Math.min(document.documentElement.clientWidth || window.innerWidth, 480), 320),
		b = 100;
	362 < a && 375 >= a && (b = Math.floor(a / 320 * 90));
	375 < a && (b = Math.floor(a / 320 * 84));
	window.__baseREM = b;
	document.querySelector("html").style.fontSize = b + "px"
})();

function return_prepage() {
	window.location.href = "" == window.document.referrer || window.document.referrer == window.location.href ? "/" : window.document.referrer
}
var system = {
	win: !1,
	mac: !1,
	xll: !1
},
p = navigator.platform;
system.win = 0 == p.indexOf("Win");
system.mac = 0 == p.indexOf("Mac");
system.x11 = "X11" == p || 0 == p.indexOf("Linux");
//if (system.win || system.mac || system.xll) window.location.href = "http://www.fc55.com";