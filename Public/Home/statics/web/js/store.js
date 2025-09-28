var flagFst = true;
var nowIssue;
var recentlyIssue;
var endState = 0;
var offSet = 0; //给3017端口发送的参数
var totalZu = 0;//3018端口返回的总条数
var store = {
	userInfo: {},
	msgList: [],
	listener: null,
	keybordDatas: {},
	mainPage: {
		lotteryNo: "",
		lotteryResult: "",
		countDown: 0
	},
	cashDetail: {},// 提现记录详情
	bettingDetail: {},// 投注记录详情
	transactionDetail: {},// 交易记录详情
	rechargeDetail: {},// 充值记录详情
	memberDetail: {},// 会员报表详情
	teamDetail: {},// 团队报表详情
        issue:0,

	
	processMsg: function(msg) {
        // if(userinfo.lottery_type==12){
        //     return
        // }
		//console.log("store.js的processMsg, msg:" + JSON.stringify(msg));
		var commandId = msg.commandid;
		if(commandId == "3001"){
            if(msg.stopOrSell==2){
                //停售
                $(".roomLi p").eq(0).html('');
                $(".roomLi p").eq(1).html('已停售');
                return
            }
		    if(msg.issue > 1){
                if(userinfo.lottery_type == 4 ||userinfo.lottery_type == 9 || userinfo.lottery_type == 5 || userinfo.lottery_type == 6 || userinfo.lottery_type == 8 || userinfo.lottery_type == 11 || userinfo.lottery_type == 13){
                    //幸运飞艇去掉前面4位期号
                    if(userinfo.lottery_type == 11){
                        var num = 5
                    }else{
                        var num = 4
                    }
                    $("#issue").text(msg.issue.substr(num));
                    $(".issue_video").text(msg.issue.substr(num));
                }else{
                    $("#issue").text(msg.issue);
                    $(".issue_video").text(msg.issue);
                }
                $("#issue").attr("data-issue",msg.issue);

                //判断当前期号必须大于开奖结果最近一期的期号
                nowIssue = msg.issue;
                if (nowIssue <= recentlyIssue){
                //if (nowIssue > recentlyIssue){
                    $(".popup").show();
                    $(".popup").html('<div class="config-issue"><h3>提示</h3><div class="configCon">当前房间期号有误!</div><div class="cigBtn" style="margin-top: 30px;"><button class="" id="ncquxiao">确定</button></div></div>')
                    return false;
                }

            }

            //清除上一期的投注记录
            if (store.issue != msg.issue) {
                $(".betTixing").hide();
                $(".quxiao ul").html('');
                fnSet.scrollTop();
            }
                     
            store.issue = msg.issue; //赋值期号
//             if (flagFst) {
//                 //获取房间内投注记录
//                 $.ajax({
//                     url: "?m=web&c=order&a=nowBet",
//                     type: "post",
//                     data: {issue: msg.issue, room_no: userinfo.room_id},
//                     dataType: "json",
//                     success: function(data) {
//                         var tzRecord = '';
//                         //console.log(data);
//                         if (data['list'].length > 0) {
//                             $(".betTixing span em").html('本期已下'+data['list'].length+'注');
//                             $(".betTixing").show();
//                             for(var i=0; i<data['list'].length; i++){
//                                 tzRecord += '<li><em class="close" order-no="'+ data['list'][i].order_no +'"></em><em class="issue">'+data['list'][i]
// .issue+'</em><label>'+ data['list'][i].way +'</label> <span>'+ data['list'][i].money +'<i class="icoAcer"></i></span></li>'; //添加投注记录
//
//                                 //tzRecord += '<li><label>投注类型：'+ data['list'][i].way +'</label> <label>金额：'+ data['list'][i].money +'<i class="icoAcer"></i></label><em class="close" order-no="'+ data['list'][i].order_no +'"></em></li>'; //添加投注记录
//                             }
//                             //$(".quxiao ul").html(tzRecord);
//                         }
//                     }
//                 });
//
//                 flagFst = false;
//             }
                        
            fnSet.countDown(msg);
		}else if(commandId == 3024 && userinfo.lottery_type == '12'){
            //更新世界杯赔率
            fnSet.update324()
            $(".rhFenShu").html(msg.against.match_score)
        }else if(commandId == "3004"){
            //机器人或者用户发言、欢迎
            fnSet.update34(msg);
		}else if(commandId == "3005"){
            //弹框
            fnSet.update35(msg);
		}else if(commandId == "3007"){
            //用户投注记录显示
            fnSet.update37(msg);

		}else if(commandId == "3014"){
		    if(msg.type && msg.type == 1){
                fnSet.alert(msg.content,function(){
                    window.location.href ="/?m=web&c=lobby&a=index";
                });
                return;
            }
            //踢人
            //debugger;
            fnSet.alert(msg.content,function(){
                window.location.href ="/?m=web&c=user&a=login";
            });

        }else if(commandId == "3008") {
            //3008服务端向客户端推送赔率配置修改
            //debugger;
            //客户端收到这个推送的消息后，再次向服务端请求赔率配置信息

            // if(userinfo.lottery_type ==5 || userinfo.lottery_type == 6){
            //     fnSet.oddsUpdateSSC(msg);
            // }else{
            //     fnSet.oddsUpdate(msg);
            // }
            //
            // if(msg.odds_explain){
            //     newsExplain = msg.odds_explain.replace(/\n/g,"<br/>");
            //     odds_explain = newsExplain;
            // }
        }else if(commandId == "3024") {
            getOdds();
        }else if(commandId == "3010"){
            //刷新金额
		 	$(".roomHead1 span.icoAcer").text(msg.money);
		}else if(commandId == "3011"){
            //公布开奖结果
            var roomGroup;
            if(userinfo.lottery_type==12){
                //世界杯
                roomGroup='<div class="userBetting2"><ul  class="system">'
                roomGroup +='<li style="font-size: 10px;text-align: center;padding: 0 5px;"><p style="color:#2aa7f6 "><p>开奖时间：'+msg.open_time+'</p><p>开奖结果：'+msg.result+'</p>'; 
                roomGroup += '</li></ul></div>';
			    $(".room .roomContent").append(roomGroup);                
                return
            }
             msg.statistics =msg.statistics.replace(/\\n|\\r/g, "\n");
            if(userinfo.lottery_type ==4 ||userinfo.lottery_type == 9 || userinfo.lottery_type ==5 || userinfo.lottery_type == 6|| userinfo.lottery_type == 10 || userinfo.lottery_type == 11 || userinfo.lottery_type == 13){
                if(userinfo.lottery_type == 11){
                    var num = 5
                }else{
                    var num = 4
                }
                var dataIssue = msg.issue.substr(num);
            }else{
                var dataIssue = msg.issue
            }
             roomGroup ='<div class="userBetting2"><ul  class="system">'
             if(userinfo.lottery_type == 10){
                roomGroup +='<li style="font-size: 10px;text-align: left;padding: 0 5px;"><p style="color:#2aa7f6 ">期号：'+dataIssue+'</p><p>开奖时间：'+msg.open_time+'</p><p>开奖号码：'+msg.result+'</p><p>开奖结果：'+msg.sum_result_str+'</p>'; 
             }else{
                roomGroup +='<li style="font-size: 10px;text-align: left;padding: 0 5px;"><p style="color:#2aa7f6 ">期号：'+dataIssue+'</p><p>开奖时间：'+msg.open_time+'</p><p>开奖结果：'+msg.result+'</p>';
            }
            if (msg.statistics != '') {
                roomGroup += '<p>统计：</p>';
            }
            roomGroup += '<pre style="font-size: 10px;">'+msg.statistics+'</pre></li></ul></div>';
			$(".room .roomContent").append(roomGroup);

			if(userinfo.lottery_type ==2 ||userinfo.lottery_type == 9 || userinfo.lottery_type ==4 || userinfo.lottery_type ==5 || userinfo.lottery_type == 6 || userinfo.lottery_type == 11 || userinfo.lottery_type == 13){  //北京PK10和幸运飞艇
			    var result =msg.result.split(",");
			    // if(userinfo.lottery_type ==4 || userinfo.lottery_type ==5 || userinfo.lottery_type == 6){
			        var html = '第<label id="issue2">'+dataIssue+'</label>期 '
                    var html2 ='<ul><li class="colorjieguo_down">第<em>'+dataIssue+'</em>期 '; //幸运飞艇更新开奖结果记录
                // }else{
                //     var html = '第<label id="issue2">'+msg.issue+'</label>期 '
                //     var html2 ='<ul><li class="colorjieguo_down">第<em>'+msg.issue+'</em>期 '; //北京Pk更新开奖结果记录
                // }
                    //'<li>'+msg.open_time+'</li>' +
                if(userinfo.lottery_type ==5 || userinfo.lottery_type == 6 || userinfo.lottery_type == 11){
                    for(var i=0; i<result.length; i++ ){
                        html+='<i class="ssc_jieguo">'+result[i]+'</i>';
                        html2 +='<i class="ssc_jieguo">'+result[i]+'</i>'
                    }
                    html2+='<label style="color: #2aa7f6">('+msg.sum_result_str+')</label></li></ul>';
                    html += '<label  style="color: #2aa7f6">('+msg.sum_result_str+')</label>';
                }else{
                    for(var i=0; i<result.length; i++ ){
                        html+='<i class="colorjieguo color'+result[i]+'">'+result[i]+'</i>';
                        html2 +='<i class="colorjieguo color'+result[i]+'">'+result[i]+'</i>'
                    }
                }



                $(".roomHead2 span").html(html);
                $(".lottery dd").prepend(html2);
            }else if(userinfo.lottery_type == 8||userinfo.lotteryType == 7){//六合彩
                $('.issueTitle .issueStyle').html(msg.issue.slice(4));
                var html = '';
                var result1 = msg.result.split(",");
                var lastStr = result1[result1.length-1].split('+');
                result1.pop();
                result1 = result1.concat(lastStr);
                var result2 = msg.sum_result_str.split(",");
                //红蓝绿波
                var redBox = [1,2,7,8,12,13,18,19,23,24,29,30,34,35,40,45,46];
                var blueBox = [3,4,9,10,14,15,20,25,26,31,36,37,41,42,47,48];
                var greenBox = [5,6,11,16,17,21,22,27,28,32,33,38,39,43,44,49];
                for(var i=0;i<(result1.length)-1;i++){
                    for(var j=0;j<redBox.length;j++){
                        if(redBox[j]==result1[result1.length-1]){
                            $('.issueTitle .lotRstEnd').html('<div><div class="redBox1">'+result1[result1.length-1]+'</div><div>'+result2[result1.length-1]+'</div></div>');
                        }
                        if(result1[i]==redBox[j]){
                            html+='<div><div class="redBox">'+result1[i]+'</div><div>'+result2[i]+'</div></div>';
                        }
                    }
                    for(var g=0;g<blueBox.length;g++){
                        if(blueBox[g]==result1[result1.length-1]){
                            $('.issueTitle .lotRstEnd').html('<div><div class="blueBox1">'+result1[result1.length-1]+'</div><div>'+result2[result1.length-1]+'</div></div>');
                        }
                        if(result1[i]==blueBox[g]){
                            html+='<div><div class="blueBox">'+result1[i]+'</div><div>'+result2[i]+'</div></div>';
                        }
                    }
                    for(var k=0;k<greenBox.length;k++){
                        if(greenBox[k]==result1[result1.length-1]){
                            $('.issueTitle .lotRstEnd').html('<div><div class="greenBox1">'+result1[result1.length-1]+'</div><div>'+result2[result1.length-1]+'</div></div>');
                        }
                        if(result1[i]==greenBox[k]){
                            html+='<div><div class="greenBox">'+result1[i]+'</div><div>'+result2[i]+'</div></div>';
                        }
                    }
                }
                $('.issueTitle .lotRstPanel').html(html);
                
                $(".lottery dd").prepend('<ul><li class="lotListItem">'+$('.issueTitle').html()+'</li></ul>');
                $('.lotListItem').children('em').remove();
            }else if(userinfo.lottery_type == 10){
                $('.rstIssue span').html(msg.issue.slice(4));
                var blueHtml = '';
                var redHtml = '';
                var winHtml = '';
                var result1 = msg.result.split("|");
                var blueArr = result1[0];
                blueArr = blueArr.split(",");
                blueArr[0] = blueArr[0].slice(3);
                var redArr = result1[1];
                redArr = redArr.split(",");
                redArr[0] = redArr[0].slice(3);
                var result2 = msg.sum_result_str.split(",")[0];
                var redNiu = msg.niu.red_niu;
                var blueNiu = msg.niu.blue_niu;
                var pokeArr = [{name:'黑桃A',url:'poker1_1'},{name:'黑桃2',url:'poker2_1'},{name:'黑桃3',url:'poker3_1'},{name:'黑桃4',url:'poker4_1'},{name:'黑桃5',url:'poker5_1'},{name:'黑桃6',url:'poker6_1'},{name:'黑桃7',url:'poker7_1'},{name:'黑桃8',url:'poker8_1'},{name:'黑桃9',url:'poker9_1'},{name:'黑桃10',url:'poker10_1'},{name:'黑桃J',url:'poker11_1'},{name:'黑桃Q',url:'poker12_1'},{name:'黑桃K',url:'poker13_1'},
                {name:'红心A',url:'poker1_2'},{name:'红心2',url:'poker2_2'},{name:'红心3',url:'poker3_2'},{name:'红心4',url:'poker4_2'},{name:'红心5',url:'poker5_2'},{name:'红心6',url:'poker6_2'},{name:'红心7',url:'poker7_2'},{name:'红心8',url:'poker8_2'},{name:'红心9',url:'poker9_2'},{name:'红心10',url:'poker10_2'},{name:'红心J',url:'poker11_2'},{name:'红心Q',url:'poker12_2'},{name:'红心K',url:'poker13_2'},
                {name:'梅花A',url:'poker1_3'},{name:'梅花2',url:'poker2_3'},{name:'梅花3',url:'poker3_3'},{name:'梅花4',url:'poker4_3'},{name:'梅花5',url:'poker5_3'},{name:'梅花6',url:'poker6_3'},{name:'梅花7',url:'poker7_3'},{name:'梅花8',url:'poker8_3'},{name:'梅花9',url:'poker9_3'},{name:'梅花10',url:'poker10_3'},{name:'梅花J',url:'poker11_3'},{name:'梅花Q',url:'poker12_3'},{name:'梅花K',url:'poker13_3'},
                {name:'方块A',url:'poker1_4'},{name:'方块2',url:'poker2_4'},{name:'方块3',url:'poker3_4'},{name:'方块4',url:'poker4_4'},{name:'方块5',url:'poker5_4'},{name:'方块6',url:'poker6_4'},{name:'方块7',url:'poker7_4'},{name:'方块8',url:'poker8_4'},{name:'方块9',url:'poker9_4'},{name:'方块10',url:'poker10_4'},{name:'方块J',url:'poker11_4'},{name:'方块Q',url:'poker12_4'},{name:'方块K',url:'poker13_4'},];
                for(var k=0;k<blueArr.length;k++){
                    for(var i=0;i<pokeArr.length;i++){
                        if(blueArr[k]==pokeArr[i].name){
                            blueHtml+='<div class="'+pokeArr[i].url+'"></div>';
                        }
                        if(redArr[k]==pokeArr[i].name){
                            redHtml+='<div class="'+pokeArr[i].url+'"></div>';
                        }
                    }
                }
                setTimeout(function(){
                    if(result2=='红方胜'){
                        $('.redRight').addClass('winStyle');
                        $('.rightWinImg').show();
                        $('.leftWinImg').hide(); 
                        $('.redRight .lottRstTip').html(redNiu).fadeIn();
                        $('.blueLeft .lottRstTip').html(blueNiu).fadeIn();
                        $('bluePoke').removeClass('winIndex');
                        $('redPoke').addClass('winIndex');
                        winHtml=redHtml;
                        
                    }else{
                        $('.blueLeft').addClass('winStyle');
                        $('.leftWinImg').show();                        
                        $('.rightWinImg').hide();                        
                        $('.redRight .lottRstTip').html(redNiu).fadeIn();
                        $('.blueLeft .lottRstTip').html(blueNiu).fadeIn();
                        $('redPoke').removeClass('winIndex');
                        $('bluePoke').addClass('winIndex');
                        winHtml=blueHtml;                        
                    }
                    $(".lottery dd").prepend('<ul><li><div>第<span class="issueStyle">'+msg.issue.slice(4)+'</span>期</div><div class="rstPanel">'+winHtml+'</div><div>('+msg.sum_result_str+')</div></li></ul>');
                },4700);
                $('.bluePoke').html(blueHtml);
                $('.redPoke').html(redHtml);
                $('.lotListItem').children('em').remove();
            }else{
                var resule = msg.result.split(/\+|=|\s\(\s|\s\)/g).slice(0,-1);

                $(".roomHead2 span").html('第<i id="issue2">'+msg.issue+'</i>期<em class="num_l">'+resule[0]+'</em> + <em class="num_l">'+resule[1]+'</em> + <em class="num_l">'+resule[2]+'</em> = <em class="num_r">'+resule[3]+'</em><em style="color: #2aa7f6"> ('+resule[4]+') </em>'); //幸运28更新开奖结果记录

                $(".lottery dd").prepend('<ul><li>第<em>'+msg.issue+'</em>期' +
                    //'<li>'+msg.open_time+'</li>' +
                    //'<li>'+msg.result+'</li></ul>');
                    '<em class="num_l">'+resule[0]+'</em> + <em class="num_l">'+resule[1]+'</em> + <em class="num_l">'+resule[2]+'</em> = <em class="num_r">'+resule[3]+'</em><em> ('+resule[4]+') </em></li></ul>'); //要分开处理数据段
                changeColor();
            }

			$(".lottery dd ul:last").remove();
            var param = {
                "commandid": "3012",
            };
            wsSendMsg(param);
			fnSet.scrollTop();
		}else if(commandId == "3015"){ //成功取消投注
            var userBetting =$('.userBetting dl.right');
            var issue =$("#issue").attr("data-issue");
            if(userinfo.lottery_type=='12'){
                msg.zushu=1
            }
            if(msg.zushu){
                offSet = offSet - msg.zushu;
            }else{
                offSet =0;
            }
//            fnSet.alert(msg.content);
            
            //limitFun(); //更新撤单后的限额金额
            //var val="";
            //$(".issue_"+msg.issue).each(function () {
            //    if($(this).attr("name") == msg.way[i]) {
            //        val =$(this).val()- msg;//减去撤单的钱
            //        $(this).val(val)
            //    }
            //});

            if (typeof(msg.order_no) != "undefined") {
                var num = $(".betTixing span em").text();
                var tempNum = num.replace(/[^0-9]/ig,'') - msg.zushu;
                //console.log(tempNum);
                $(".betTixing span em").html('本期已下'+tempNum+'注');
                totalZu=tempNum;
                offSet-=msg.zushu;
            }
            
            //去掉对应的下注记录，标黑
            if (typeof(msg.order_no) == "undefined") {//去掉全部
                for(var i=0; i<userBetting.length; i++){
                    if(userBetting.eq(i).attr("data-issue") ==issue){
                        userBetting.eq(i).find("i").show();
                    }
                }
                
                $(".betTixing").hide();
                $(".quxiao ul").html("");
            }else{ //去掉对应的下注记录，划线
                $("p[order-no="+ msg.order_no +"]").css("text-decoration","line-through");
                $("p[order-no="+ msg.order_no +"]").children(".r").css("text-decoration","line-through");
                $("p[order-no="+ msg.order_no +"]").attr("bj","true");
                var tzts = $("p[order-no="+ msg.order_no +"]").siblings("p").length;
                var bjts = $("p[order-no="+ msg.order_no +"]").siblings("p[bj=true]").length;
                if (bjts == tzts) {
                    $("p[order-no="+ msg.order_no +"]").siblings("i").show();
                }
                if(userinfo.lottery_type=='12'){
                    $("font[order-no="+ msg.order_no +"]").parent().remove();    //下注记录中对应的记录删除
                }else{
                    $("em[order-no="+ msg.order_no +"]").parent().remove();    //下注记录中对应的记录删除
                }
                //console.log($(".quxiao ul li").length);
                if ($(".quxiao ul li").length == 0) {
                    $(".betTixing").hide();
                }
            }
            
            //系统消息反馈
            roomGroup ='<div class="userBetting2"><ul class="system"><li style="font-size: 14px;"><pre>'+msg.content+'</pre></li></ul></div>'
            $(".room .roomContent").append(roomGroup);
            fnSet.scrollTop();
            
		}else if(commandId == "3018"){
            //并更新用户投注信息，记录
            fnSet.update318(msg);

        }else if(commandId == "3019") {
            $("#dZh").show();//追号详情图标显示
            $("#zhuiHList").empty();//追号工具列表清空
            //用户追号信息消息推送
            fnSet.update319(msg);

        }else if(commandId == "3020"){
            if(userinfo != null) {
                var param = {
                    "commandid": "3002",
                    "uid": userinfo.userid,
                    "roomid":userinfo.room_id
                };
                wsSendMsg(param);
            }
        }else if(commandId == "3022"){
            $(".zhui_details").hide();//追号详情弹窗隐藏
            //fnSet.alert(msg.content);
            //系统消息反馈
            roomGroup ='<div class="userBetting2"><ul class="system"><li style="font-size: 14px;"><pre>'+msg.content+'</pre></li></ul></div>'
            $(".room .roomContent").append(roomGroup);
            fnSet.scrollTop();

        }else if(commandId == "4003"){
            //这一项不清楚
		 	var float ="left";
			var head_url1 ="/statics/web/images/avatar.png";
			if(msg.username ==""){
				msg.username ="机器人";
                var contentHtml ='<li><div class="customContent '+float+'"><pre>'+msg.content+'</pre></div></li>';

			}else if(msg.username == userinfo.nickname){
				float ="right";
                head_url1 =userinfo.head_url;
                var contentHtml ='<li><h3 class="'+float+'">'+ msg.username +'</h3><div class="girl '+float+'"><img src="'+head_url1+'"></div><div class="customContent '+float+'"><pre>'+msg.content+'</pre></div></li>';
            }

			$(".customService ul").append(contentHtml);

            setTimeout(function() {
                $('.customService').css("padding-bottom",$(".customNews").outerHeight()+98);
                var pageHeight =$(".customService ul").outerHeight();
                $('.customService').scrollTop(pageHeight);
            }, 100);

        }else if(commandId == "3026") {
            //中奖提示弹窗
            var html = '<div class="win-pop"><div class=""></div><div class="win-wrap"><div class="win-con">'+
                '<div class="win-img"></div><div class="win-info">中奖'+msg.money+'元宝</div>'+
                '<div class="win-close"></div></div></div></div>';
            $(".popup").html(html);
            $(".popup").fadeIn();
            //刷新金额
            $(".roomHead1 span.icoAcer").text(msg.use_money);
        }else if(commandId == "3031"){
            $(".titleTime").html(msg.data.match_field+'<span class="timeAni">'+msg.data.take_time)
            endState = msg.data.match_end_state
            var rhText = ''
            switch(endState){
                case 0:
                    // fnSet.showOdds(0,0,true);
                    rhText='未开赛'
                    break;
                case 1:
                    rhText='上半场'
                    break;
                case 2:
                    rhText='半场'
                    break;
                case 3:
                    rhText='下半场'
                    break;
                case 4:
                    rhText='下半场结束'
                    break;
                case 5:
                    rhText='加时赛'
                    break;
                case 6:
                    rhText='加时赛结束'
                    break;
                case 7:
                    rhText='点球'
                    break;
                case 8:
                    rhText='点球结束'
                    break;
                case 9:
                    rhText='全场结束'
                    break;
                default:
                    rhText='未开赛'
                    break;
            }
            $(".rhState").html(rhText)
        }else if(commandId == "3032"){
            var param = {
                "commandid": "3017",
                "uid": userinfo.userid,
                "roomid":userinfo.room_id,
                "lottery_type":userinfo.lottery_type,
                "offSet":offSet
            };
            wsSendMsg(param);
        }
	}
};