/**
 * Created by HCHT- on 2016/12/2.
 */
var port = $("#wsPort").attr("data-port");
//var port ="7272";
var host = 'www.4ww-pc3333.com';
var url = "wss://"+host+":"+port;
var flagScroll = true;
// var odds_explain ="赔率说明";
//倒计时
var time;
var order_no;
var music = [];
var check = false;
var check2 = false;


fnSet.countDown=function(msg){
    clearInterval(time);
    var theTime2 = parseInt(msg.time);// 秒
    if(msg.stopOrSell == 1)
    {
        if(theTime2 && theTime2 > 0){
            time =setInterval(settime, 1000);
        }else{
            if(userinfo.lottery_type == 4 || userinfo.lottery_type == 9 ||userinfo.lottery_type == 5 || userinfo.lottery_type == 6 || userinfo.lottery_type == 11 || userinfo.lottery_type == 13){
                //幸运飞艇去掉前面4位期号
                $("#issue").text(msg.issue.substr(4));
                $(".issue_video").text(msg.issue.substr(4));
            }else{
                $("#issue").text(msg.issue);
                $(".issue_video").text(msg.issue);
            }
            $("#issue").attr('data-issue',msg.issue);
            var param = {
                "commandid": "3012",
                "uid": userinfo.userid,
            };
            wsSendMsg(param);
            // $(".roomLi p:last").html('<span class="icoTime">开奖中</span>');
        }
    }else if(msg.stopOrSell == 2){
        $(".roomLi p:first").html(msg.stopMsg.split(" ")[0]);
        $(".roomLi p:last").html(msg.stopMsg.split(" ")[1]);
        $(".video_info_left p:first").html(msg.stopMsg.split(" ")[0]);
        $(".video_info_left p:last").html(msg.stopMsg.split(" ")[1]);
    }
    function settime() {
        if(theTime2 <= 0 ) {
            // 将定时器停止
            clearInterval(time);
            var param = {
                "commandid": "3012",
                "uid": userinfo.userid,
            };
            wsSendMsg(param);
            // $(".roomLi p:last").html('<span class="icoTime">开奖中</span>');
        }else {
            var tmpTime = theTime2 - msg.sealingTim;
            var theTime1 = parseInt(tmpTime/60);  //分
            var theTime = parseInt(tmpTime%60);  //秒

            if(!check2){

                if(music && music.length > 7 ){
                    if(music[7].state == "1") {
                        $("#room_play").attr("src", music[7].url);
                        check2 = true;
                    }
                }
            }
            if(tmpTime >0 && check === true ){
                if(music && music.length > 8){
                    if(music[8].state == "1"){
                        $("#room_play").attr("src",music[8].url);
                        check = false;
                    }
                }

            }
            if(theTime2 && theTime2 <=msg.sealingTim){
                $(".roomLi p:last").html('<span class="icoTime">已封盘</span>');
                $(".video_info_left p:last").html('<span class="icoTime2">已封盘</span>');
                if(check === false && tmpTime <= 0){
                    if(music && music.length > 9){
                        if(music[9].state == "1"){
                            $("#room_play").attr("src",music[9].url);
                            check = true;
                        }
                    }
                }
            }else{
                check = false;
                if(theTime1 <= 0){
                    $(".roomLi p:last").html('<span class="icoTime">'+theTime+'秒</span>');
                    $(".video_info_left p:last").html('<span class="icoTime2">'+theTime+'秒</span>');
                }else{
                    $(".roomLi p:last").html('<span class="icoTime">'+theTime1+'分'+theTime+'秒</span>');
                    $(".video_info_left p:last").html('<span class="icoTime2">'+theTime1+'分'+theTime+'秒</span>');
                }

                // $(".icoTime").text(theTime1+"分"+theTime+"秒");
            }
            theTime2--;
        }
    }
}
fnSet.alert =function(text,sibtne){
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
// fnSet.config =function(text,confirm,callOff){
//     var alert ='<div class="popupAlert"><div class="config"><p>'+text+'</p><div class="button" style="margin-top: 30px;"><button class="confirm">确认</button><button class="callOff">取消</button></div></div></div>'
//     $("body").append(alert);
//
//     $(".confirm").on("click",function(){
//         $(this).parents(".popupAlert").remove();
//     })
// }
//玩法赔率
fnSet.oddsUpdate = function(msg){
    var chase_number_dome = $(".sel");
    var panel_1 =msg.panel_1;
    var play1 ="";
    var panel_2 =msg.panel_2;
    var play2 ="";
    var panel_3 =msg.panel_3;
    var play3="";

    var indexT =chase_number_dome.children("option:selected").index();
    // selected
    if(indexT == -1){
        chase_number_dome.empty();
        chase_number_dome.append("<option value=''>请选择</option>");
        if(panel_1.length>0){
            for(var i=0; i<panel_1.length; i++){
                chase_number_dome.append("<option value='"+panel_1[i].title+"'>"+panel_1[i].title+"</option>")
            }
        }
        if(panel_2.length>0){
            for(var i=0; i<panel_2.length; i++){
                chase_number_dome.append("<option value='"+panel_2[i].title+"'>"+panel_2[i].title+"</option>")
            }
        }
        if(panel_3.length>0){
            for(var i=0; i<panel_3.length; i++){
                chase_number_dome.append("<option value='"+panel_3[i].title+"'>"+panel_3[i].title+"</option>")
            }
        }
    }
    // else{
    //     if(indexT!=0 && chase_number_dome.children("option:selected").val() != panel_1[indexT-1].title){
    //         chase_number_dome.empty();
    //         chase_number_dome.append("<option value=''>请选择</option>");
    //         if(panel_1.length>0){
    //             for(var i=0; i<panel_1.length; i++){
    //                 chase_number_dome.append("<option value='"+panel_1[i].title+"'>"+panel_1[i].title+"</option>")
    //             }
    //         }
    //         if(panel_2.length>0){
    //             for(var i=0; i<panel_2.length; i++){
    //                 chase_number_dome.append("<option value='"+panel_2[i].title+"'>"+panel_2[i].title+"</option>")
    //             }
    //         }
    //         if(panel_3.length>0){
    //             for(var i=0; i<panel_3.length; i++){
    //                 chase_number_dome.append("<option value='"+panel_3[i].title+"'>"+panel_3[i].title+"</option>")
    //             }
    //         }
    //     }
    // }

        var play1 ='<p class="desc">'+ panel_1[0].desc +'</p><ul>';

    for(var i=0; i<panel_1.length; i++){
        play1+='<li><div class="key-dxds" data-desc="'+ panel_1[i].desc +'"><span>'+panel_1[i].title+'</span><p>1:'+panel_1[i].value+'</p></div></li>';
    }
    $(".play1").children("ul").html(play1);
    //$(".play1 ul li").eq(0).children().addClass("pit"); //默认选中第一个玩法

    var play2 ='<p class="desc">'+ panel_2[0].desc +'</p><ul>';
    for(var i=0; i<panel_2.length; i++){
        play2+='<li><div class="key-guessNum color color'+panel_2[i].title+'" data-color="'+ panel_2[i].color +'" data-desc="'+ panel_2[i].desc +'"><span>'+panel_2[i].title+'</span><p>'+panel_2[i].value+'</p></div></li>';
        // chase_number_dome.append("<option value='"+panel_2[i].title+"'>"+panel_2[i].title+"</option>")
    }
    $(".play2").children("ul").html(play2);

    if (panel_3.length>0) {
        var colorArr = new Array("red1","lv","lan","huang");
            var play3 ='<p class="desc">'+ panel_3[0].desc +'</p><ul>';

        for(var i=0; i<panel_3.length; i++){
            if (panel_3[i].title == "红" || panel_3[i].title == "绿" || panel_3[i].title == "蓝"){
                play3 += '<li><div class="key-special"  data-desc="'+ panel_3[i].desc +'"><span style="background:'+panel_3[i].color+'">'+ panel_3[i].title +'</span><p>1:' + panel_3[i].value+ '</p></div></li>';
            }else{
                play3 += '<li><div class="key-special"  data-desc="'+ panel_3[i].desc +'"><span data-color="'+panel_3[i].color+'">'+ panel_3[i].title +'</span><p>1:' + panel_3[i].value+ '</p></div></li>';
            }
            // play3 += '<li><div  data-desc="'+ panel_3[i].desc +'"><span>'+ panel_3[i].title +'</span><p class="'+ colorArr[i] +'">1:' + panel_3[i].value+ '</p></div></li>';
            // chase_number_dome.append("<option value='"+panel_3[i].title+"'>"+panel_3[i].title+"</option>")
        }

        play3 += '</ul>'
        $(".play3").html(play3);
    }else{
        $(".play3").parent().remove();
    }

    $(".jianpanTitle h4").val()
}
//后台返回3004 前端推送数据
fnSet.update34 =function(msg){
    var roomGroup;
    var float="left";
    var honor ="";
    var icon ="";
    msg.content =msg.content.replace(/\\r|\\n/g, "\n");

    var check_code_limit ="\u3010\u8ffd\u53f7\u3011\u6295\u6ce8\u6210\u529f";
    var limit_data_list = msg.content.split(" ");
    if(limit_data_list[3]===check_code_limit) {
        var limit_exist = false;
        $(".issue_"+limit_data_list[0]).each(function () {
            if($(this).attr("name") == limit_data_list[1]) {
                limit_exist = true;
                var limit_money = parseFloat(limit_data_list[2])+parseFloat($(this).val());
                $(this).val(limit_money.toFixed(2));
            }
        });
        if(!limit_exist) $("body").eq(0).prepend("<input type='hidden' name='"+limit_data_list[1]+"' class='issue_"+limit_data_list[0]+"' value='" +limit_data_list[2]+ "'>");
    }

    if(Number(msg.honor_status) == 1){
        honor ='<i class="level-'+Number(msg.sort)+'"></i>';
    }
    if(msg.icon){
        icon = "<span><img src='"+msg.icon+"' /> </span>";
    }

    if(msg.nickname ==""){
        //系统消息
        if(msg.content.indexOf("欢迎")!= -1){
            if(msg.username == userinfo.nickname){
                //刚进房间时设置投注区荣誉等级的图片
                $(".stake-icon").eq(0).css("background","url(/statics/web/images/honor/mbadge0"+Number(msg.sort)+"_default@2x.png) no-repeat #4d4c4b center");
                $(".stake-icon").eq(1).css("background","url(/statics/web/images/honor/mbadge0"+Number(msg.sort)+"@2x.png) no-repeat #4d4c4b center");
            }
            if(Number(msg.honor_status) == 1){
                msg.content = msg.content.replace(/{#username}/g, '<i class="level-'+Number(msg.sort)+'"></i>' + msg.username);
            }else{
                msg.content = msg.content.replace(/{#username}/g, msg.username);
            }

            roomGroup ='<div class="userBetting2"><ul class="welcome"><li style="font-size: 12px;"><pre>'+msg.content+'</pre></li></ul></div>'
        }else{
            roomGroup ='<div class="userBetting2"><ul class="system"><li style="font-size: 12px;"><div class="content_l"><pre>'+msg.content+'</pre></div></li></ul></div>'; //系统提示
        }

    }else{
        if(msg.uid == userinfo.userid){
            float="right";
            roomGroup ='<div class="userBetting"><ul class="'+float+'"><li data-href="'+ tz_record +'"><img src="'+msg.avatar+'"></li><li style="font-size: 14px;"><h3 style="text-align: '+float+'"><b>'+userinfo.nickname+'</b>'+honor+icon+'</h3><div style="text-align: right;"><div class="content_r"><pre>'+msg.content+'</pre></div></div>'
        }else{
            roomGroup ='<div class="userBetting"><ul class="'+float+'"><li data-href="'+ tz_record +'"><img src="'+msg.avatar+'"></li><li style="font-size: 14px;"><h3 style="text-align: '+float+'"><b>'+msg.nickname+'</b>'+honor+icon+'</h3><div style="text-align: left;"><div class="content_l"><pre>'+msg.content+'</pre></div></div>';
        }

        var oTime = timeHandle();
        roomGroup +='<span class="timeRecord">'+oTime+'</span></li></ul></div>';
    }

    if(msg.status){
        fag1 =true;
    }
    $(".room .roomContent").append(roomGroup);
    fnSet.scrollTop();
}

//后台返回3005 前端推送数据
fnSet.update35 =function(msg){
    fnSet.alert(msg.content);
}
//后台返回3007 前端推送数据
fnSet.update37 =function(msg){
    var roomGroup;
    var float ="left";
    var tzRecord = "";
    var honor ="";
    var icon="";
    var issue ="";
    if(userinfo.lottery_type ==4 ||userinfo.lottery_type == 9 || userinfo.lottery_type == 5 || userinfo.lottery_type == 6 || userinfo.lottery_type == 11){
        if(userinfo.lottery_type == 11){
            var num = 5
        }else{
            var num = 4
        }
        issue = msg.issue.substr(num);
    }else{
        issue = msg.issue
    }
    if(Number(msg.honor_status) == 1){
        honor ='<i class="level-'+Number(msg.sort)+'"></i>';
    }
    if(msg.icon){
        icon = "<span><img src='"+msg.icon+"' /> </span>";
    }
    if(msg.uid == userinfo.userid){
        $(".keyboard").click();
        float ="right";
        //$(".room").css("padding-top","110px")
        $(".betTixing span em").html('本期已下'+msg.count+'注');

        $(".betTixing").show();

        $(".textArea >div").html("");
        $(".textArea").html('<div contenteditable="true" data-content="true"></div>');
        for(var i=0; i<msg.way.length; i++){
            var limit_exist = false;
            if($(".issue_"+issue).html()!=undefined) {
                $(".issue_"+issue).each(function () {
                    if($(this).attr("name") == msg.way[i]) {
                        limit_exist = true;
                        var limit_money = parseFloat(msg.money[i])+parseFloat($(this).val());
                        $(this).val(limit_money.toFixed(2));
                    }
                });
            }
            if(!limit_exist) $("body").eq(0).prepend("<input type='hidden' name='"+msg.way[i]+"' class='issue_"+issue+"' value='"+msg.money[i]+"'>");
            offSet ++;//配备后台的数据 把
        }
        fag1 =true;
        aWanf=[];
        aJine=[];
        roomGroup ='<div class="userBetting" ><dl class="'+float+'" data-issue="'+msg.issue+'"><dt data-href="'+ tz_record +'&fromRoom=1"><img src="'+msg.avatar+'"></dt><dd><h3 style="text-align: '+float+'"><b>'+userinfo.nickname+'</b>'+honor+icon+'</h3><div class="bet-con-self"><div class="issueCon"><em></em><span>'+issue+'期</span></div>';
    } else {  //除了游客和注册用户的其他
        roomGroup ='<div class="userBetting" ><dl class="'+float+'" data-issue="'+msg.issue+'"><dt><img src="'+msg.avatar+'"></dt><dd><h3 style="text-align: '+float+'"><b>'+msg.nickname+'</b>'+honor+icon+'</h3><div class="bet-con"><div class="issueCon"><em></em><span>'+issue+'期</span></div> ';
    }

    for(var i=0; i<msg.way.length; i++){

        tzRecord += '<li><em class="close" order-no="' + msg.order_no[i] + '"></em><label>' + msg.way[i] + '</label> <span>' + msg.money[i] + '<i class="icoAcer"></i></span></li>'; //添加投注记录

        roomGroup += '<p order-no="' + msg.order_no[i] + '"><label>投注类型：</label><span>' + msg.way[i] + '</span><label class="r">金额：<em>' + msg.money[i] + '</em><i class="iconMoney"></i></label></p>'; //聊天室投注消息

    }

    var oTime = timeHandle();
    roomGroup +='<i class="mask"></i></div><span class="timeRecord">'+oTime+'</span></dd></dl></div>';
    $(".room .roomContent").append(roomGroup);

    if(msg.uid == userinfo.userid) {
        $(".quxiao ul").prepend(tzRecord);
    }

    fnSet.scrollTop();
}

fnSet.update318 =function(msg){
    var roomGroup;
    var float ="left";
    var tzRecord = "";
    var honor ="";
    var icon="";
    if(msg.data.length){
        $(".betTixing span em").html('本期已下'+msg.totalZu+'注');
        totalZu =msg.totalZu;
    }
    for(var t=0; t<msg.data.length; t++){
        if(msg.data[t].nickname ==""){
            msg.data[t].nickname =msg.data[t].username;
        }
        if(msg.data[t].avatar==""){
            msg.data[t].avatar ="/up_files/room/avatar.png";
        }
        if(Number(msg.data[t].honor_status)){
            honor ='<i class="level-'+Number(msg.data[t].sort)+'"></i>';
        }

        if(msg.data[t].icon){
            icon = "<span><img src='"+msg.data[t].icon+"' /> </span>";
        }
        if(userinfo.lottery_type == 5 ||userinfo.lottery_type == 9 || userinfo.lottery_type == 4 || userinfo.lottery_type == 6 || userinfo.lottery_type == 11){
            if(userinfo.lottery_type == 11){
                var num = 5
            }else{
                var num = 4
            }
            var dataIssue = msg.data[t].issue.substr(num);
        }else{
            var dataIssue = msg.data[t].issue;
        }
        if(msg.data[t].uid == userinfo.userid){
            offSet ++;
            $(".keyboard").click();
            float ="right";
            //$(".room").css("padding-top","110px")
            //$(".betTixing span em").html('本期已下'+msg.count+'注');
            $(".betTixing").show();
            $(".textArea >div").html("");
            // fag1 =true;
            aWanf=[];
            aJine=[];

            roomGroup ='<div class="userBetting" ><dl class="'+float+'" data-issue="'+msg.data[t].issue+'"><dt data-href="'+ tz_record +'"><img src="'+msg.data[t].avatar+'"></dt><dd><h3  style="text-align: '+float+'"><b>'+userinfo.nickname+'</b>'+honor+icon+'</h3><div class="bet-con-self"><div class="issueCon" "><em></em><span>'+dataIssue+'期</span></div>';
        } else {
            float ="left";
            roomGroup ='<div class="userBetting" ><dl class="'+float+'" data-issue="'+msg.data[t].issue+'"><dt><img src="'+msg.data[t].avatar+'"></dt><dd><h3  style="text-align: '+float+'"><b>'+msg.data[t].nickname+'</b>'+honor+icon+'</h3><div class="bet-con"><div class="issueCon" "><em></em><span>'+dataIssue+'期</span></div>';
        }

        tzRecord = '<li><em class="close" order-no="'+ msg.data[t].order_no +'"></em><label>'+ msg.data[t].way +'</label> <span>'+ msg.data[t].money +'<i class="icoAcer"></i></span></li>';  //添加投注记录

        roomGroup+='<p order-no="'+ msg.data[t].order_no +'"><label>投注类型：</label> <span>'+msg.data[t].way+'</span><label class="r">金额：<em>'+msg.data[t].money+'</em><i class="iconMoney"></i></label></p>';  //聊天室投注消息

        var oTime = timeHandle();
        roomGroup +='<i class="mask"></i></div><span class="timeRecord">'+oTime+'</span></dd></dl></div>';
        $(".room .roomContent").append(roomGroup);

        if(msg.data[t].uid == userinfo.userid){
            $(".quxiao ul").append(tzRecord);
        }

    }
    if($('#zhuiHao').css("display") == "block"){
        $("#chaseNumber").click();
    }
    fnSet.scrollTop();
    flagScroll = true;
}

fnSet.update319 =function(msg){
    var roomGroup;
    var float ="right";
    var tzRecord = "";
    var honor ="";
    var msg = msg.data;
    if(userinfo.lottery_type == 4 ||userinfo.lottery_type == 9 || userinfo.lottery_type == 5 || userinfo.lottery_type == 6 || userinfo.lottery_type == 11){
        if(userinfo.lottery_type == 11){
            var num = 5
        }else{
            var num = 4
        }
        msg.begin_issue = msg.begin_issue.substr(num);
        msg.end_issue = msg.end_issue.substr(num);
    }
    if(Number(msg.honor_status) == 1){
        honor ='<i class="level-'+Number(msg.sort)+'"></i>';
    }

    $(".keyboard").click();
    //$(".room").css("padding-top","110px")
    $(".textArea >div").html("");
    $(".textArea").html('<div contenteditable="true" data-content="true"></div>');

    fag1 =true;
    aWanf=[];
    aJine=[];
    roomGroup ='<div class="userBetting" ><dl class="'+float+'" data-issue="'+msg.begin_issue+'"><dt data-href="'+ tz_record +'"><img src="'+ userinfo.head_url+'"></dt><dd><h3 style="text-align: '+float+'">'+honor+'<b>'+userinfo.nickname+'</b></h3><div class="chase-con"><div class="issueCon"><em></em>追号: <span>'+msg.begin_issue+'-'+msg.end_issue+'期</span></div><p class="chase-tit"><span>投注类型</span><span>金额</span><span>翻倍</span></p>';

    for(var i=0; i<msg.bet_array.length; i++){
        roomGroup += '<p><span>'+msg.bet_array[i].way+'</span><span>'+msg.bet_array[i].money+'</span><span>'+msg.bet_array[i].multiple+' 倍</span></p>'; //聊天室投注消息
    }
    roomGroup += '<div class="chase-static"><span>共 <em>'+msg.count+'</em> 注</span><span>总计: <em>'+msg.total_amount+'</em>元宝</span></div><i class="mask"></i>';

    var oTime = timeHandle();
    roomGroup +='</div><span class="timeRecord">'+oTime+'</span></dd></dl></div>';
    $(".room .roomContent").append(roomGroup);

    fnSet.scrollTop();
}

//调整滚动条
fnSet.scrollTop =function(){
    setTimeout(function() {
        // 将窗口滚动条调整到底部
        var keyHeight =$(".bottomWarp").outerHeight();
        var roomNewsHeight =$('.roomNews').outerHeight();
        // if($(".bettingKey").css("display") =="none"){
        //     keyHeight =0;
        // }
        // var roomHeadHeight =$(".roomHead").outerHeight();//获取头部的高度
        if($(".roomHead").css("display") =="none"){
            $('.betTixing').css("top","44px");
            $('.room').css("top","44px");
        }else{
            if($(".betTixing").css("display") =="none"){
                $('.room').css("top","151px");
                $('.betTixing').css("top","151px");
            }else{
                $('.room').css("top","191px");
                $('.betTixing').css("top","151px");
            }
        }
        $(".customNews").css("bottom",keyHeight);
        //$('.room').css("bottom",keyHeight+roomNewsHeight);
        var pageHeight =$(".roomContent").outerHeight();
        $('.room').animate({scrollTop:pageHeight},500);
    }, 100);
}
fnSet.button = function(aWanf,aJine,aWanfT){
    //if(jine == -1){
    //    jine ="梭哈";
    //}
    //var issue = $("#issue").html();
    //todo
    if(userinfo.lottery_type ==2||userinfo.lottery_type ==9 || userinfo.lottery_type ==4 || userinfo.lottery_type==5 || userinfo.lottery_type == 6 || userinfo.lottery_type == 11){ //北京幸运飞艇和幸运飞艇
        if (aWanf.length) {
            var html = '';
            for (var i = 0; i < aWanf.length; i++) {
                html += '<p><em class="issue">' + aWanfT[i] + '</em><label>' + aWanf[i] + '</label> <span>' + aJine[i] + '</span><em class="close"></em></p>'
            }
            $(".betList").children(".list-con").html(html);
        }
    }else{
        if (aWanf.length) {
            var html = '';
            for (var i = 0; i < aWanf.length; i++) {
                html += '<p><em class="close"></em><em class="issue">' + aWanfT[i] + '</em><label>' + aWanf[i] + '</label> <span>' + aJine[i] + '</span></p>'
            }
            $(".betList").children(".list-con").html(html);
        }
    }
    //$(".cenMoneyWarp").hide();
    //$("#inputNumber").val("");
    if( $(".betList .list-con p").length >3){
        var pheight = $(".betList .list-con p").height();
        $(".betList").scrollTop(($(".betList .list-con p").length -3)*pheight)
    }
}




$(function(){
    getOdds();
    //房间提示音的控制
    $(".icoSound").click(function () {
        $(this).toggleClass("icoNosound");
        //$("#room_play").attr("src", "");
        if($("#room_play").paused){
            $("#room_play").play();
        }else{
            $("#room_play").pause();
        }
    })

    $(".quxiao ul").on("scroll",function(){
        var liHeight = $(this).children().outerHeight() * offSet;
        if(offSet < totalZu){
            if((liHeight -290) <= $(this).scrollTop() ){
                if(flagScroll){
                    var param = {
                        "commandid": "3017",
                        "uid": userinfo.userid,
                        "roomid":userinfo.room_id,
                        "lottery_type":userinfo.lottery_type,
                        "offSet":offSet
                    };
                    wsSendMsg(param);
                    flagScroll = false;
                }

            }
        }
    })
    fnSet.scrollTop();
    //var height ="";
    var fag =true;
    // var genWanf =new Array();
    // var genJine =new Array();
    var bet_min_money = 0;
    //投注金额
    $.ajax({
        url: "/index.php?m=web&c=openAward&a=getBetting",
        //data:{},
        type: "POST",
        dataType:'json',
        success:function(msg){
            if(msg.status == "0"){
                var list="";
                var chipNum="";
                for(var i=0; i<msg.list.length; i++){
                    if(Number(msg.list[i]) >= 1000){
                        chipNum = Number(msg.list[i])/1000 + 'K'
                    }else{
                        chipNum = msg.list[i];
                    }

                    list +='<span>'+ chipNum +'</span>';
                }
                $(".chip").prepend(list);
                music = msg.music;
                bet_min_money = msg.list[0];
            }
        },
        error:function(er){
        }
    });

    //弹出隐藏投注键盘
    $("body").on("click","#bettingBtu", function(event){
        //var wrapper = document.getElementById("#wrapper");
        //$("wrapper").addEventListener('click', function (event) {
        //    event.preventDefault();
        //},false);

        $("wrapper").on("touchmove",function(event){
            event.preventDefault();
        })

        //幸运飞艇最小投注金额
        if($("#lower").val() -  $("#user_group_lower").val() >= 0){
            $(".stake-limit span").text($("#lower").val());
        }else{
            $(".stake-limit span").text($("#user_group_lower").val());
        }


        //$("#speak").hide();
        //$(this).hide();
        //$(".textArea div").attr("contenteditable","false");
        //$(".keyboard").show();
        //$(".roomHead").hide();
        $('.roomNews').hide();
        $(".bettingKey .operateWarp").show();
        $(".bettingKey-pkft .operateWarp").hide();
        $(".zhuiHao").hide();

        $(".stakeWarp").show();
        // $(".hasStake").hide();
        // $(".noStake").show();

        // var keyHeight =$(".bettingKey").outerHeight();
        // $('.customNews').css("bottom",keyHeight);
        $(".bettingWarp").show();
        $(".betCon").css("padding-bottom","50px");
        $(".bettingKey").slideDown(function(){
            $("#zhuiHList").empty();
            if($(".lottery").css("display")=="block"){
                $(".lottery").css("display","none");
            }
            if($(".subtotal").length>0){
                $(".subtotal").remove();
            }
            fnSet.scrollTop();
        });


        //$(".bettingKey-pkft").slideDown(function(){
        //    $(".selecArea").addClass("betting").removeClass("chasing");
        //    $("#zhuiHList").empty();
        //    if($(".lottery").css("display")=="block"){
        //        $(".lottery").css("display","none");
        //    }
        //    if($(".subtotal").length>0){
        //        $(".subtotal").remove();
        //}
        //    fnSet.scrollTop();
        //});

        if($(".betWarp")){
            $(".betWarp").show().addClass("betting").removeClass("chasing");
            setTimeout(function () {
                $(".betCon").css("top","150px");
            },20)
            $(".betLeftNav ul li").eq(0).click();
        }

    })

    //    $(".bettingWarp").hide();
    //    $(".keyboard").show();
    //隐藏投注键盘
    $(".bettingWarp").click(function(e){
        e = e || window.event;
        var drag = $(".bettingWarp"),
            dragel = $(".bettingWarp")[0],
            target = e.target;
        if (dragel === target && !$.contains(dragel, target)) {

            $(".bettingKey").slideUp(function(){
                drag.hide();
                //$("#speak").show();
                //$("#bettingBtu").show();
                //$(".textArea").html('<div contenteditable="true" data-content="true"></div>')
                //$(this).hide();
                //$(".roomHead").show();
                //$("#bettingBtu1").hide();
                $('.customNews').css("bottom","0");
                $('.roomNews').show();
                //$(".bettingWarp").hide();
                //$(".bettingKey").hide();
                $("#zhuiHList").empty();
                $(".zhuiHaoList").hide();
                // var height =$(".roomNews").outerHeight();
                //$(".room").css("bottom",height);

                //$("#bettingBtu").text("赔率");
                //roomBtu(height);
                aWanf=[];
                aJine=[];
                aWanfT=[];
                fnSet.scrollTop();
                //fag= true;
            });
            $(".bettingKey-pkft").slideUp(function(){
                drag.hide();
                $('.customNews').css("bottom","0");
                $('.roomNews').show();
                //$(".bettingWarp").hide();
                //$(".bettingKey").hide();
                $("#zhuiHList").empty();
                $(".zhuiHaoList").hide();

                aWanf=[];
                aJine=[];
                fnSet.scrollTop();
                //fag= true;
            });

        }
    });


    //幸运28和螞蟻收益选号操作
    var valText;//给冠亚拼接用的
    function xh (el){

        //清除所有选号
        if(el.hasClass("pit")){
            el.removeClass("pit");
            if(! $(".pit").length){
                $(".desc").html("");
            }
        }else{
            if($("#betTitle").text() != "猜车号" && $("#betTitle").text() != "猜冠亚" && $("#betTitle").text() != "猜数字"){
                $(".betRightCon .betPlay").each(function(){
                    $(this).removeClass("pit");
                })
                $(".desc").html(el.attr("data-oddsexp"));
            }else{
                $(".desc").html("");
            }
            el.addClass("pit");

        }

        if($("#betTitle").text() == "猜冠亚" && $('.pit').length > 2){
            el.removeClass("pit");
            return;
        }
        if($("#betTitle").text() == "猜冠亚"  && $('.pit').length < 2){
            $('.roomNews').show();
            $(".operate").hide();
            $(".chaseWarp").hide();
            $(".stakeWarp").show();
            // $(".betList").hide();
            $("#inputNumber").blur();
            valText =  $(el).attr("data-title");
            return
        }

        //投注选号操作
        if($(".betWarp").hasClass("betting")){
            $('.roomNews').hide();
            $(".operate").show();
            $(".chaseWarp").hide();
            $(".stakeWarp").hide();
            // $(".betList").hide();
            $("#inputNumber").focus();
        }
        // 追号选号操作
        if($(".betWarp").hasClass("chasing")){
           $('.roomNews').hide();
           // $(".operateWarp").hide();
           $(".stakeWarp").hide();
           // $(".betList").hide();
           //$("#inputNumber").focus();
            if($("#betTitle").text() == "猜冠亚"  && $('.pit').length == 2){
                valText = valText +'_'+ $(el).attr("data-title").split("_")[1];
                $(".chase-wanf").html(valText);
            }else{
                $(".chase-wanf").html($(el).attr("data-title"));
            }

           $(".zhuiHaoList").hide();
           $(".chaseWarp").show();
           $(".zhuiHao").slideDown();
        }
        event.stopPropagation();
    }

    $(".bettingKey .play").on("click","ul li", function(){
        if(aJine !=""){
            var jineLength =aJine.length;
            if(aJine[jineLength-1] =="-1"){
                fnSet.alert("您已经梭哈");
                return false;
            }
        }

        reset();
        $(".play .desc").text($(this).children("div").attr("data-desc"));
        $(this).children("div").addClass("pit");
        //$("#touzhuzhi").html($(this).find("span").text());
        //var height =$(".cenMoney").outerHeight();
        //$(".cenMoney").css("margin-top",-(height/2))
        event.stopPropagation();
    })
    //北京幸运飞艇和幸运飞艇选号操作
    //$(".bettingKey-pkft .play").on("click","ul li", function(){
    //    //todo
    //    $(".play .desc").text($(this).children("div").attr("data-desc"));
    //    if($(".selecArea").hasClass("betting") && $(this).parents(".play").hasClass("play2")){
    //        if($(this).children("div").hasClass("pit")){
    //            $(this).children("div").removeClass("pit");
    //        }else{
    //    $(this).children("div").addClass("pit");
    //        }
    //    }else{
    //        reset();
    //        $(this).children("div").addClass("pit");
    //    }
    //    //投注选号操作
    //    if($(".selecArea").hasClass("betting")){
    //        $('.roomNews').hide();
    //        $(".operateWarp").show();
    //        $(".zhuiHao").hide();
    //        $(".stakeWarp").hide();
    //        $(".betList").hide();
    //        $("#inputNumber").focus();
    //    }
    //    //追号选号操作
    //    if($(".selecArea").hasClass("chasing")){
    //        $('.roomNews').hide();
    //        $(".operateWarp").hide();
    //        $(".stakeWarp").hide();
    //        $(".betList").hide();
    //        //$("#inputNumber").focus();
    //        $(".chase-wanf").html($(this).find("span").text());
    //        $(".chaseWarp").show();
    //        $(".zhuiHao").slideDown();
    //    }
    //    //var height =$(".cenMoney").outerHeight();
    //    //$(".cenMoney").css("margin-top",-(height/2))
    //    event.stopPropagation();
    //})

    //$(".betWarp").on("click",".betRightCon dl dd .betPlay", function(){}



    //重置玩法赔率的数据
    if(userinfo.lottery_type == 5 || userinfo.lottery_type == 6 || userinfo.lottery_type == 11){
        var wanfOdds=[
            {
                name:"猜双面",
                set:['第一球','第二球','第三球','第四球','第五球'],
                panel:[]
            },
            {
                name:"猜数字",
                set:['第一球','第二球','第三球','第四球','第五球'],
                panel:[]
            },
            {
                name:"猜总和",
                panel:[]
            },
            {
                name:"猜龙虎",
                panel:[]
            }

        ]
    }else{
        var wanfOdds=[
            {
                name:"猜双面",
                set:['冠军','亚军','第三名','第四名','第五名','第六名','第七名','第八名','第九名','第十名'],
                panel:[]
            },
            {
                name:"猜车号",
                set:['冠军','亚军','第三名','第四名','第五名','第六名','第七名','第八名','第九名','第十名'],
                panel:[]
            },
            {
                name:"猜龙虎",
                set:['冠军','亚军','第三名','第四名','第五名'],
                panel:[]
            },
            {
                name:"猜庄闲",
                panel:[]
            },
            {
                name:"猜冠亚",
                panel:[]
            },
            {
                name:"冠亚和",
                panel:[]
            }
        ];
    }
    fnSet.oddsUpdateSSC = function(msg){
        for(var i =0 ; i<wanfOdds.length; i++){
            if(wanfOdds[i].name =="猜双面"){
                wanfOdds[i].panel =msg.panel_1;
            }
            if(wanfOdds[i].name =="猜数字"){
                wanfOdds[i].panel =msg.panel_2;
            }
            if(wanfOdds[i].name =="猜总和"){

                if(msg.panel_3.length){
                    wanfOdds[i].panel =msg.panel_3;
                }else{
                    $(".betLeftNav ul li").eq(2).hide();
                }

            }
            if(wanfOdds[i].name =="猜龙虎"){
                if(msg.panel_4.length){
                    wanfOdds[i].panel =msg.panel_4;
                }else{
                    $(".betLeftNav ul li").eq(3).hide();
                }
            }
        }
    };
    fnSet.oddsUpdate幸运飞艇 = function (msg) {
        for(var i =0 ; i<wanfOdds.length; i++){
            if(wanfOdds[i].name =="猜双面"){
                wanfOdds[i].panel =msg.panel_1;
            }
            if(wanfOdds[i].name =="猜车号"){
                wanfOdds[i].panel =msg.panel_2;
            }
            // if(wanfOdds[i].name =="猜龙虎"){
            //     wanfOdds[i].panel =msg.data.panel_6;
            // }
        }
    }
    fnSet.oddsUpdate幸运飞艇_2 = function (msg) {
        for(var i =0 ; i<wanfOdds.length; i++){
            if(wanfOdds[i].name =="冠亚和"){
                if((msg.panel_3["冠亚"] && msg.panel_3["冠亚"].length) || (msg.panel_3["区段"] && msg.panel_3["区段"].length) || (msg.panel_3["和"] && msg.panel_3["和"].length) ){
                    wanfOdds[i].panel =msg.panel_3;
                }else{
                    $(".betLeftNav ul li").eq(5).hide();
                }
                wanfOdds[i].panel =msg.panel_3;
            }
            if(wanfOdds[i].name =="猜庄闲"){
                if(msg.panel_4['庄闲'] && msg.panel_4['庄闲'].length){
                    wanfOdds[i].panel =msg.panel_4;
                }else{
                    $(".betLeftNav ul li").eq(3).hide();
                }
            }
        }
    }
    fnSet.oddsUpdate幸运飞艇_3 = function (msg) {
        for(var i =0 ; i<wanfOdds.length; i++){

            if(wanfOdds[i].name =="猜冠亚"){
                if(msg.panel_5.length){
                    wanfOdds[i].panel =msg.panel_5;
                }else{
                    $(".betLeftNav ul li").eq(4).hide();
                }
            }
            if(wanfOdds[i].name =="猜龙虎"){
                wanfOdds[i].panel =msg.panel_6;
            }
        }
    }

    var title = "";
    $(".betWarp").on("click",function () {
        $(".betCon").css("top","100%");
        setTimeout(function(){
            $(".betWarp").hide().removeClass("betting");
            $(".roomNews").show();
        },480);
    })
    $(".betCon").on("click",function (event) {
        event.stopPropagation();    //  阻止事件冒泡
    })
    $(".betLeftNav ul").on("click","li",function () {
        if($(".betWarp ").hasClass("chasing")){
            $(".stakeWarp").hide();
        }else{
            $(".stakeWarp").show();
        }
        $(".operate").hide();
        title = $(this).attr("data-title");
        $(this).addClass("active").siblings().removeClass("active");
        if((title == '猜车号' && $(".betWarp").hasClass("betting")) || (title == '猜数字' && $(".betWarp").hasClass("betting"))){
            $("#betTitle").html(title+'<i class="multiple"></i>');
        }else{
            $("#betTitle").html(title);
        }

        $(".data-con").html("");
        var set ="";
        var betCon='<div class="betRightCon"><p class="desc"></p>';
        $("#betTitle").show();
        for(var i =0; i<wanfOdds.length; i++){
            if(title == wanfOdds[i].name){
                betCon+='<dl>';
                if(wanfOdds[i].set){
                    set +='<div class="betRightSel">'+
                        '<span>'+wanfOdds[i].set[0]+'</span>'+
                        '<select>';
                    for(var k=0; k<wanfOdds[i].set.length; k++){
                        set +='<option>'+wanfOdds[i].set[k]+'</option>';
                    }
                    set +=  '</select></div>';
                    var panel =wanfOdds[i].panel[wanfOdds[i].set[0]];
                    for(var l =0; l< panel.length; l++){
                        if(wanfOdds[i].name == "猜龙虎"){
                            betCon+='<dd><div class="betPlay" data-title="'+panel[l].title+'" data-oddsexp="'+panel[l].desc+'"><em>'+panel[l].title.substr(panel[l].title.indexOf("_")+1)+'</em><span>'+panel[l].value+'</span></div></dd>'

                        }else {
                            betCon+='<dd><div class="betPlay" data-title="'+panel[l].title+'" data-oddsexp="'+wanfOdds[i].panel['说明'][panel[l].title.substr(panel[l].title.indexOf("_")+1)]+'"><em>'+panel[l].title.substr(panel[l].title.indexOf("_")+1)+'</em><span>'+panel[l].value+'</span></div></dd>'

                        }
                    }
                    betCon+='</dl><div class="oddsExplain" data-index="\\\'+i+\\\'"><i class="ico_peilv"></i>赔率说明</div></div>';
                    $(".data-con").append(set+betCon);

                    //选号操作
                    //$(".data-con .betRightCon dl dd").on( 'click','.betPlay',function() {
                    //    xh($(this));
                    //});
                }else{
                    if(title =="猜庄闲"){
                        var panel =  wanfOdds[i].panel["庄闲"];
                        betCon+='<dl>';
                        for(var l =0; l< panel.length; l++){
                            betCon+='<dd><div class="betPlay" data-title="'+panel[l].title+'" data-oddsexp="'+panel[l].desc+'"><em>'+panel[l].title.substr(panel[l].title.indexOf("_")+1)+'</em><span>'+panel[l].value+'</span></div></dd>'
                        }
                        betCon+='</dl><div class="oddsExplain" data-index="\\\'+i+\\\'"><i class="ico_peilv"></i>赔率说明</div></div>';
                        $(".data-con").append(betCon);

                        //选号操作
                        //$(".data-con .betRightCon dl dd").on( 'click','.betPlay',function() {
                        //    xh($(this));
                        //});

                    }
                    if(title =="冠亚和"){
                        var panel =  wanfOdds[i].panel["冠亚"];
                        if(panel){
                            betCon+='<dl><dt>冠亚和</dt></dl><dl>';
                            for(var l =0; l< panel.length; l++){
                                betCon+='<dd><div class="betPlay" data-title="'+panel[l].title+'" data-oddsexp="'+panel[l].desc+'"><em>'+panel[l].title.substr(panel[l].title.indexOf("_")+1)+'</em><span>'+panel[l].value+'</span></div></dd>'
                            }
                        }


                        var panel2 =  wanfOdds[i].panel["和"];
                        if(panel2){
                            betCon+='</dl><dl><dt>猜数字</dt></dl><dl>';
                            for(var l =0; l< panel2.length; l++){
                                betCon+='<dd><div class="betPlay" data-title="'+panel2[l].title+'" data-oddsexp="'+panel2[l].desc+'"><em>'+panel2[l].title.substr(panel2[l].title.indexOf("_")+1)+'</em><span>'+panel2[l].value+'</span></div></dd>'
                            }
                        }

                        var panel3 =  wanfOdds[i].panel["区段"];
                        if(panel3){
                            betCon+='</dl><dl><dt>猜区段</dt></dl><dl>';
                            for(var l =0; l< panel3.length; l++){
                                betCon+='<dd class="w100"><div class="betPlay" data-title="'+panel3[l].title+'" data-oddsexp="'+panel3[l].desc+'"><em>'+panel3[l].title.substr(panel3[l].title.indexOf("_")+1)+'</em><span>';
                                if(panel3[l].title.substr(panel3[l].title.indexOf("_")+1) =="A"){
                                    betCon+="3~7";
                                }else if(panel3[l].title.substr(panel3[l].title.indexOf("_")+1) =="B"){
                                    betCon+="8~14";
                                }else if(panel3[l].title.substr(panel3[l].title.indexOf("_")+1) =="C"){
                                    betCon+="15~19";
                                }
                                betCon+='</span><span>'+panel3[l].value+'</span></div></dd>';
                            }
                        }


                        betCon+='</dl><div class="oddsExplain" data-index="\\\'+i+\\\'"><i class="ico_peilv"></i>赔率说明</div></div>';
                        $(".data-con").append(betCon);
                        $("#betTitle").hide();
                        $(".betRightCon").css({
                            "padding-top":"0",
                            "border-top":"0"
                        })

                        //选号操作
                        //$(".data-con .betRightCon dl dd").on( 'click','.betPlay',function() {
                        //    xh($(this));
                        //});
                    }
                    if(title =="猜冠亚"){
                        betCon+='<ul>';
                        for(var l =1; l<11; l++){
                            betCon+='<li><div class="betPlay" data-title="冠亚_'+l+'" data-oddsexp="'+wanfOdds[i].panel[0].desc+'">'+l+'</div></li>';
                        }
                        betCon +='<p class="" style="text-align: left;padding-left: 5px;">倍数：'+  wanfOdds[i].panel[0].value +'</p><p style="text-align: left; color: #999;padding-left: 5px;">备注：每次竞猜需要两个号码，顺序不限（两个号码为一注）</p>';
                        betCon+='</ul><div class="oddsExplain" data-index="\\\'+i+\\\'"><i class="ico_peilv"></i>赔率说明</div></div>';
                        $(".data-con").append(betCon);


                        //选号操作
                        //$(".data-con .betRightCon ul li").on( 'click','.betPlay',function() {
                        //    xh($(this));
                        //});
                    }


                    if(title =="猜总和" || title =="猜龙虎"){
                        var panel =  wanfOdds[i].panel;
                        betCon+='<dl>';
                        for(var l =0; l< panel.length; l++){
                            betCon+='<dd><div class="betPlay" data-title="'+panel[l].title+'" data-oddsexp="'+panel[l].desc+'"><em>'+panel[l].title.substr(panel[l].title.indexOf("_")+1)+'</em><span>'+panel[l].value+'</span></div></dd>'
                        }
                        betCon+='</dl><div class="oddsExplain" data-index="\\\'+i+\\\'"><i class="ico_peilv"></i>赔率说明</div></div>';
                        $(".data-con").append(betCon);
                    }


                }


            }

        }



        $(".betRightSel").on("change","select",function () {
            $(".betRightSel span").html($(this).val());
            $(".betRightCon .desc").html("");
            var betCon ="";
            $(".betRightCon dl").html("");
            for(var i =0; i<wanfOdds.length; i++){
                if(title == wanfOdds[i].name){
                    var panel =wanfOdds[i].panel[$(this).val()];
                    for(var l =0; l< panel.length; l++){
                        if(wanfOdds[i].name == "猜龙虎"){
                            betCon+='<dd><div class="betPlay" data-title="'+panel[l].title+'" data-oddsexp="'+panel[l].desc+'"><em>'+panel[l].title.substr(panel[l].title.indexOf("_")+1)+'</em><span>'+panel[l].value+'</span></div></dd>'

                        }else{
                            betCon+='<dd><div class="betPlay" data-title="'+panel[l].title+'" data-oddsexp="'+wanfOdds[i].panel['说明'][panel[l].title.substr(panel[l].title.indexOf("_")+1)]+'"><em>'+panel[l].title.substr(panel[l].title.indexOf("_")+1)+'</em><span>'+panel[l].value+'</span></div></dd>'

                        }
                    }
                }
            }
            $(".betRightCon dl").append(betCon);
            //选号操作
            $(".data-con .betRightCon dl dd").on( 'click','.betPlay',function() {
                xh($(this));
            });
        })

        //选号操作
        $(".data-con .betRightCon").on( 'click','.betPlay',function() {
            xh($(this));
        });
        $(".data-con .betRightCon").on("click",".oddsExplain",function(){
            var odds_explain = localStorage.getItem("odds_exp")
            var alert ='<div class="popupAlert"><div class="config-alert"><div class="tit">赔率说明</div> <p style="display: block">'+odds_explain+'</p><div class="btn"><button class="confirm">确定</button></div></div></div>';
            var p = $(alert).appendTo($('body'));

            p.find('.confirm').off().on('click', function () {
                p.remove();
            });
            return false
        });

    })

    ////选号操作
    //$(".data-con .betRightCon dl dd").on( 'click','.betPlay',function() {
    //    xh($(this));
    //});



    //隐藏追号键盘
    $(".chaseWarp").click(function(e){
        e = e || window.event;
        var drag = $(".chaseWarp"),
            dragel = $(".chaseWarp")[0],
            target = e.target;
        if (dragel === target && !$.contains(dragel, target)) {
            $(".zhuiHao").slideUp(function(){

                $(".pit").removeClass("pit");

                drag.hide();
                $("#zhuiHList").empty();
                $(".zhuiHaoList").hide();

                aWanf=[];
                aJine=[];
                fnSet.scrollTop();
                //fag= true;
            });

        }
    });

    $("#inputNumber").focus(function(){
        $(".betfun .chip span").each(function(){
            $(this).removeClass("active");
        })
    })
    
    // $("#inputNumber").blur(function () {
    //     $(this).parent().hide();
    //     $(".stakeWarp").show();
    // })
    //最小投注
    $(".betfun").on("click",".bet-min", function() {
        var limit1  = Number($("#lower").val());
        var limit2  = Number($("#user_group_lower").val());
        if(limit1 > limit2){
            var limit = limit1;
        }else{
            var limit = limit2;
        }
        $("#inputNumber").val(limit);
        $(".betfun .chip span").each(function(){
            $(this).removeClass("active");
        })
    })
    $(".chase-form-right").on("click",".bet-min", function() {
        var limit1  = Number($("#lower").val());
        var limit2  = Number($("#user_group_lower").val());
        if(limit1 > limit2){
            var limit = limit1;
        }else{
            var limit = limit2;
        }
        $(".money-text").val(limit);
    })
    //筹码选择
    $(".betfun").on("click",".chip span", function() {
        $(this).addClass("active").siblings().removeClass("active");
        var val = $(this).text();
        var zMoney = $(".icoAcer").html();
        if(val == "梭哈"){
            $(".list-con p").each(function(){
                zMoney -= $(this).children("span").html();
            })
            zMoney = Math.floor(zMoney);
            //var wanf =$("#touzhuzhi").text();
            //aWanf.push(wanf);
            //aJine.push(Math.floor(zMoney));
            //fnSet.button(wanf,Math.floor(zMoney));
            //fnSet.scrollTop();
            $("#inputNumber").val(zMoney);
        }else if(val.indexOf("K") != -1){
            val = Number(val.substr(0,val.length-1))*1000;
            $("#inputNumber").val(val);
        }else{
            $("#inputNumber").val($(this).text());
        }
    })
    //快捷投注
    $(".betfun").on("click",".bet-quick", function(){
        if(aJine !=""){
            var jineLength =aJine.length;
            if(aJine[jineLength-1] =="-1"){
                fnSet.alert("您已经梭哈");
                return false;
            }
        }

        //新增特殊玩法描述
        $(".play3 .desc").text($(this).children("div").attr("data-desc"));
        $(".cenMoneyWarp").show();
        $("#touzhuzhi").html($(this).find("span").text());
        var height =$(".cenMoney").outerHeight();
        $(".cenMoney").css("margin-top",-(height/2))
        event.stopPropagation();
    })

    //快捷投注 金额值梭哈
    $(".text-ul").on("click","li", function(){
        var val = $(this).text();
        var zMoney = $(".icoAcer").html();
        if(val == "梭哈"){
            $(".textArea div p").each(function(){
                zMoney -= $(this).children("span").html();
            })
            $("#touzhuzhi").text();
            var wanf =$("#touzhuzhi").text();
            aWanf.push(wanf);
            aJine.push(Math.floor(zMoney));
            fnSet.button(wanf,Math.floor(zMoney));
            fnSet.scrollTop();
        }else{
            $("#inputNumber").val($(this).text());
        }
        $(".cenMoneyWarp").hide();
    });
    $(".cigBtn").on("click touch", ".cancel", function(){
        $(".cenMoneyWarp").hide();
    })

        // var money = $(".roomHead1 .icoAcer").text();
        // if(!fag1){
        //     setTimeout(function(){
        //         fag1 =true;
        //     },1000);
        //     return false;
        // }
        // //if(fag){
        //     $('.customNews').css("bottom","492px");
        //     $(".bettingKey").show();
        //     //var height =$(".roomNews").outerHeight()+$(".bettingKey").outerHeight();
        //     //$(".room").css("bottom",height);
        //    // roomBtu(height);
        //     $("#speak").hide();
        //     $(".keyboard").show();
        //
        //     $(this).hide();
        //     $('#bettingBtu1').show();
        //
        //     // $(this).text("投注");
        //     $(".textArea").html('<div data-content="false"></div>');
        //     //投注金额键盘
        //     $(".play >ul").on("click","li", function(){
        //         if(aJine !=""){
        //             var jineLength =aJine.length;
        //             if(aJine[jineLength-1] =="-1"){
        //                 fnSet.alert("您已经梭哈");
        //                 return false;
        //             }
        //         }
        //
        //         //新增特殊玩法描述
        //         $(".play3 .desc").text($(this).attr("data-desc"));
        //
        //         $(".cenMoneyWarp").show();
        //         $("#touzhuzhi").html($(this).children("span").text());
        //         var height =$(".cenMoney").outerHeight();
        //         $(".cenMoney").css("margin-top",-(height/2))
        //         event.stopPropagation();
        //     })
        //     fag =false;
        //}else{
        //     var zMoney = 0;
        //     for(var i=0; i<aJine.length;i++){
        //         zMoney =aJine[i]+zMoney;
        //     }
           // if(fag1){
           //      if(aWanf ==""){
           //          fnSet.alert("请投注！");
           //      }else if(money<zMoney){
           //          fnSet.alert("余额不足");
           //      }else{
           //          if(fag1){
           //
           //              var param = {
           //                  "commandid": "3006",
           //                  "nickname": userinfo.nickname,
           //                  "way":aWanf,
           //                  "money":aJine,
           //                  "avatar":userinfo.head_url
           //              };
           //
           //              wsSendMsg(param);
           //          }

                   // }
                   // fag1 =false;
               // }

           // }
        //}
    function bet (type) {
        var money = $(".roomHead1 .icoAcer").text();//账号余额
        var zMoney = 0;
        var issue = $("#issue").attr("data-issue");
        for(var i=0; i<aJine.length;i++){
            zMoney =aJine[i]+zMoney;
        }

        var list = [];
        for(i in aWanf) {
            // var reverse_check = checkReverse(issue,aWanf[i],aWanf);
            // if(!reverse_check) return;
            switch (aWanf[i]) {
                case '大':
                case '小':
                case '单':
                case '双':
                    if(!list["blsd"]) list["blsd"] = {money:aJine[i]};
                    else list["blsd"].money += aJine[i];
                    break;
                case '极大':
                case '极小':
                    if(!list["mimx"]) list["mimx"] = {money:aJine[i]};
                    else list["mimx"].money += aJine[i];
                    break;
                case '大单':
                case '小单':
                case '大双':
                case '小双':
                    if(!list["mix"]) list["mix"] = {money:aJine[i]};
                    else list["mix"].money += aJine[i];
                    break;
                case '红':
                    if(!list["red"]) list["red"] = {money:aJine[i]};
                    else list["red"].money += aJine[i];
                    break;
                case '绿':
                    if(!list["green"]) list["green"] = {money:aJine[i]};
                    else list["green"].money += aJine[i];
                    break;
                case '蓝':
                    if(!list["blue"]) list["blue"] = {money:aJine[i]};
                    else list["blue"].money += aJine[i];
                    break;
                case '豹子':
                    if(!list["leo"]) list["leo"] = {money:aJine[i]};
                    else list["leo"].money += aJine[i];
                    break;
                case '正顺':
                    if(!list["zhengshun"]) list["zhengshun"] = {money:aJine[i]};
                    else list["zhengshun"].money += aJine[i];
                    break;
                case '倒顺':
                    if(!list["daoshun"]) list["daoshun"] = {money:aJine[i]};
                    else list["daoshun"].money += aJine[i];
                    break;
                case '半顺':
                    if(!list["banshun"]) list["banshun"] = {money:aJine[i]};
                    else list["banshun"].money += aJine[i];
                    break;
                case '乱顺':
                    if(!list["luanshun"]) list["luanshun"] = {money:aJine[i]};
                    else list["luanshun"].money += aJine[i];
                    break;
                case '对子':
                    if(!list["pair"]) list["pair"] = {money:aJine[i]};
                    else list["pair"].money += aJine[i];
                    break;
                default:
                    if(!list[aWanf[i]]) list[aWanf[i]] = {money:aJine[i]};
                    else list[aWanf[i]].money += aJine[i];
                    break;
            }
        }

        for(i in list) {
            var new_way = "";
            var new_name = "";
            switch (i) {
                case 'blsd':
                    new_way = "大";
                    new_name = "大小单双";
                    break;
                case 'mimx':
                    new_way = "极大";
                    new_name = "极大极小";
                    break;
                case 'mix':
                    new_way = "大单";
                    new_name = "组合";
                    break;
                case 'red':
                    new_way = "红";
                    new_name = "红";
                    break;
                case 'green':
                    new_way = "绿";
                    new_name = "绿";
                    break;
                case 'blue':
                    new_way = "蓝";
                    new_name = "蓝";
                    break;
                case 'leo':
                    new_way = "豹子";
                    new_name = "豹子";
                    break;
                case 'zhengshun':
                    new_way = "正顺";
                    new_name = "正顺";
                    break;
                case 'daoshun':
                    new_way = "倒顺";
                    new_name = "倒顺";
                    break;
                case 'banshun':
                    new_way = "半顺";
                    new_name = "半顺";
                    break;
                case 'luanshun':
                    new_way = "乱顺";
                    new_name = "乱顺";
                    break;
                case 'pair':
                    new_way = "对子";
                    new_name = "对子";
                    break;
                default:
                    new_way = i;
                    new_name = i;
                    break;
            }
            var bet_limit_check = checkMoney(issue,new_way,list[i].money);
            if(!bet_limit_check) {
                if(type ==1){
                    aWanf = [];
                    aJine = [];
                    aWanfT = [];
                }
                return;
            }
        }
        var group_check = checkGroup(issue,zMoney,1);
        if(group_check>0){
            var group_name = "";
            if($("#user_group_name").val()!=undefined) group_name = $("#user_group_name").val();
            if(group_check==1) {
                var group_upper = 1000000;
                if($("#user_group_upper").val()!=undefined) group_upper = parseFloat($("#user_group_upper").val());
                fnSet.alert("您的投注额累计超过，您所属会员组("+group_name+")上限:"+group_upper);
                if(type ==1){
                    aWanf = [];
                    aJine = [];
                    aWanfT = [];
                }
                return;
            }else if(group_check==2) {
                var group_lower = 0;
                if($("#user_group_lower").val()!=undefined) group_lower = parseFloat($("#user_group_lower").val());
                fnSet.alert("您的投注额低于，您所属会员组("+group_name+")下限:"+group_lower);
                if(type ==1){
                    aWanf = [];
                    aJine = [];
                    aWanfT = [];
                }
                return;
            }
        }

        if(aWanf ==""){
            fnSet.alert("请投注！");
        }else if(money<zMoney){
            fnSet.alert("余额不足");
        } else{
            if(fag1){
                var param = {
                    "commandid": "3006",
                    "nickname": userinfo.nickname,
                    "way":aWanf,
                    "money":aJine,
                    "avatar":userinfo.head_url
                };
                //console.log(param);
                if(type == 3){
                    $.confirm("本次投注共"+sum(aJine)+"元宝",function(){
                        fag1 =false;
                        wsSendMsg(param);
                        // //投注后清空
                        $(".list-con").html("");
                        reset(); //清空所有选号.pit
                        aWanf=[];
                        aJine=[];
                        aWanfT=[];

                        $(".bettingWarp").hide();
                        $(".bettingKey").hide();

                        $(".betCon").css("top","100%");
                        setTimeout(function(){
                            $(".betWarp").hide().removeClass("betting");
                            $(".roomNews").show();
                        },480);
                        // $(".betList").hide();
                        $(".betList").css("top","100%");
                        setTimeout(function () {
                            $(".betListWarp").hide();
                        },500);
                        stakeflag =false;
                        $(".roomNews").show();
                        $(".hasStake").hide();
                        $(".noStake").show();
                    })
                }else{
                    fag1 =false;
                    wsSendMsg(param);
                    // //投注后清空
                    $(".list-con").html("");
                    reset(); //清空所有选号.pit
                    aWanf=[];
                    aJine=[];
                    aWanfT=[];

                    $(".bettingWarp").hide();
                    $(".bettingKey").hide();
                    $(".betList").hide();
                    $(".roomNews").show();
                }

            }

            //fnSet.scrollTop();
        }
    }

    //一键投注
    $("body").on("click","#bettingBtu1", function(){
        //aWanf=[];
        //aJine=[];
        if(aWanf.length == 0){
            var wanf =$(".play ul li .pit span").text();
            var jine =window.parseInt($("#inputNumber").val());
            var wanfT =$(".jianpanTitle h4 em").text();
            //if(text == "确认"){
            if(wanf ==""){
                fnSet.alert("请投注！");
                return false;
            }
            if(jine =="" || isNaN(jine)){
                fnSet.alert("请输入金额！");
                return false;
            }
            aWanf.push(wanf);
            aJine.push(jine);
            aWanfT.push(wanfT);
        }

        bet(1);
    })

    //注单确认投注
    $(".betList").on("click",".list-bet", function(){
        bet(2);
    })
    //pkft注单确认投注
    $(".hasStake").on("click","#listBet", function(){
        //$(".stake-money").text()

        bet(3);
    })

    function sum(arr) {
        var s = 0;
        for (var i=arr.length-1; i>=0; i--) {
            s += arr[i];
        }
        return s;
    }

    function ballAnimation() {
        var $pointDiv = $('<div id="pointDivs">').appendTo('body');
        for(var i = 0; i < 5; i++) {
            $('<div class="point-outer point-pre"><div class="point-inner"/></div>').appendTo($pointDiv);
        }

        var startOffset = $(".pit").offset();
        var endTop = window.screen.height - 50,
            endLeft = 15,
            left = startOffset.left,
            top = startOffset.top;
        var outer = $('#pointDivs .point-pre').first().removeClass("point-pre").css({
            left: left + 'px',
            top: top + 'px'			});
        var inner = outer.find(".point-inner");

        setTimeout(function() {
            outer[0].style.webkitTransform = 'translate3d(0,' + (endTop - top) + 'px,0)';
            inner[0].style.webkitTransform = 'translate3d(' + (endLeft - left) + 'px,0,0)';
            setTimeout(function() {
                outer.removeAttr("style").addClass("point-pre");
                inner.removeAttr("style");
            }, 500);
            //这里的延迟值和小球的运动时间相关
        }, 1);
        //小球运动坐标
    }

    function stakeNum(aJine){
        $(".stake-num").text(aJine.length);
        $(".stake-money").text(sum(aJine));
        $(".stake-num-txt span").text(aJine.length);
        $(".stake-num").animate({ width: "25", height: "25", lineHeight: "25px" }, "1000", function(){ $(".stake-num").animate({height: "20", width: "20", lineHeight: "20px"},"500")});
    }

    //加入注单 //投注金额键盘button
    var aWanfT = [];
    $("#butJp").on("click",function(){
        //todo

        //ballAnimation()
        if(userinfo.lottery_type ==2||userinfo.lottery_type ==9 || userinfo.lottery_type ==4 || userinfo.lottery_type ==5 || userinfo.lottery_type == 6 || userinfo.lottery_type == 11) {
            if (!$(".pit").length) {
                fnSet.alert("请选择玩法！");
                return false;
            }
            if ($("#inputNumber").val()=="" || !/^[1-9]\d*$/.test($("#inputNumber").val())) {
                fnSet.alert("请输入有效金额！");
                return false;
            }
            //动画效果
            setTimeout(ballAnimation(),800);
            if($("#betTitle").text() =="猜冠亚"){
                var wanf = "冠亚_" + $(".pit").eq(0).text() +"_"+$(".pit").eq(1).text();
                var jine = window.parseInt($("#inputNumber").val());
                var wanfT = $("#betTitle").text();
                aWanf.push(wanf);
                aJine.push(jine);
                aWanfT.push(wanfT);
            }else{
                for(var i =0; i< $(".pit").length; i++){
                    var wanf = $(".pit").eq(i).attr("data-title");
                    var jine = window.parseInt($("#inputNumber").val());
                    var wanfT = $("#betTitle").text();
                    aWanf.push(wanf);
                    aJine.push(jine);
                    aWanfT.push(wanfT);
                }
            }

            $(".pit").removeClass("pit");
            var winStop = $("#if_win_stop").attr('checked') ? 1 : 0;
            console.log(winStop);
            //aWanf.push(wanf);
            //aJine.push(jine);
            fnSet.button(aWanf, aJine, aWanfT);
            fnSet.scrollTop();

            //$(".betList").show();
            $(".cenMoneyWarp").hide();

        }else{
            var len = $(".play ul li .pit").length;
            if (len > 1) {  //北京幸运飞艇和幸运飞艇猜数字多选
                for (var i = 0; i < len; i++) {
                    var wanf = $(".play ul li .pit span").eq(i).text();
                    var jine = window.parseInt($("#inputNumber").val());
                    var wanfT = $(".nav-title h4.active p").text();
                    if (wanf == "" || wanf == undefined) {
                        fnSet.alert("请选择玩法！");
                        return false;
                    }
                    if (jine == "" || isNaN(jine)) {
                        fnSet.alert("请输入金额！");
                        return false;
                    }
                    aWanf.push(wanf);
                    aJine.push(jine);
                    aWanfT.push(wanfT);
                }
            } else {
                var wanf = $(".play ul li .pit span").text();
                var jine = window.parseInt($("#inputNumber").val());
                if (userinfo.lottery_type == 2 || userinfo.lottery_type == 9 || userinfo.lottery_type == 4 || userinfo.lottery_type == 5 || userinfo.lottery_type == 6 || userinfo.lottery_type == 11) {
                    var wanfT = $(".nav-title h4.active p").text();
                } else {
                    var wanfT = $(".jianpanTitle h4 em").text();
                }

                if (wanf == "") {
                    fnSet.alert("请选择玩法！");
                    return false;
                }
                if (jine == "" || isNaN(jine)) {
                    fnSet.alert("请输入金额！");
                    return false;
                }
                aWanf.push(wanf);
                aJine.push(jine);
                aWanfT.push(wanfT);
            }
            var winStop = $("#if_win_stop").attr('checked') ? 1 : 0;
            console.log(winStop);
            //aWanf.push(wanf);
            //aJine.push(jine);
            fnSet.button(aWanf, aJine, aWanfT);
            fnSet.scrollTop();

            $(".xyjnd .betList").show();
            $(".cenMoneyWarp").hide();
            //动画效果
            setTimeout(ballAnimation(),800);
        }

            //$("#inputNumber").val("");

        //pkft
        stakeNum(aJine);  //更新状态
        //$(".bettingKey-pkft .betList").show();
        $(".betWarp .operate").hide();
        $(".stakeWarp").show();
        $(".hasStake").show();
        $(".noStake").hide();
        // $("#inputNumber").val("");

    });

    //pkft取消
    $("#cancelJp").on("click",function(){
        $(".operateWarp").hide();
        $("#inputNumber").val("");
        $(".operate").hide();
        $(".stakeWarp").show();
        $(".betRightCon .pit").each(function () {
            $(this).removeClass("pit");
        })

    })
    //pkft打开注单
    var stakeflag = false;
    $(".stake-icon").on("click",function(){
        var len = $(".betList .list-con p").length;
        if(len>0){
            if(stakeflag){
                $(".betList").css("top","100%");
                setTimeout(function () {
                    $(".betListWarp").hide();
                },500);
                stakeflag =false;
            }else{
                $(".betListWarp").show();

                $(".betList").css("top",$("body").height()-235);


                stakeflag =true;
            }
            //todo
            // $(".betLeftNav").css("height","392px");
            // $(".betRight").css("height","392px");
        }
    })
    $(".betListWarp").on("click",function () {
        stakeflag =false;
        $(".betList").css("top","100%");
        setTimeout(function () {
            $(".betListWarp").hide();
        },500);
    })
    $(".betList").on("click",function (event) {
        event.stopPropagation();
    })
    //删除投注
    $('.list-con').on("click touch",".close",function(){
        var index =$(this).parent("p").index();
        $(this).parent("p").remove();
        aJine.splice(index,1);
        aWanf.splice(index,1);
        aWanfT.splice(index,1);
        fnSet.scrollTop();

        stakeNum(aJine);  //更新状态
        if( $(".betList .list-con p").length < 1){
            $(".betList").css("top","100%");
            setTimeout(function () {
                $(".betListWarp").hide();
            },300);
            // $(".betList").hide();
            $(".hasStake").hide();
            $(".noStake").show();
        }
    })
    //清除注单
    $('.list-tit').on("click touch",".list-empty",function(){
        $.confirm("确认清空注单吗？", function(){
            $(".list-con").html("");
            aWanf=[];
            aJine=[];
            $("#inputNumber").val("");
            stakeNum(aJine);  //更新状态
            $(".betList").css("top","100%");
            setTimeout(function () {
                $(".betListWarp").hide();
            },300);
            // $(".betList").hide();
            $(".hasStake").hide();
            $(".noStake").show();
        })

    })

    //监听高度变化
    $("body").on("input",".list-con", function(){
        //$('.room').css("bottom",$(".roomNews").outerHeight());
        fnSet.scrollTop();
    })
    //发言
    $("body").on("click","#speak", function(){
        var content =$(".textArea>div").text();
        if(content !=""){
            var param = {
                "commandid": "3003",
                "nickname": userinfo.nickname,
                "content":content,
                "uid": userinfo.userid,
                "avatar":userinfo.head_url
            };
            //console.log(param);
            wsSendMsg(param);
            $(".textArea>div").text("");
           // var height =$(".roomNews").outerHeight();
            //roomBtu(height)
            fnSet.scrollTop();
        }else{
            fnSet.alert("内容不能为空！")
        }

    })
    //取消当前投注
    var flag = false;
    $("body").on("click",".betTixing",function(){
        if(flag){
          $(".betTixing .jt").css("transform","rotate(0deg)");
            flag =false;
        }else{
            $(".betTixing .jt").css("transform","rotate(180deg)");
            flag =true;
        }
        $(".quxiao").toggle();
    })
    //取消当前投注确认
    $("body").on("click","#querenBut",function(){
        var param = {
            "commandid": "3016",
            "uid": userinfo.userid,
            "order_no": order_no,
        };
        wsSendMsg(param);
        $(".popup").hide();
    })

    //第一次注册修改昵称
    $("body").on("click","#keepName",function(){
        var nickname =$("input[name=nickname]").val();
        if(nickname ==""){
            fnSet.alert("请输入昵称");
        }else if(nickname.length >10){

        }else{
            $.ajax({
                url: btn_url,
                data:{"nickname":nickname},
                type: "POST",
                dataType:'json',
                success:function(msg){
                    if(msg.status == "0"){
                        userinfo.nickname =nickname;
                        initWebSocket();
                        $('.popup').hide();
                    }
                },
                error:function(er){
                }
            });
            // $.ajax({ url: btn_url,{"nickname": nickname},type:POST, success: function(msg){
            //     var msg =JSON.parse(msg);
            //     if(msg.status == "0"){
            //         userinfo.nickname =nickname;
            //         initWebSocket();
            //         $('.popup').hide();
            //     }
            // }});
        }
    })
    //修改昵称取消
    $("body").on("click","#ncquxiao",function(){
        window.location.href ="/?m=web&c=lobby&a=index";
    })
    //跟投
    $("body").on("click",".userBetting dd .bet-con-self, .userBetting dd .bet-con",function(){
        var nameMz =$(this).parent().siblings("h3").find("b").text();
        var timeTz =$(this).children("div").children("span").eq(0).text()
        var gentou ='<div class="config-confirm"><h3>跟投</h3><div class="gentouCon">';
        var genZhi = $(this).find("p");
        var player = $(this).siblings("h3").children("b").html();
        gentou+='<div class="gentou_player"><label>玩家:</label><span>'+player+'</span></div><div class="gentou_issue"><label>期数:</label><span>'+$('#issue').text()+'</span></div><div class="con"><div class="con-l">类别:</div><div class="con-r">';
        for(var i=0; i<genZhi.length; i++){
            gentou+='<p><span style="color: #d22727">'+genZhi.eq(i).children("span").text()+'</span> <label class="configAcer" style="text-align: right;"><em>'+genZhi.eq(i).find("em").text()+'</em></label></p>'
        }
        gentou+='</div></div></div><div class="cigBtn"><button id="genTou">确定</button><button class="cancel">取消</button></div>';
        $(".popup").html(gentou);
        $(".popup").show();
    })
    $("body").on("click","#genTou",function(){
        var genWanf =new Array();
        var genJine =new Array();
        var genZhi =$(this).parent().siblings(".gentouCon").find(".con-r").children("p");
        var sub_total = 0;
        for(var i=0; i<genZhi.length; i++){
            genWanf.push(genZhi.eq(i).children("span").text());
            genJine.push(genZhi.eq(i).find("em").text());
            sub_total += parseFloat(genZhi.eq(i).find("em").text());
        }
        //提前限额判断
        var issue = $("#issue").attr("data-issue");
        var list = [];
        for(i in genWanf) {
            //逆向投注检测
            // var reverse_check = checkReverse(issue,genWanf[i],genWanf);
            // if(!reverse_check) return;

            switch (genWanf[i]) {
                case '大':
                case '小':
                case '单':
                case '双':
                    if(!list["blsd"]) list["blsd"] = {money:parseFloat(genJine[i])};
                    else list["blsd"].money += parseFloat(genJine[i]);
                    break;
                case '极大':
                case '极小':
                    if(!list["mimx"]) list["mimx"] = {money:parseFloat(genJine[i])};
                    else list["mimx"].money += parseFloat(genJine[i]);
                    break;
                case '大单':
                case '小单':
                case '大双':
                case '小双':
                    if(!list["mix"]) list["mix"] = {money:parseFloat(genJine[i])};
                    else list["mix"].money += parseFloat(genJine[i]);
                    break;
                case '红':
                    if(!list["red"]) list["red"] = {money:parseFloat(genJine[i])};
                    else list["red"].money += parseFloat(genJine[i]);
                    break;
                case '绿':
                    if(!list["green"]) list["green"] = {money:parseFloat(genJine[i])};
                    else list["green"].money += parseFloat(genJine[i]);
                    break;
                case '蓝':
                    if(!list["blue"]) list["blue"] = {money:parseFloat(genJine[i])};
                    else list["blue"].money += parseFloat(genJine[i]);
                    break;
                case '豹子':
                    if(!list["leo"]) list["leo"] = {money:parseFloat(genJine[i])};
                    else list["leo"].money += parseFloat(genJine[i]);
                    break;
                case '正顺':
                    if(!list["zhengshun"]) list["zhengshun"] = {money:parseFloat(genJine[i])};
                    else list["zhengshun"].money += parseFloat(genJine[i]);
                    break;
                case '倒顺':
                    if(!list["daoshun"]) list["daoshun"] = {money:parseFloat(genJine[i])};
                    else list["daoshun"].money += parseFloat(genJine[i]);
                    break;
                case '半顺':
                    if(!list["banshun"]) list["banshun"] = {money:parseFloat(genJine[i])};
                    else list["banshun"].money += parseFloat(genJine[i]);
                    break;
                case '乱顺':
                    if(!list["luanshun"]) list["luanshun"] = {money:parseFloat(genJine[i])};
                    else list["luanshun"].money += parseFloat(genJine[i]);
                    break;
                case '对子':
                    if(!list["pair"]) list["pair"] = {money:parseFloat(genJine[i])};
                    else list["pair"].money += parseFloat(genJine[i]);
                    break;
                default:
                    if(!list[genWanf[i]]) list[genWanf[i]] = {money:parseFloat(genJine[i])};
                    else list[genWanf[i]].money += parseFloat(genJine[i]);
                    break;
            }
        }

        for(i in list) {
            var new_way = "";
            var new_name = "";
            switch (i) {
                case 'blsd':
                    new_way = "大";
                    new_name = "大小单双";
                    break;
                case 'mimx':
                    new_way = "极大";
                    new_name = "极大极小";
                    break;
                case 'mix':
                    new_way = "大单";
                    new_name = "组合";
                    break;
                case 'red':
                    new_way = "红";
                    new_name = "红";
                    break;
                case 'green':
                    new_way = "绿";
                    new_name = "绿";
                    break;
                case 'blue':
                    new_way = "蓝";
                    new_name = "蓝";
                    break;
                case 'leo':
                    new_way = "豹子";
                    new_name = "豹子";
                    break;
                case 'zhengshun':
                    new_way = "正顺";
                    new_name = "正顺";
                    break;
                case 'daoshun':
                    new_way = "倒顺";
                    new_name = "倒顺";
                    break;
                case 'banshun':
                    new_way = "半顺";
                    new_name = "半顺";
                    break;
                case 'luanshun':
                    new_way = "乱顺";
                    new_name = "乱顺";
                    break;
                case 'pair':
                    new_way = "对子";
                    new_name = "对子";
                    break;
                default:
                    new_way = i;
                    new_name = i;
                    break;
            }
            var bet_limit_check = checkMoney(issue,new_way,list[i].money);

            if(!bet_limit_check) {
                return;
            }
        }
        var group_check = checkGroup(issue,sub_total,1);
        if(group_check>0){
            var group_name = "";
            if($("#user_group_name").val()!=undefined) group_name = $("#user_group_name").val();
            if(group_check==1) {
                var group_upper = 1000000;
                if($("#user_group_upper").val()!=undefined) group_upper = parseFloat($("#user_group_upper").val());
                fnSet.alert("您的投注额累计超过，您所属会员组("+group_name+")上限:"+group_upper);
                return;
            }else if(group_check==2) {
                var group_lower = 0;
                if($("#user_group_lower").val()!=undefined) group_lower = parseFloat($("#user_group_lower").val());
                fnSet.alert("您的投注额低于，您所属会员组("+group_name+")下限:"+group_lower);
                return;
            }
        }

        var param = {
            "commandid": "3006",
            "nickname": userinfo.nickname,
            "way":genWanf,
            "money":genJine,
            "avatar":userinfo.head_url,
            "ext_a":1            
        };
        wsSendMsg(param);
        // genWanf=[];
        // genJine=[];
        $(".popup").hide();
    })

    $("body").on("input",'#inputNumber',function(){
        var text =$(this).val();

        if(text.indexOf("0")=="0"){
            $(this).val("");
        }

    })
    // //赔率说明
    // $(".odds-info").off().click(function(){
    //     var alert ='<div class="popupAlert"><div class="config-alert"><div class="tit">赔率说明</div> <p>'+odds_explain+'</p><div class="btn"><button class="confirm">确定</button></div></div></div>';
    //     var p = $(alert).appendTo($('body'));
    //
    //     p.find('.confirm').off().on('click', function () {
    //         p.remove();
    //     });
    // });
    // $(".odds_info_pkft").off().click(function(){
    //     var alert ='<div class="popupAlert"><div class="config-alert"><div class="tit">赔率说明</div> <p>'+odds_explain+'</p><div class="btn"><button class="confirm">确定</button></div></div></div>';
    //     var p = $(alert).appendTo($('body'));
    //
    //     p.find('.confirm').off().on('click', function () {
    //         p.remove();
    //     });
    // });

    //弹出层上面的X
    $("body").on("click",".configClose,.cancel,.win-close",function(){
        $(".popup").fadeOut();
    })
    //回车发送消息
    $("body").on("keyup",".textArea div",function(e){
        e = e? e : (window.event ? window.event : null);
        if(e.keyCode==13)//Enter
        {
            document.getElementById("speak").click();
        }
    })

    //取消指定投注
    $(".quxiao ul li .close").live('click', function() {
        order_no = $(this).attr("order-no"); //赋予全局订单号
        var quxiaoT = '<div class="config-confirm"><div class="tit">提示</div><div class="con"><p>取消投注后将返还所有下注金额，是否继续？</p></div><div class="btn"><button id="querenBut" class="confirm">确认</button><button class="cancel">取消</button></div></div>';
        $(".popup").html(quxiaoT);
        $(".popup").show();
    });

    //取消当期所有投注
    $("body").on("click","#quxiaoSy",function(){
        var quxiaoT = '<div class="config-confirm"><div class="tit">提示</div><div class="con"><p>取消投注后将返还所有下注金额，是否继续？</p></div><div class="btn"><button id="qbBut" class="confirm">确认</button><button class="cancel">取消</button></div></div>';
        $(".popup").html(quxiaoT);
        $(".popup").show()
    })

    //取消当前所有投注确认
    $("body").on("click","#qbBut",function(){
        var param = {
            "commandid": "3009",
            "uid": userinfo.userid,
        };
        wsSendMsg(param);
        var issue = $("#issue").attr("data-issue");
        // $(".issue_"+issue).remove();
        $(".popup").hide();
    })

    //客服图标 移动
    var bodyWidth = $("body").width();
    var bodyHeight = $("body").height();
    var dragWidth =$("#drag").width();
    var move = false;//移动标记
    var seup =false;
    var _x, _y;//鼠标离控件左上角的相对位置

    //drag.addEventListener("touchstart",function () {
    //    move = true;
    //    seup =true;
    //    _x = event.targetTouches[0].pageX - parseInt($("#drag").css("left"));
    //    _y = event.targetTouches[0].pageY - parseInt($("#drag").css("top"));
    //})
    //drag.addEventListener("touchmove",function (){
    //    if (event.targetTouches.length == 1) {
    //        event.preventDefault();// 阻止浏览器默认事件，重要
    //        if (move) {
    //            var x = event.targetTouches[0].pageX - _x;//控件左上角到屏幕左上角的相对位置
    //            var y = event.targetTouches[0].pageY - _y;
    //            $("#drag").css({"top": y, "left": x});
    //        }
    //    }
    //
    //})
    //drag.addEventListener("touchend",function (){
    //    if(seup){
    //        var left = $("#drag").offset().left;
    //        var top = $("#drag").offset().top;
    //        if(top<86){
    //            $("#drag").css({"top": '44px'});
    //        }
    //        // if(top > 44 || top > bodyHeight -102){
    //            if((bodyWidth/2) < left){
    //                $("#drag").css({"left": bodyWidth-53+"px"});
    //            }else{
    //                $("#drag").css({"left": "15px"});
    //            }
    //        // }
    //        if(top>bodyHeight-76){
    //            $("#drag").css({"top":bodyHeight- 38});
    //        }
    //    }
    //    move = false;
    //    seup =false;
    //})

    dZh.addEventListener("touchstart",function () {
        move = true;
        seup =true;
        _x = event.targetTouches[0].pageX - parseInt($("#dZh").css("left"));
        _y = event.targetTouches[0].pageY - parseInt($("#dZh").css("top"));
    })
    dZh.addEventListener("touchmove",function (){
        if (event.targetTouches.length == 1) {
            event.preventDefault();// 阻止浏览器默认事件，重要
            if (move) {
                var x = event.targetTouches[0].pageX - _x;//控件左上角到屏幕左上角的相对位置
                var y = event.targetTouches[0].pageY - _y;
                $("#dZh").css({"top": y, "left": x});
            }
        }

    })
    dZh.addEventListener("touchend",function (){
        if(seup){
            var left = $("#dZh").offset().left;
            var top = $("#dZh").offset().top;
            if(top<86){
                $("#dZh").css({"top": '44px'});
            }
            // if(top > 44 || top > bodyHeight -102){
                if((bodyWidth/2) < left){
                    $("#dZh").css({"left": bodyWidth-53+"px"});
                }else{
                    $("#dZh").css({"left": "15px"});
                }
            // }
            if(top>bodyHeight-76){
                $("#dZh").css({"top":bodyHeight- 38});
            }

        }
        move = false;
        seup =false;
    })

    var video = document.getElementById("video");
    video.addEventListener("touchstart",function () {
        move = true;
        seup =true;
        _x = event.targetTouches[0].pageX - parseInt($("#video").css("left"));
        _y = event.targetTouches[0].pageY - parseInt($("#video").css("top"));
    })
    video.addEventListener("touchmove",function (){
        if (event.targetTouches.length == 1) {
            event.preventDefault();// 阻止浏览器默认事件，重要
            if (move) {
                var x = event.targetTouches[0].pageX - _x;//控件左上角到屏幕左上角的相对位置
                var y = event.targetTouches[0].pageY - _y;
                $("#video").css({"top": y, "left": x});
            }
        }

    })
    video.addEventListener("touchend",function (){
        if(seup){
            var left = $("#video").offset().left;
            var top = $("#video").offset().top;
            if(top<86){
                $("#video").css({"top": '44px'});
            }
            // if(top > 44 || top > bodyHeight -102){
                if((bodyWidth/2) < left){
                    $("#video").css({"left": bodyWidth-53+"px"});
                }else{
                    $("#video").css({"left": "15px"});
                }
            // }
            if(top>bodyHeight-76){
                $("#video").css({"top":bodyHeight- 30});
            }

        }
        move = false;
        seup =false;
    })


    //追号
    $("body").on("click","#chaseNumber", function(){
        $("wrapper").on("touchmove",function(event){
            event.preventDefault();
        })

        $(".bettingWarp").show();
        $(".operateWarp").hide();
        $(".zhuiHao").show();
        $(".bettingKey").slideDown(function(){
            $('.roomNews').hide();
            $(".list-con").html("");
            $(".betList").hide();
            //if($(".subtotal").length>0){  //总金额提示
            //    $(".subtotal").remove();
            //}
            $(".lottery").hide(); //开奖结果列表
            //fnSet.scrollTop();
        })

    })
    //pkxy追号
    $("body").on("click","#chaseNumber_pkxy", function(){
        $("wrapper").on("touchmove",function(event){
            event.preventDefault();
        })

        $(".stakeWarp").hide();
        $(".betCon").css("padding-bottom",0);
        $(".betWarp").show().removeClass("betting").addClass("chasing");
        setTimeout(function () {
            $(".betCon").css("top","150px");
        },20)
        $(".betLeftNav ul li").eq(0).click();




        // $(".bettingWarp").show();
        // $(".operateWarp").hide();
        // $('.stakeWarp').hide();
        // $(".zhuiHao").hide();
        // $(".zhuiHaoList").hide();
        // $(".bettingKey-pkft").slideDown(function(){
        //     reset();
        //     $(".selecArea").addClass("chasing").removeClass("betting");
        //     $('.roomNews').hide();
        //     $(".list-con").html("");
        //     $(".betList").hide();
        //     $(".lottery").hide(); //开奖结果列表
        // })

    })

//追号翻倍
    //加的效果
    $(".add").click(function () {
        var n = $(this).prev().val();
        var num = parseInt(n) + 1;
        if (num == 0) {
            return;
        }
        $(this).prev().val(num);
    });
    //减的效果
    $(".lessB").click(function () {
        var n = $(this).next().val();
        var num = parseInt(n) - 1;
        if (num == 0) {
            return
        }
        $(this).next().val(num);
    });
    //追号生成
    $(".chase-form-right").on("click",".generate",function(){
        var money =$(".money-text").val();
        //var sel = $(".sel option:selected").val();
        if($("#myBtn").hasClass("bettingKey")){
            var sel =$(".play ul li .pit span").text();
        }
        if($(".betWarp").hasClass("chasing")){
            if($("#betTitle").text() =="猜冠亚"){
                var sel = "冠亚_"+ $(".betPlay.pit").eq(0).text() +"_"+ $(".betPlay.pit").eq(1).text();
            }else{
                var sel = $(".betPlay.pit").attr("data-title");
            }

            // var sel =$(".chase-wanf").text();
        }

        if(sel ==""){
            fnSet.alert("请选择玩法");
            return false;
        }
        if(money ==""){
            fnSet.alert("请填写金额");
            return false;
        }
        if(!/^[1-9]\d*$/.test(money)){
            fnSet.alert("金额必须为大于0的整数");
            return false;
        }
        var zhuiQs =$("#zhuiQs").val();
        var zhuiBs =$("#zhuiBs").val();
        var dqQH2 =$("#issue").attr("data-issue");
        var dqQH =$("#issue").text();
        var morenBs =1;
        var tabTr="";
        if(parseInt(zhuiQs) > 100 ) {
            fnSet.alert("追号一次最多100期");
            return false;
        }
        if(!/^[1-9]\d*$/.test(zhuiQs)){
            fnSet.alert("期数必须为正整数");
            return false;
        }
        if(zhuiQs =="" || zhuiQs =="0"){
            fnSet.alert("请输入期数");
            return false;
        }
        if(zhuiBs =="" || zhuiBs =="0"){
            fnSet.alert("请输入倍数");
            return false;
        }
        if(!/^[1-9]\d*$/.test(zhuiBs)){
            fnSet.alert("倍数必须为正整数");
            return false;
        }
        var subtotal = 0;
        var bet_count = 0;
        for(var i=0; i<zhuiQs; i++){
            if(i==0){
                var room_check  = checkMoney(dqQH,sel,money);
                var group_check = checkGroup(dqQH,money,2);
                // var reverse_check = checkReverse(dqQH,sel,[]);
                if(group_check>0) {
                    var group_name = "";
                    if($("#user_group_name").val()!=undefined) group_name = $("#user_group_name").val();
                    if(group_check==1) {
                        var group_upper = 1000000;
                        if($("#user_group_upper").val()!=undefined) group_upper = parseFloat($("#user_group_upper").val());
                        fnSet.alert("您的投注额累计超过，您所属会员组("+group_name+")上限:"+group_upper);
                        return;
                    }else if(group_check==2) {
                        var group_lower = 0;
                        if($("#user_group_lower").val()!=undefined) group_lower = parseFloat($("#user_group_lower").val());
                        fnSet.alert("您的投注额低于，您所属会员组("+group_name+")下限:"+group_lower);
                        return;
                    }
                } else if(!room_check) {
                    return;
                    // }else if(!reverse_check){
                    //     return;
                } else{
                    if(dqQH2 != dqQH){
                        //dqQH ="0"+dqQH;
                        tabTr += '<tr data-name="'+sel+'"><td width="33%"><em data-issue="'+dqQH2+'">' + (dqQH2+'').substr(4) + '</em></td><td width="33%"><em  class="single-money">' + money * morenBs + '</em>元</td><td width="33%"><em>' + morenBs + '</em>倍</td></tr>';
                    }else{
                        tabTr += '<tr data-name="'+sel+'"><td width="33%"><em data-issue="'+dqQH2+'">' + dqQH2 + '</em></td><td width="33%"><em  class="single-money">' + money * morenBs + '</em>元</td><td width="33%"><em>' + morenBs + '</em>倍</td></tr>';

                    }
                    subtotal += parseFloat(money);
                    bet_count++;
                }
            } else{
                morenBs = morenBs*zhuiBs;
                var room_check  = checkMoney(dqQH,sel,money * morenBs);
                var group_check = checkGroup(dqQH,money * morenBs,2);
                // var reverse_check = checkReverse(dqQH,sel,[]);
                if(group_check>0) {
                    var group_name = "";
                    if($("#user_group_name").val()!=undefined) group_name = $("#user_group_name").val();
                    if(group_check==1) {
                        var group_upper = 1000000;
                        if($("#user_group_upper").val()!=undefined) group_upper = parseFloat($("#user_group_upper").val());
                        fnSet.alert("您的投注额累计超过，您所属会员组("+group_name+")上限:"+group_upper);
                        return;
                    }else if(group_check==2) {
                        var group_lower = 0;
                        if($("#user_group_lower").val()!=undefined) group_lower = parseFloat($("#user_group_lower").val());
                        fnSet.alert("您的投注额低于，您所属会员组("+group_name+")下限:"+group_lower);
                        return;
                    }
                } else if(!room_check) {
                    return;
                    // }else if(!reverse_check){
                    //     return;
                } else {
                    if(dqQH2 != dqQH){
                        //dqQH ="0"+dqQH;
                        tabTr += '<tr data-name="'+sel+'"><td width="33%"><em data-issue="'+dqQH2+'">' + (dqQH2+'').substr(4) + '</em></td><td width="33%"><em  class="single-money">' + money * morenBs + '</em>元</td><td width="33%"><em>' + morenBs + '</em>倍</td></tr>';
                    }else{
                        tabTr += '<tr data-name="'+sel+'"><td width="33%"><em data-issue="'+dqQH2+'">' + dqQH2 + '</em></td><td width="33%"><em  class="single-money">' + money * morenBs + '</em>元</td><td width="33%"><em>' + morenBs + '</em>倍</td></tr>';

                    }
                    subtotal += parseFloat(money * morenBs);
                    bet_count++;
                }
            }
            dqQH++;
            dqQH2++;
        }
        if($(".subtotal").length>0){
            $(".subtotal").remove();
        }
        $(".listQs").eq(0).append("<div class='subtotal' style='text-align: center;display: none;' id='subtotal' all='"+subtotal+"' count='"+bet_count+"' >总金额:<em>"+subtotal+"</em>元</div>");
        $("#zhuiHList").html(tabTr);

        $(".zhuiHaoList").show();
        fnSet.scrollTop();
    })
    //删除追号列表中列
    $('#zhuiHList').on("click touch",".close",function(){
        var index =$(this).parents("tr").index();
        $(this).parents("tr").remove();
        $("#subtotal").attr("count", $("#subtotal").attr("count")-1);
        $("#subtotal").attr("all", $("#subtotal").attr("all")-$(this).parents("tr").find(".single-money").text());
        $("#subtotal em").html($("#subtotal").attr("all"));
        $("#zhuiQs").val($("#subtotal").attr("count"));

        //aJine.splice(index,1);
        //aWanf.splice(index,1);
        //fnSet.scrollTop();

        if( $("#zhuiHList tr").length < 1){
            $(".zhuiHaoList").hide();
        }
    })
    //确认追号
    $("body").on("click","#querenTz", function(){
        var all = 0;
        var count = 0;
        var winStop=$('#if_win_stop').attr('checked')?1:0;
        if($("#subtotal")!=undefined) all = $("#subtotal").attr("all");
        if($("#subtotal")!=undefined) count = $("#subtotal").attr("count");
        if(count>0){
            $.confirm("本次追号投注:"+count+"期,共:"+all+"元宝", function () {
                var bodyDome = $("#zhuiHList").find("tr");
                var money = $(".roomHead1 .icoAcer").text();
                var zMoney = 0;
                var betData = [];
                var sel =$("#zhuiHList tr").eq(0).attr("data-name");
                //if($("#myBtn").hasClass("bettingKey")){
                //    var sel =$(".play ul li .pit span").text();
                //}
                //if($("#myBtn").hasClass("bettingKey-pkft")){
                //    var sel =$(".chase-wanf").text();
                //}
                for(var a=0;a<bodyDome.length;a++) {
                    var issue = $(bodyDome[a]).find("td").eq(0).find("em").eq(0).attr("data-issue"),
                        bet_money = $(bodyDome[a]).find("td").eq(1).find("em").html(),
                        //way = $(".sel option:selected").val();
                        way = sel;
                    var obj = {
                        'qihao':issue,
                        'money':bet_money,
                        'way':way,
                        'multiple':$(bodyDome[a]).find("td").eq(2).find("em").html(),
                    }
                    zMoney +=parseInt(obj.money);
                    betData.push(obj);
                }
                for(i in betData) {
                    if(betData[i].way==""||betData[i].way==undefined) {
                        fnSet.alert("请选择玩法");
                        return;
                    }
                }
                if(betData.length <= 0 || zMoney == 0){
                    fnSet.alert("请生成投注信息");
                }else if(money<zMoney){
                    fnSet.alert("余额不足");
                }else{
                    if($(".subtotal").length>0){
                        $(".subtotal").remove();
                    }
                    // if(fag1){
                    var param = {
                        "commandid": "3019",
                        "nickname": userinfo.nickname,
                        //"uid": userinfo.userid,
                        "data":betData,
                        "win_stop":winStop,
                        "avatar":userinfo.head_url
                    };
                    //console.log(param);
                    wsSendMsg(param);
                    //

                    //投注后清空
                    fnSet.scrollTop();
                    $(".chaseWarp").click();
                    $(".betWarp").click();
                    $(".bettingWarp").click();
                }
            },function () {});
        }else{
            fnSet.alert("请选择投注内容")
        }

    });
    //追号图标
    $("body").on("click",".ioc_zhuihao",function(){
        var data = {
            'token':userinfo.token,
            'room_id':userinfo.room_id
        }
        $.ajax({
            url:"?m=api&c=order&a=getZhuiHaoInfo",
            type:'post',
            dataType:'json',
            data:data,
            success:function(data) {
                var zhui_details_con = $(".zhui_details_con");
                zhui_details_con.empty();
                var html = '';
                if(data.code == 0){

                    for(var x=0;x<data.list.length;x++) {
                        html += '<dl><dt><span>'+data.list[x]['time']+'</span><span><button class="chedan" onclick="chedan(\''+data.list[x]['number']+'\',this)">撤单</button></span></dt><dd style="text-align: center"><span style="width: 100%; text-align: center;"><em>玩法：</em><em style="color: #2aa7f6">'+data.list[x]['way']+'</em></span></dd>';
                        for(var b=0;b<data.list[x]['data'].length;b++){
                            var color = "";
                            if(data.list[x]['data'][b]['award_state'] == 0){
                                var award_state = "待开奖";
                            }else if(data.list[x]['data'][b]['award_state'] == 1){
                                var award_state = "未中奖";
                                color = "#EE6A50";
                            }else if(data.list[x]['data'][b]['award_state'] == 2){
                                var award_state = "中奖";
                                color = "#34b86c";
                            }
                            var issue ='';
                            if(userinfo.lottery_type == 4 ||userinfo.lottery_type == 9 || userinfo.lottery_type == 5 || userinfo.lottery_type == 6 || userinfo.lottery_type == 11){
                                issue =data.list[x]['data'][b]['issue'].substr(4);
                            }else{
                                issue =data.list[x]['data'][b]['issue']
                            }
                            html += '<dd><span>第'+issue+'期</span><span>'+data.list[x]['data'][b]['money']+'元</span><span>'+data.list[x]['data'][b]['multiple']+'倍</span><span style="color:'+ color+';">'+award_state+'</span></dd>';
                        }
                        html += '</dl>';
                    }

                    html += '</dl>';
                    zhui_details_con.append(html);
                    $(".zhui_details").show();

                } else {
                    //html += "<dd>"+data.msg+"</dd>";
                    layer.open({
                        content: data.msg,
                        skin: 'msg',
                        time: 2 //2秒后自动关闭
                    });
                    $(".ioc_zhuihao").hide();
                }

            }
        })

    });

    //追号详情关闭
    $("body").on("click",".zhui_details",function(){
        $(this).hide();
        return false;
    })
    //追号详情弹出层上面的X
    $("body").on("click",".zhui_close",function(){
        $(".zhui_details").hide();
        return false;
    });
    $(".zhui_details_w .zhui_con").on("click",function(e){
        return false;
        // e.stopPropagation();
    })

    function checkMoney(issue,sel,money) {
        var limit = 1000000000000;
        var bet_total = 0;
        var bet_all = 0;
        $(".issue_" + issue).each(function () {
            switch (sel) {
                case '大':
                case '小':
                case '单':
                case '双':
                    if ($(this).attr("name") == "大" || $(this).attr("name") == "小" || $(this).attr("name") == "单" || $(this).attr("name") == "双") {
                        if ($(this).val() != undefined && $(this).val() != "") bet_total += parseFloat($(this).val());
                    }
                    break;
                case '极大':
                case '极小':
                    if ($(this).attr("name") == "极大" || $(this).attr("name") == "极小") {
                        if ($(this).val() != undefined && $(this).val() != "") bet_total += parseFloat($(this).val());
                    }
                    break;
                case '大单':
                case '小单':
                case '大双':
                case '小双':
                    if ($(this).attr("name") == "大单" || $(this).attr("name") == "小单" || $(this).attr("name") == "大双" || $(this).attr("name") == "小双") {
                        if ($(this).val() != undefined && $(this).val() != "") bet_total += parseFloat($(this).val());
                    }
                    break;
                case '红':
                    if ($(this).attr("name") == "红") {
                        if ($(this).val() != undefined && $(this).val() != "") bet_total += parseFloat($(this).val());
                    }
                    break;
                case '绿':
                    if ($(this).attr("name") == "绿") {
                        if ($(this).val() != undefined && $(this).val() != "") bet_total += parseFloat($(this).val());
                    }
                    break;
                case '蓝':
                    if ($(this).attr("name") == "蓝") {
                        if ($(this).val() != undefined && $(this).val() != "") bet_total += parseFloat($(this).val());
                    }
                    break;
                case '豹子':
                    if ($(this).attr("name") == "豹子") {
                        if ($(this).val() != undefined && $(this).val() != "") bet_total += parseFloat($(this).val());
                    }
                    break;
                case '正顺':
                    if ($(this).attr("name") == "正顺") {
                        if ($(this).val() != undefined && $(this).val() != "") bet_total += parseFloat($(this).val());
                    }
                    break;
                case '倒顺':
                    if ($(this).attr("name") == "倒顺") {
                        if ($(this).val() != undefined && $(this).val() != "") bet_total += parseFloat($(this).val());
                    }
                    break;
                case '半顺':
                    if ($(this).attr("name") == "半顺") {
                        if ($(this).val() != undefined && $(this).val() != "") bet_total += parseFloat($(this).val());
                    }
                    break;
                case '乱顺':
                    if ($(this).attr("name") == "乱顺") {
                        if ($(this).val() != undefined && $(this).val() != "") bet_total += parseFloat($(this).val());
                    }
                    break;
                case '对子':
                    if ($(this).attr("name") == "对子") {
                        if ($(this).val() != undefined && $(this).val() != "") bet_total += parseFloat($(this).val());
                    }
                    break;
                default:
                    if ($(this).attr("name") == sel) {
                        if ($(this).val() != undefined && $(this).val() != "") bet_total += parseFloat($(this).val());
                    }
                    break;
            }
            bet_all += parseFloat($(this).val());
        });
        bet_all += parseFloat(money);
        money = bet_total + parseFloat(money);
        if (isNaN(sel)) {
            switch (sel) {
                case '大':
                    if ($("#size_ds").val() != undefined && $("#size_ds").val() != "0" && $("#size_ds").val() != "") limit = parseFloat($("#size_ds").val());
                    break;
                case '小':
                    if ($("#size_ds").val() != undefined && $("#size_ds").val() != "0" && $("#size_ds").val() != "") limit = parseFloat($("#size_ds").val());
                    break;
                case '单':
                    if ($("#size_ds").val() != undefined && $("#size_ds").val() != "0" && $("#size_ds").val() != "") limit = parseFloat($("#size_ds").val());
                    break;
                case '双':
                    if ($("#size_ds").val() != undefined && $("#size_ds").val() != "0" && $("#size_ds").val() != "") limit = parseFloat($("#size_ds").val());
                    break;
                case '极大':
                    if ($("#minimax").val() != undefined && $("#minimax").val() != "0" && $("#minimax").val() != "") limit = parseFloat($("#minimax").val());
                    break;
                case '大单':
                    if ($("#parts").val() != undefined && $("#parts").val() != "0" && $("#parts").val() != "") limit = parseFloat($("#parts").val());
                    break;
                case '小单':
                    if ($("#parts").val() != undefined && $("#parts").val() != "0" && $("#parts").val() != "") limit = parseFloat($("#parts").val());
                    break;
                case '大双':
                    if ($("#parts").val() != undefined && $("#parts").val() != "0" && $("#parts").val() != "") limit = parseFloat($("#parts").val());
                    break;
                case '小双':
                    if ($("#parts").val() != undefined && $("#parts").val() != "0" && $("#parts").val() != "") limit = parseFloat($("#parts").val());
                    break;
                case '极小':
                    if ($("#minimax").val() != undefined && $("#minimax").val() != "0" && $("#minimax").val() != "") limit = parseFloat($("#minimax").val());
                    break;
                case '红':
                    if ($("#red").val() != undefined && $("#red").val() != "0" && $("#red").val() != "") limit = parseFloat($("#red").val());
                    break;
                case '绿':
                    if ($("#green").val() != undefined && $("#green").val() != "0" && $("#green").val() != "") limit = parseFloat($("#green").val());
                    break;
                case '蓝':
                    if ($("#blue").val() != undefined && $("#blue").val() != "0" && $("#blue").val() != "") limit = parseFloat($("#blue").val());
                    break;
                case '豹子':
                    if ($("#leo").val() != undefined && $("#leo").val() != "0" && $("#leo").val() != "") limit = parseFloat($("#leo").val());
                    break;
                case '正顺':
                    if ($("#zhengshun").val() != undefined && $("#zhengshun").val() != "0" && $("#zhengshun").val() != "") limit = parseFloat($("#zhengshun").val());
                    break;
                case '倒顺':
                    if ($("#daoshun").val() != undefined && $("#daoshun").val() != "0" && $("#daoshun").val() != "") limit = parseFloat($("#daoshun").val());
                    break;
                case '半顺':
                    if ($("#banshun").val() != undefined && $("#banshun").val() != "0" && $("#banshun").val() != "") limit = parseFloat($("#banshun").val());
                    break;
                case '乱顺':
                    if ($("#luanshun").val() != undefined && $("#luanshun").val() != "0" && $("#luanshun").val() != "") limit = parseFloat($("#luanshun").val());
                    break;
                case '对子':
                    if ($("#pair").val() != undefined && $("#pair").val() != "0" && $("#pair").val() != "") limit = parseFloat($("#pair").val());
                    break;
            }
        } else {
            var arr = null;
            if ($("#single_digit").val() != undefined && $("#single_digit").val() != "0" && $("#single_digit").val() != "") {
                arr = $("#single_digit").val().split(",");
                if(arr[sel]!="0"&&arr.length>10) limit = arr[sel];
                else if(arr[parseFloat(sel)-1]!="0"&&arr.length==10) limit = arr[parseFloat(sel)-1];
            }
        }
        var all = 100000000;
        var all_limit = true;
        if ($("#general_note").val() != undefined && $("#general_note").val() != "0" && $("#general_note").val() != "") {
            all = parseFloat($("#general_note").val());
        }else{
            all_limit = false;
        }
        var lower = 0;
        var lower_limit = true;
        if ($("#lower").val() != undefined && $("#lower").val() != "0" && $("#lower").val() != "") {
            lower = parseFloat($("#lower").val());
        }else {
            lower_limit = false;
        }
        if (money > limit) {
            switch (sel) {
                case '大':
                case '小':
                case '单':
                case '双':
                    fnSet.alert("超过了房间玩法:(大小单双)的限额:"+limit);
                    break;
                case '极大':
                case '极小':
                    fnSet.alert("超过了房间玩法:(极值)的限额:"+limit);
                    break;
                case '大单':
                case '小单':
                case '大双':
                case '小双':
                    fnSet.alert("超过了房间玩法:(组合)的限额:"+limit);
                    break;
                case '红':
                case '绿':
                case '蓝':
                case '豹子':
                case '正顺':
                case '倒顺':
                case '半顺':
                case '乱顺':
                case '对子':
                default:
                    fnSet.alert("超过了房间玩法:("+sel+")的限额:"+limit);
                    break;
            }
            return false;
        }
        else if(bet_all>all) {
            if(all_limit) {
                fnSet.alert("超过了房间本期总投注限额:"+all);
                return false;
            }
        }
        else if(lower>bet_all) {
            if(lower_limit) {
                fnSet.alert("低于房间最低投注限额:" + lower);
                return false;
            }else {
                return true;
            }
        }
        else return true;
    }

    function checkGroup(issue,money) {
        var bet_total = 0;
        $(".issue_"+issue).each(function () {
            if($(this).val()!=undefined&&$(this).val()!="") bet_total+=parseFloat($(this).val());
        });
        bet_total += parseFloat(money);
        var group_upper = 10000000000000;
        var group_lower = 0;
        var up_limite =true; //后台有做上限
        var low_limite =true; //后台有做下限
        //能取到值说明后台有做上下限制
        if($("#user_group_upper").val()!=undefined && $("#user_group_upper").val() != "0" && $("#user_group_upper").val() != "") group_upper = parseFloat($("#user_group_upper").val());
        else up_limite = false; //后台没做上限
        if($("#user_group_lower").val()!=undefined && $("#user_group_lower").val() != "0" && $("#user_group_lower").val() != "") group_lower = parseFloat($("#user_group_lower").val());
        else low_limite = false; //后台没做上限

        if( bet_total > group_upper) {
            if(up_limite) return 1;
            else return 0;
        }else if( bet_total < group_lower){
            if(low_limite) return 2;
            else return 0;
        }else return 0;
    }

    // function checkReverse(issue,sel,arr) {
    //     var way_list = [];
    //     $(".issue_"+issue).each(function () {
    //         if($(this).attr("name") != undefined) {
    //             way_list.push($(this).attr("name"));
    //         }
    //     });
    //     way_list.push(sel);
    //     if(arr.length>0) way_list = way_list.concat(arr);
    //     if( $("#reverse_2").val()=="1" && way_list.indexOf("大双")>=0 && way_list.indexOf("小双")>=0 && way_list.indexOf("大单")>=0 && way_list.indexOf("小单")>=0) {
    //         fnSet.alert("第"+issue+"期，不能投注房间玩法:(组合)的所有玩法");
    //         return false;
    //     }
    //
    //     if( $("#reverse_1").val()=="1" && way_list.indexOf("单")>=0 && way_list.indexOf("双")>=0) {
    //         fnSet.alert("第"+issue+"期，不能投注房间玩法:(单双)的所有玩法");
    //         return false;
    //     }
    //
    //     if( $("#reverse_0").val()=="1" && way_list.indexOf("大")>=0 && way_list.indexOf("小")>=0) {
    //         fnSet.alert("第"+issue+"期，不能投注房间玩法:(大小)的所有玩法");
    //         return false;
    //     }
    //
    //     return true;
    // }
})
function chedan(number,obj) {
    var icoTime = $(".icoTime").text();
    var type = 0;
    if(icoTime == "已封盘" || icoTime == "开奖中"){
        // type = 1
        //fnSet.alert("不能撤单");
    }
    var param = {
        "commandid": "3016",
        "uid": userinfo.userid,
        "order_no": number
    };
    wsSendMsg(param);
}
/*
 * 修改人: CLoud
 * 获取该房间所有限额
 * 用途:限制生成追号
 * params id int 房间号
 */
$(function(){
    limitFun();
    //处理幸运飞艇的期号前4位
    if(userinfo.lottery_type == 4 ||userinfo.lottery_type == 9 || userinfo.lottery_type == 5 || userinfo.lottery_type == 6 || userinfo.lottery_type == 11 || userinfo.lottery_type == 13){
        if(userinfo.lottery_type == 11){
           var num = 5
        }else{
            var num = 4
        }
        $("#issue2").text($("#issue2").text().substr(num));
        var lottery =$(".lottery dl dd ul");
        for(var i=0; i < lottery.length; i++){
            lottery.eq(i).children("li").find("em").text(lottery.eq(i).children("li").find("em").text().substr(num));
        }
    }
});
function limitFun(){
    $.ajax({
        url:"?m=api&c=dataCenter&a=getRoomLimite&id="+userinfo.room_id,
        type:'post',
        dataType:'json',
        success:function(data) {
            if(data.status.code==10000) {
                for (name in data.data) {
                    $("body").eq(0).prepend("<input type='hidden' name='" + name + "' value='" + data.data[name] + "' id='"+name+"'>");
                }
            }
        },
        error:function () {
            $.ajax({
                url:"?m=api&c=dataCenter&a=getRoomLimite&id="+userinfo.room_id,
                type:'post',
                dataType:'json',
                success:function(data) {
                    if(data.status.code==10000) {
                        for (name in data.data) {
                            $("body").eq(0).prepend("<input type='hidden' name='" + name + "' value='" + data.data[name] + "' id='"+name+"'>");
                        }
                    }
                }
            });
        }
    });

    $.ajax({
        url:"?m=api&c=dataCenter&a=getAllBet&user_id="+userinfo.userid+"&room_id="+userinfo.room_id,
        type:'post',
        dataType:'json',
        success:function(data) {
            if(data.status.code==10000) {
                for (name in data.data) {
                    //如果有当期有投注记录
                    $("body").eq(0).prepend("<input type='hidden' name='"+data.data[name].way+"' class='issue_"+data.data[name].issue+"' value='" + data.data[name].money + "'>");
                }
            }
        },
        error:function () {
            $.ajax({
                url:"?m=api&c=dataCenter&a=getAllBet&user_id="+userinfo.userid+"&room_id="+userinfo.room_id,
                type:'post',
                dataType:'json',
                success:function(data) {
                    if(data.status.code==10000) {
                        for (name in data.data) {
                            $("body").eq(0).prepend("<input type='hidden' name='"+data.data[name].way+"' class='issue_"+data.data[name].issue+"' value='" + data.data[name].money + "'>");
                        }
                    }
                }
            });
        }
    });

    $.ajax({
        url:"?m=api&c=dataCenter&a=getReverse",
        type:'post',
        dataType:'json',
        success:function(data) {
            if(data.status.code==10000) {
                for (name in data.data) {
                    $("body").eq(0).prepend("<input type='hidden' name='"+data.data[name].name+"' id='reverse_"+name+"' value='" + data.data[name].state + "'>");
                }
            }
        },
        error:function () {
            $.ajax({
                url:"?m=api&c=dataCenter&a=getReverse",
                type:'post',
                dataType:'json',
                success:function(data) {
                    if(data.status.code==10000) {
                        for (name in data.data) {
                            $("body").eq(0).prepend("<input type='hidden' name='"+data.data[name].name+"' id='reverse_"+name+"' value='" + data.data[name].state + "'>");
                        }
                    }
                }
            });
        }
    });
}


function getOdds() {
    // if(userinfo.lottery_type !=1 || userinfo.lottery_type != 3){
        $.ajax({
            url:"?m=web&c=odds&a=getOdds",
            type:'post',
            dataType:'json',
            data:{"room_id":userinfo.room_id},
            success:function(data) {
                if(data.code==0) {
                    var odds = data.data
                    if(userinfo.lottery_type ==5 || userinfo.lottery_type == 6 || userinfo.lottery_type == 11){
                        fnSet.oddsUpdateSSC(odds);
                    }else if(userinfo.lottery_type ==2 ||userinfo.lottery_type ==9 || userinfo.lottery_type == 4){
                        fnSet.oddsUpdate幸运飞艇(odds);
                        fnSet.oddsUpdate幸运飞艇_2(odds);
                        fnSet.oddsUpdate幸运飞艇_3(odds);
                    }else if(userinfo.lottery_type == 1 || userinfo.lottery_type == 3){
                        fnSet.oddsUpdate(odds);
                    }
                }
            }
        });
    // }

}
