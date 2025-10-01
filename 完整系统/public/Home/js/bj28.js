var k3v = {},
    tryflag = true, //试试手
    timer = null, //试试手
    ifopen = false, //是不是视频启动中
    g_hostIp = "http://" + document.domain + "/Home/Get/getBj28?t=" + Math.random(),
    ifpaused = "", //是不是音乐都暂停了
    animateId = {};

$(function() {
    $(".animate").find(".loading").fadeOut(1000, function() {});
    $(".kuai3Animate").on("click", ".kaimodule", function() {
        k3v.tryPlay();
    });
    $("#soundBtn").on("click", "#spanbtn", function() {
        var audioId = document.getElementById("audio");
        if ($("#spanbtn").attr("class") == "sounds") {
            $("#soundBtn").children().removeClass("sounds").addClass("sounds2");
            k3v.sound.stop("audioid");
        } else {
            $("#soundBtn").children().removeClass("sounds2").addClass("sounds");
            k3v.sound.play("all");
        }
    });


    $.get(g_hostIp, function(data) {
        data = JSON.parse(data);
        ifopen = true;
        k3v.startGame(true);
        k3v.stopVideo(data);
    });
})

k3v.startGame = function(flag) {
    //操作号码
    var obj = this;
    obj.codePlay = function() {
        var li = $("#code").find("li");
        obj.run(2, "80", "0", li);
        obj.run(5, "80", "1", li);
        obj.run(8, "80", "2", li);
    }
    obj.run = function(num, space, id, li) {
        var interval = setInterval(function() {
            $(li).eq(id).attr("class", "k3v0" + num);
            num++;
            if (num >= 8) {
                num = 1;
            }
        }, space);
        animateId[id] = interval;
    }
    if (flag) {
        obj.codePlay();
    }
    $(".linelist").find("li").addClass("redli");
    //添加音乐播放
    ifpaused = "audioidR";
    if ($("#spanbtn").attr("class") == "sounds" && ifopen) {
        k3v.sound.play("audioidR");
    }
    k3v.bressBG(10); //呼吸动作
}

k3v.stopGame = function(arr) {
    this.stop = function(i, arr) {
        setTimeout(function() {
            console.log(animateId[i]);
            clearInterval(animateId[i]);
            var li = $("#code").find("li");
            $(li).eq(i).attr("class", "k3v0" + arr);
        }, i * 800);
    }
    for (var i = 0, len = 3; i < len; i++) {
        this.stop(i, arr[i]);
    }
}
//模拟开奖
var trytime = [];
k3v.tryPlay = function() {
    var arr = [];
    if (tryflag) {
        $("#timetitle").text("模拟开奖");
        $("#hourtxt").hide();
        $("#opening").show();
        tryflag = false;
        k3v.startGame(true);
        var time1 = setTimeout(function() {
            for (var i = 0; i < 3; i++) {
                arr.push(Math.round(Math.random() * 5 + 1));
            }
            k3v.stopGame(arr);
            var time2 = setTimeout(function() {
                var codetop = $("#codetop").find("li");
                var resultArr = [];
                for (var i = 0, len = codetop.length; i < len; i++) {
                    resultArr.push($(codetop).eq(i).text());
                }
                k3v.stopGame(resultArr);
                tryflag = true;
            }, 8000);
            var time3 = setTimeout(function() {
                $("#timetitle").text("倒计时");
                $("#hourtxt").show();
                $("#opening").hide();
                ifpaused = "audioidB";
                if ($("#spanbtn").attr("class") == "sounds" && ifopen) {
                    k3v.sound.play("audioidB");
                }
                k3v.bressBG();
                $(".linelist").find("li").removeAttr("class");
            }, 2000);
            trytime.push(time1);
            trytime.push(time2);
            trytime.push(time3);
            // console.log("time2" + time2);
        }, 5000);
    } else {
        $(".noinfor").fadeIn(200, "", function() {
            setTimeout(function() {
                $(".noinfor").fadeOut("300");
            }, 1000)
        });

    }
}
//倒计时完成启动动画
k3v.playGame = function() {
    var arr = [];
    $("#timetitle").text("正在开奖");
    $("#hourtxt").hide();
    $("#opening").show();
    k3v.startGame(true);
}
//停止开奖时更新数据
k3v.updateData = function(data) {
    var arrCode = data.preDrawCode;
    var preDrawIssue = $("#preDrawIssue").text(data.preDrawIssue);

    var num1 = parseInt(arrCode[0]);
    $("#num1").text(num1);
    var num2 = parseInt(arrCode[1]);
    $("#num2").text(num2);
    var num3 = parseInt(arrCode[2]);
    $("#num3").text(num3);

    var sumNum = $("#sumNum").text(num1 + num2 + num3); //var sumNum = $("#sumNum").text(data.sumNum);
    var sumBigSmall = $("#sumBigSmall").text(data.sumBigSmall);
    var drawIssue = $("#drawIssue").text(data.drawIssue);
    var drawTimestr = data.drawTime.substr(data.drawTime.length - 8, 8);
    var drawTime = $("#drawTime").text(drawTimestr);
}
//倒计时
k3v.cutTime = function(time) {
    if (timer != null) {
        clearInterval(timer);
    }
    var sys_second = time - 3;
    timer = setInterval(function() {
        if (sys_second >= 1) {
            sys_second -= 1;
            //var day = Math.floor((sys_second / 3600) / 24);
            var hour = Math.floor(sys_second / (60 * 60));
            var minute = Math.floor((sys_second / (60)) % 60);
            var second = Math.floor((sys_second) % 60);
            var html = "";
            //如果时间小于0则删除时间显示
            html = (hour < 10 ? ("0" + hour) : hour) + ":";
            html = html + "" + (minute < 10 ? ("0" + minute) : minute) + ":" + (second < 10 ? ("0" + second) : second);
            $("#hourtxt").text(html); //计算小时
            if (sys_second < 10) {
                var lilist = $(".linelist").find("li");
                $(lilist).eq(sys_second).addClass("redli");
            }
            if (sys_second < 20) {
                tryflag = false;
                $(".noinfor").text("即将开奖，禁止模拟");
            }
        } else {
            $(".noinfor").text("正在开奖，禁止模拟");
            clearInterval(timer); //清除当前定时器
            k3v.startGame(true);

            var t = setInterval(function() {
                $.get(g_hostIp, function(data) {
                    data = JSON.parse(data);
                    if (data.preDrawIssue != $('#preDrawIssue').text()) {
                        ifopen = true;

                        k3v.stopVideo(data);
                        clearInterval(t);
                    }

                });
            }, 5000);
        }
    }, 1000);
}
k3v.startVideo = function(data) {
    $("#audioid").attr("loop", "loop");
    if ($("#spanbtn").attr("class") == "sounds" && ifopen) {
        k3v.sound.play("audioidB");
    }
    k3v.cutTime(data.time);
    k3v.bressBG();
}
k3v.sound = {
    play: function(id) {
        if ($("#spanbtn").attr("class") == "sounds" && ifopen) {
            if (id == "all") {
                document.getElementById(ifpaused).play();
            } else {
                document.getElementById("audioidB").pause();
                document.getElementById("audioidR").pause();
                document.getElementById(id).play();
            }
        }
    },
    stop: function(id) {
        var audioidB = document.getElementById("audioidB");
        if (audioidB.paused) {
            ifpaused = "audioidR";
        } else {
            ifpaused = "audioidB";
        }
        document.getElementById("audioidB").pause();
        document.getElementById("audioidR").pause();

    }
}
//停止动画
k3v.stopVideo = function(data) {
    //终止游戏
      var arrCode = data.preDrawCode;
    var resultArr = [];
    var num1 = parseInt(arrCode[0]);
    var num2 = parseInt(arrCode[1]);
    var num3 = parseInt(arrCode[2]);
    resultArr.push(num1);
    resultArr.push(num2);
    resultArr.push(num3);

     k3v.stopGame(resultArr);
    //更新数据以
    k3v.updateData(data);
    setTimeout(function() {
        $("#timetitle").text("倒计时");
        $("#hourtxt").fadeIn();
        $("#opening").hide();
        $(".linelist").find("li").removeAttr("class");
        k3v.startVideo(data);
        tryflag = true;
    }, 2000);
}
k3v.bressBG = function(space) {
    var opacityV = 1,
        flag = false;
    if (animateId["bressBG"] != undefined) {
        clearInterval(animateId["bressBG"]);
    }
    if (space == undefined) {
        space = 80;
    }
    var timesetInterval = setInterval(function() {
        $(".bodybg").find("img").stop().animate({
            opacity: "0." + opacityV
        }, space);
        if (flag) {
            opacityV -= 1;
            if (opacityV < 1) {
                flag = false;
            }
        } else {
            opacityV += 1;
            if (opacityV > 8) {
                flag = true;
            }
        }
    }, space);
    animateId["bressBG"] = timesetInterval;
}


// $(function(){
//  var  data =  {
//             "preDrawCode": [1,1,3],
//             "drawIssue": "171020049",
//             "drawTime": "2017-10-20 16:39:00",
//             "preDrawIssue": "171020048",
//             "sumBigSmall": '小',
//             "sumNum": 5,
//     };
// k3v.stopVideo(data);

// })

// var data =  {
//     "preDrawCode": [7,2,4,8,1],
//     "drawTime": "2017-08-26 21:10:45",
//     "preDrawIssue": 20170826097,
//     "sumNum": 27,
//     "sumBigSmall": "大",
//      "dragonTiger": "龙",
//     "sumSingleDouble": "单",
//     "status": 0,
//     "id": "#numBig",
//     'counttime':200,
// };
// data.preDrawCode = [1,2,3,4,5];
// ifopen = true;
// k3v.tryPlay();