/**
 * Created by HCHT- on 16-11-17.
 */
function GetRequest() {
    var url = decodeURI(location.search); //获取url中"?"符后的字串
    var theRequest = new Object();
    if (url.indexOf("?") != -1) {
        var str = url.substr(1);
        strs = str.split("&");
        for(var i = 0; i < strs.length; i ++) {
            theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);
        }
    }
    return theRequest;
}

function timeHandle(time){
    //把当前时间转成日期格式
    if(time != '' && time != undefined){
        var oDate = new Date(Number(time)*1000);
    }else{
        var oDate = new Date();
    }

    var oYear = oDate.getFullYear(),
        oMonth = oDate.getMonth()+1,
        oDay = oDate.getDate(),
        oHour = oDate.getHours(),
        oMin = oDate.getMinutes();
    //oSen = oDate.getSeconds(),

    oMonth<9 ? oMonth ="0"+oMonth:oMonth;
    oDay<9 ? oDay ="0"+oDay:oDay;
    oHour<9 ? oHour ="0"+oHour:oHour;
    oMin<9 ? oMin ="0"+oMin:oMin;
    var oTime = oYear +'-'+ oMonth +'-'+ oDay +' '+ oHour +':'+ oMin;//最后拼接时间
    return oTime;
}
//function add0(m){return m<10?'0'+m:m }
//function timeHandle(shijianchuo)
//{
////shijianchuo是整数，否则要parseInt转换
//    var time = new Date(parseInt(shijianchuo)*1000);
//    var y = time.getFullYear();
//    var m = time.getMonth()+1;
//    var d = time.getDate();
//    var h = time.getHours();
//    var mm = time.getMinutes();
//    var s = time.getSeconds();
//    return y+'-'+add0(m)+'-'+add0(d)+' '+add0(h)+':'+add0(mm)+':'+add0(s);
//}


function changeColor () {
    var oSum = $(".num_r");
    var arrRed = [3,6,9,12,15,18,21,24];
    var arrGreen = [1,4,7,10,16,19,22,25];
    var arrBlue = [2,5,8,11,17,20,23,26];
    var arrBlack = [0,13,14,27];

    for(var i=0; i<oSum.size(); i++){
        var n = Number(oSum.eq(i).text())

        if($.inArray(n, arrRed) != -1){
            oSum.eq(i).addClass("bg_red");
        }else if($.inArray(n, arrGreen) != -1){
            oSum.eq(i).addClass("bg_green");
        }else if($.inArray(n, arrBlue) != -1){
            oSum.eq(i).addClass("bg_blue");
        }else if($.inArray(n, arrBlack) != -1){
            oSum.eq(i).addClass("bg_black");
        }
    }
}

var fnSet=function(){};

fnSet.alert =function(text,sibtne){                //提示框
    var _alert = $(".popupAlert");
    a_sibtne = function() {
        $(this).parents(".popupAlert").remove();
        if (typeof(sibtne) == 'function') {
            sibtne();
        }
    }
    var alert ='<div class="popupAlert"><div class="config-alert"><div class="tit">提示</div><p>'+text+'</p><div class="btn"><button class="confirm">确认</button></div></div></div>'
    // $("body").append(alert);
    // if (_alert.length) {
    //     _alert.show().find(".config p").html(text);
    // } else {
    $("body").append(alert);
    // }
    $(".confirm").off("click").on("click", a_sibtne);
    // $(".confirm").on("click",function(){
    //     $(this).parents(".popupAlert").remove();
    // })
}

jQuery.confirm = function(content,sibtne,cile) {  //询问框  确认和取消按钮
    a_sibtne = function(){
        $(this).parents("#box-confirm").hide();
        if (typeof(sibtne) == 'function') {
            sibtne();
        }
    }
    b_sibtne = function(){
        $(this).parents("#box-confirm").hide();
        if (typeof(cile) == 'function') {
            cile();
        }
    }

    var html= '<div class="popupAlert" id="box-confirm">'+
        '<div class="config-confirm"><div class="tit">提示</div><p class="box-confirm-con">'+
        content +
        '</p><div class="btn confirmWap">'+
        '<button class="confirm left" id="box-confirm-submit">确认</button>'+
        '<button class="cancel  right" id="box-confirm-cancel">取消</button>'+
        '</div></div></div>';

    // var html= '<div class="cenWarp"><div class="cenCon"><div class="cenConText">'+content+'</div><button id="sibtne">确定</button><button id="cile">取消</button></div>';
    var confirm =$("#box-confirm");
    if(confirm.length){
        confirm.show();
        confirm.find(".box-confirm-con").html(content);
    }else{
        $("body").append(html);
    }
    $("#box-confirm-submit").off("click").on("click",a_sibtne);
    $("#box-confirm-cancel").off("click").on("click",b_sibtne);
}

$(function(){

    changeColor();
    if($(".headerRight li.icoNews").attr("data-new") > 0){
        $(".headerRight li.icoNews").addClass("oAfter")
    }

    /*连接href*/
    $("*").on("click","[data-href]",function(){
        //alert($(this).attr("data-href"));
        var href = $(this).attr("data-href");
            window.location.href= href;
    });
    // $("*").on("click","[data-lhc]",function(){
    //     var url = $(this).attr('data-lhc');
    //     layer.open({
    //         content: "<span style='font-size: 30px'>使用APP，更快！更流畅！是否要去下载APP？</span>",
    //         skin: 'winning-class', //样式类名  自定义样式
    //         btn: ["立即下载","稍后下载"], //按钮
    //         yes:function(){
    //             layer.close();
    //             window.location.href= url;
    //             },
    //             no:function(){
    //             layer.close();
    //         }
    //     });
        
    // });
    //*********************密码可见********************************/
    $(".see").click(function(){
        var type = $(this).siblings().children("input").attr("type");
        if(type =="text"){
            $(this).removeClass("see1");
            //$(this).siblings().children("input")[0].type = "password";
            for(var i = $(".pas").length; i--;){
                $(".pas").eq(i).children("input")[0].type = "password";
            }
        }else{
            $(this).addClass("see1");
            for(var i = $(".pas").length; i--;){
                $(".pas").eq(i).children("input")[0].type = "text";
            }
        }
    })
    //*********************select 下拉列表********************************/
    $(".select").change(function(){
        $(this).siblings(".inputW").text($(this).children('option:selected').text())
//        $(".inputW").text($(this).children('option:selected').val());
    })
    //*********************input 首次设置资金密码********************************/
    $(".inputPas").on("focus",function(){
        var index =$(this).val().length;
        // //把光标移到input值后面
        // var v =$(this).val();
        // $(this).val("");
        if(index ==6){
            $(this).siblings("ul").children("li").eq(index-1).addClass("guangbiao1");
        }else{
            $(this).siblings("ul").children("li").eq(index).addClass("guangbiao");
        }

    })
    $(".inputPas").on("blur",function(){
        $(this).siblings("ul").children("li").removeClass("guangbiao");
        $(this).siblings("ul").children("li").removeClass("guangbiao1");
    })

    $(".inputPas").on("input change",function(){
        $(this).val($(this).val().replace(/[^\d]/g,''));
        var index =$(this).val().length;
        var val =$(this).val();
        if(index > 6){
            val=val.substring(0,6);
            $(this).val(val);
            // fnSet.alert("密码最多6位数！");
            return false;
        }
        var oLi =$(this).siblings("ul").children("li");
        if(index){
            oLi.eq(index).prevAll().text("*");
            oLi.eq(index-1).nextAll().text("");
            oLi.removeClass("guangbiao").eq(index).addClass("guangbiao");
            if(index ==5){
                oLi.removeClass("guangbiao1");
            }
            if(index ==6){
                oLi.eq(index-1).text("*");
                oLi.removeClass("guangbiao").eq(index-1).addClass("guangbiao1");
            }

        }
        if(!index){
            oLi.eq(0).text("");
            oLi.removeClass("guangbiao").eq(0).addClass("guangbiao");
        }

    })

    //房间开奖结果显示隐藏/
    var fag =true;
    $(".roomHead2,.issueTitle,.lottRstPanel").click(function(){
        if(fag){
            $(this).children("em").css("transform","rotate(180deg)");
            $(".lottery").show();
            $(".lottery dl").addClass("lottery-show").slideDown();
            fag =false;
        }else{
            $(this).children("em").css("transform","rotate(0deg)");
            $(".lottery dl").slideUp(function(){
                $(".lottery").hide();
            });
            fag =true;
        }
    })

//title上的加号
    $(".icoAdd").click(function(){
        $(".menu").toggle();
    })
    $(".menu li").click(function(){
        $(".menu").hide();
    })

    //点击X关闭弹窗
    $(".configClose").click(function() {
        $(".popup").css("display","none");
        $("input[name=secret_pwd]").val('');
    });
//查询报表
    $(function(){
        $("#teamTime").on("click",function(){
            $(".teamSearchTime").show();
        })
        // $("#date1").on("input",function(){
        //     var val = $(this).val();
        //     $("#startTime").val(val.replace(/-/g,"/"));
        // })
        // $("#date2").on("input",function(){
        //     var val = $(this).val();
        //     $("#endTime").val(val.replace(/-/g,"/"));
        // })
        $(".cancelX").on("click",function(){
            $(".teamSearchTime").hide();
        })
        $(".chaX").on("click",function(){
            var starTime =$("#startTime").val();
            var endTime = $("#endTime").val();
            
            if (starTime == '' && endTime == '') {
                $("#teamTime span").html("交易时间：全部");
            }else if(starTime.substring(0,4)-endTime.substring(0,4) ==0){
                $("#teamTime span").html(starTime.substring(5,10)+'-'+endTime.substring(5,10));
            }else{
                $("#teamTime span").html(starTime+'-'+endTime);
            }

        })
        $('.cleanUp').on("click",function(){
            $("#startTime").val('');
            $("#endTime").val('');
            $("#teamTime span").html("交易时间：今天")

        })


        //判断时间轴
        var date1 ,
            date2;
        $("#date1").on("input", function(){
            date1 = $(this).val();
            if(date2){
               if(date1>date2){
                   alert("开始时间不能大于结束时间")
               }else{
                   var val = $(this).val();
                   $("#startTime").val(val.replace(/-/g,"/"));
               }
            }
        })
        $("#date2").on("input", function(){
            date2 = $(this).val();
            if(date1){
                if(date1>date2){
                    alert("结束时间不能少于开始时间")
                }else{
                    var val = $(this).val();
                    $("#endTime").val(val.replace(/-/g,"/"));
                }
            }
        })





        //刷新
        $(".icoRefresh").click(function() {
            window.location.reload();
        });
    })
    
    //首页选项卡
    $(".indexTab ul li").click(function() {
        $(this).addClass("active").siblings().removeClass("active");
        var index = $(this).index()
        $(".tabContent .tabC").eq(index).show().siblings().hide();
    });

    //选择时间
    $("input[type=date]").on("change",function(){
        $(this).siblings(".inputDate").val($(this).val());
    })
    //下拉菜单
    $("select").on("change",function(){
        $(this).siblings(".sel").val($(this).find("option:selected").text());
    })


    $("body").on("focus","input",function(){
        // var viewTop = $(window).scrollTop(),            // 可视区域顶部
        //     viewBottom = viewTop + window.innerHeight;  // 可视区域底部
        // var elementTop = $(this).offset().top, // $element是保存的input
        //     elementBottom = elementTop + $(this).height();
        // $(window).scrollTop(value);


    })




})
