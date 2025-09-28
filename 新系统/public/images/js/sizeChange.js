function size() {
    var wDpr = window.devicePixelRatio;
    var deviceWidth = document.documentElement.clientWidth;
    if (deviceWidth > 768) { //设定最大的字体尺寸用的是768px的即为ipad尺寸
        deviceWidth = 768;
    }
    document.documentElement.style.fontSize = deviceWidth / 6.4 + 'px'; //根据设计的尺寸宽度而定,这边设定的尺寸是750px的。
    document.getElementsByTagName("html")[0].setAttribute("data-dpr", wDpr);
}
size();
window.onresize = function() {
    size();
}

function back(){
	window.history.back();
}
