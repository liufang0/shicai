$(function () {
    var width=window.innerWidth;
    var fontSize=parseFloat($('html').css('font-size'));
    var newFontSize=fontSize/(375/width);
    $('html').css('font-size',newFontSize+'px')
})