$(function () {

    //弹窗开启与关闭
    $(".zhicwl-xiaz").on("click", function () {
        $(".zhicwl-csplr").show();
        $(".zhicwl-fxalpr", parent.document).css("visibility", "visible");
        $(".zhicwl-xfqugao", parent.document).click();
    });
    $(".zhicwl-guanb").on("click", function () {
        $(".zhicwl-csplr").hide();
        $("html,body", parent.document).animate({
            scrollTop: 0
        }, 300);
        $(".zhicwl-fxalpr", parent.document).css("visibility", "hidden");
    });

    //顶部TAB切换
    $(".zhicwl-tc-01 dt").each(function (index) {
        index++
        $(this).attr("ino", index);
    });
    $(".zhicwl-tc-01 dt").on("click", function () {
        if($(this).hasClass('on')){
            return false;
        }
        if ($(".zhicwl-qiejlf > div").eq($(this).index()).find(".zhicwl-keapr").find("dl").find("dt").hasClass("on")) {
            $("body", parent.document).find(".zhicwl-fxalpr-an1, .zhicwl-fxalpr-an3").addClass("on");
        } else {
            $(".zhicwl-hdrte-nav").hide();
            $(".zhicwl-hdrte-nav ul li").removeClass("on");
            $("body", parent.document).find(".zhicwl-fxalpr-an1, .zhicwl-fxalpr-an3").removeClass("on");
        }
        ;
        $(".zhicwl-tc-01 dt").eq($(this).index()).addClass("on").siblings().removeClass('on');
        $(".zhicwl-qiejlf .zhicwl-qiejlf-tab").hide().eq($(this).index()).show();

        $(".zhicwl-qiejlf .zhicwl-qiejlf-tab .zhicwl-keapr dt").removeClass('on');
        //取得投注总数
        $("body", parent.document).find("#zhicwl-sqjdrt").text("0");
        $("body", parent.document).find("#zhicwl-tzjev").text("0");

        var stck = $(".zhicwl-tc-01 dt.on").attr("ino");
        if ($(".zhicwl-qiejlf > div").eq(stck - 1).find(".zhicwl-keapr").find(">div").length > 1) {
            var msuoas = $(".zhicwl-qiejlf > div").eq(stck - 1).find(".zhicwl-keapr").find(">div:first-child").find("dl").find("dt.on").length;
            var qitask = $(".zhicwl-qiejlf > div").eq(stck - 1).find(".zhicwl-keapr").find(">div:nth-child(n+2)").find("dl").find("dt.on").length;
            var msjqit = parseInt(msuoas * qitask);
        } else {
            var msuoas = $(".zhicwl-qiejlf > div").eq(stck - 1).find(".zhicwl-keapr").find(">div").find("dl").find("dt.on").length;
            var msjqit = parseInt(msuoas);
        }

        $("body", parent.document).find("#zhicwl-sqjdrt").text(msjqit);
        $("body", parent.document).find("#zhicwl-tzjev").text(msjqit);
    });

    //下拉
    $(".zhicwl-hdrte-nav ul li").each(function (index) {
        var index = index + 4;
        $(this).attr("itx", index);
        $(".zhicwl-hdrte-nav ul li").on("click", function (event) {
            $(".zhicwl-qiejlf > div").hide().eq($(this).attr("itx") - 1).show();
            $(".zhicwl-hdrte-nav").hide();
            $(".zhicwl-hdrte h6").removeClass("on");
            $(".zhicwl-hdrte-nav ul li").removeClass('on');
            $(this).addClass("on");
            $(".zhicwl-tc-01 dt").removeClass("on");

            $("body", parent.document).find("#zhicwl-sqjdrt").text("0");
            $("body", parent.document).find("#zhicwl-tzjev").text("0");

            var stck = $(".zhicwl-hdrte-nav ul li.on").attr("itx");
            var msuoas = $(".zhicwl-qiejlf > div").eq(stck - 1).find(".zhicwl-keapr").find(">div").find("dl").find("dt.on").length;
            var msjqit = parseInt(msuoas);
            $("body", parent.document).find("#zhicwl-sqjdrt").text(msjqit);
            $("body", parent.document).find("#zhicwl-tzjev").text(msjqit);
            if (msjqit == "0") {
                $("body", parent.document).find(".zhicwl-fxalpr-an1, .zhicwl-fxalpr-an3").removeClass("on");
            } else {
                $("body", parent.document).find(".zhicwl-fxalpr-an1, .zhicwl-fxalpr-an3").addClass("on");
            }
            event.stopPropagation();
        });
    });
    $(".zhicwl-hdrte").on("click", function () {
        $(this).find("h6").toggleClass("on");
        $(".zhicwl-hdrte-nav").toggle();
    });

    //点击高亮
    $(".zhicwl-keapr > div dl dt").on("click", function () {
        $(this).toggleClass("on");

        //取得投注总数
        if ($(".zhicwl-tc-01 dt").hasClass("on")) {
            var stck = $(".zhicwl-tc-01 dt.on").attr("ino");
            if ($(".zhicwl-qiejlf > div").eq(stck - 1).find(".zhicwl-keapr").find(">div").length > 1) {
                var msuoas = $(".zhicwl-qiejlf > div").eq(stck - 1).find(".zhicwl-keapr").find(">div:first-child").find("dl").find("dt.on").length;
                var qitask = $(".zhicwl-qiejlf > div").eq(stck - 1).find(".zhicwl-keapr").find(">div:nth-child(n+2)").find("dl").find("dt.on").length;
                var msjqit = parseInt(msuoas * qitask);
            } else {
                var msuoas = $(".zhicwl-qiejlf > div").eq(stck - 1).find(".zhicwl-keapr").find(">div").find("dl").find("dt.on").length;
                var msjqit = parseInt(msuoas);
            }
        } else {
            var stck = $(".zhicwl-hdrte-nav ul li.on").attr("itx");
            var msuoas = $(".zhicwl-qiejlf > div").eq(stck - 1).find(".zhicwl-keapr").find(">div").find("dl").find("dt.on").length;
            var msjqit = parseInt(msuoas);
        }

        $("body", parent.document).find("#zhicwl-sqjdrt").text(msjqit);
        $("body", parent.document).find("#zhicwl-tzjev").text(msjqit);

        if (msjqit == "0") {
            $("body", parent.document).find(".zhicwl-fxalpr-an1, .zhicwl-fxalpr-an3").removeClass("on");
        } else {
            $("body", parent.document).find(".zhicwl-fxalpr-an1, .zhicwl-fxalpr-an3").addClass("on");
        }
    });

});