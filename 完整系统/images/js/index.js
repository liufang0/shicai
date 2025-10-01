var tuichuFn = function () {
    $.ajax({
        url: "/moveSession",
        async:false,
        success: function(data){
            window.location.href = "/reLogin";
        },
        error:function(data)
        {
        }
    });
}


var afterInit = function(){
    var  intervalMeiqia = setInterval(function(){
        if($("#MEIQIA-BTN-HOLDER").length>0){
            $("#MEIQIA-BTN-HOLDER").css("top","8.29rem");
            $("#MEIQIA-BTN-ICON").css("font-size","0.25rem").css("color","#fff").css("margin","0.3rem 0.23rem").text("客服").removeClass("MEIQIA-ICON");
            clearInterval(intervalMeiqia)
        }
    },500);

}

$(function(){
  
    var bannerSwiper = new Swiper('.banner', {
                    //pagination: '.swiper-pagination',
                    loop: true,
                    autoplay : 3000,
                    grabCursor: true,
                    //paginationClickable: true
                })
    
    return;
     $.ajax({
        url: "/getgameContent",
        async:false,
        success: function(data){

                ajaxobj=eval("("+data+")");
                var words =ajaxobj.index_content.split(",");
                var str="";
                for(var i=1;i<words.length;i++){
                  str=str+"<li class='swiper-slide'><img src='"+words[i]+" '/></li>"
                }

                $("#index_content").html(str)

                // 通栏轮播
                var bannerSwiper = new Swiper('.banner', {
                    //pagination: '.swiper-pagination',
                    loop: true,
                    autoplay : 3000,
                    grabCursor: true,
                    //paginationClickable: true
                })

        },
        error:function(data)
        {

        }
    });
    if($("#loginType").val()=='RG'){

    }
    $(".tuichuup").show();
    $(".tuichuup").click(function () {
        tuichuFn();
    })

    // 传递顾客信息
    _MEIQIA('metadata', {
        id: $("#user_id").val(), // 美洽默认字段
        name: $("#user_nickname").val() // 自定义字段
    });
    if(entId){
        _MEIQIA('init');//初始化
        setTimeout(afterInit(),200);
    }
})
