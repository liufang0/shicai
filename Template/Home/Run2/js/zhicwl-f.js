$(function () {

    //控制滚动条位置
    $(".zhicwl-xfqugao").on("click", function () {
        var laksr = $("#myIframe").offset().top;
        $('html,body').animate({
            scrollTop: laksr - 6
        }, 300);
    });

    //清除选中项
    $(".zhicwl-fxalpr .zhicwl-fxalpr-an1").on("click", function () {
        var ino = $("#myIframe").contents().find(".zhicwl-tc-01").find("dt.on").attr("ino");

        if ($("#myIframe").contents().find(".zhicwl-tc-01").find("dt").hasClass("on")) {
            $("#myIframe").contents().find(".zhicwl-qiejlf").find(">div").eq(ino - 1).find(".zhicwl-keapr").find(">div").find("dl").find("dt").removeClass("on");
        } else {
            var stck = $("#myIframe").contents().find(".zhicwl-hdrte-nav").find("ul").find("li.on").attr("itx");
            $("#myIframe").contents().find(".zhicwl-qiejlf").find(">div").eq(stck - 1).find(".zhicwl-keapr").find(">div").find("dl").find("dt").removeClass("on");
        }
        ;

        $(this).removeClass("on");
        $(this).siblings(".zhicwl-fxalpr-an3").removeClass("on");
    });

    $(".zhicwl-fxalpr-an1").on("click", function () {
        $("#zhicwl-sqjdrt, #zhicwl-tzjev").text("0");
    });

    var tz = null;
    $(".zhicwl-fxalpr .zhicwl-fxalpr-an3").on("click", function () {
        if ($(this).hasClass("on")) {
            tz=$("#myIframe")[0].contentWindow.checkData();
            if (!tz) {
                return false;
            }
//                $("#myIframe")[0].contentWindow.wsTz(tz,10);
            


            if ($("#myIframe").contents().find(".zhicwl-tc-01").find("dt").hasClass("on")) {
                var titsrev = $("#myIframe").contents().find(".zhicwl-tc-01").find("dt.on").find("span").text();
            } else {
                var titsrev = $("#myIframe").contents().find(".zhicwl-hdrte-nav").find("ul").find("li.on").text();
            }
            ;
            $(".zhicwl-jdoas h2").text(titsrev);
            $(".zhicwl-qdxldfp").show();
        }
        ;
    });
    $('.zhicwl-qxldpr-an2').click(function(){
        if($('#my_money').val()>0){
            $("#myIframe")[0].contentWindow.wsTz(tz,$('#my_money').val());
            $(".zhicwl-qdxldfp").hide();
            $(".zhicwl-xzqkl").trigger('click');
            $(".zhicwl-fxalpr .zhicwl-fxalpr-an1").trigger('click');
        }else{
            alert("请选择投注金额");
        }
    });
    //确定下注弹窗
    $(".zhicwl-jdlptr, .zhicwl-qxldpr-an1").on("click", function () {
        $(".zhicwl-qdxldfp").hide();
    });

    $(".zhicwl-sailsp dl dt").on("click", function () {
        var indsrt = $(this).find("b").text();
        var zhicwl_tzjev = $("#zhicwl-tzjev").text();
        var ankdfrt = $(".zhicwl-xzje-inp input").val();
        if (ankdfrt == "¥0.00") {
            var ankdfrt = "0";
        }
        ;
        $(".zhicwl-xzje-inp input").addClass("on").val(parseInt(ankdfrt) + parseInt(indsrt));
        $("#zhicwl-jgr").text((parseInt(ankdfrt) + parseInt(indsrt)) * zhicwl_tzjev)
    });

    $(".zhicwl-xzqkl").on("click", function () {
        $("#zhicwl-jgr").text("0");
        $(".zhicwl-xzje-inp input").removeClass("on").val("¥0.00");
    });

});