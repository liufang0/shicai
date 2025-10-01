var roomId=1;
$(function(){
	$(".listCon li").click(function(){
		var price = 60;//从后台获取的余额
		var type = $(this).attr("type");
		if (type == "privateRoom") {
         
		    roomId=4;
			$(".mask").fadeIn();
			$(".roomNum").addClass("active");
			return;
		}else if(type == "primaryRoom"){
			//window.location.href = "gamePage.html?roomId=1";
			roomId=1;
		}else if(type=="middleRoom"){
            roomId=2;
		}else if(type=="seniorRoom"){
            roomId=3;
		}
      	return ;
         $.ajax({
            url: "/index.php/Home/Index/checkroom?roomId="+roomId+"&password=",
            async:false,
            success: function(data){
                    //alert(data);
                    ajaxobj=eval("("+data+")");

                    if(ajaxobj.flag){
                        if(ajaxobj.gameId == 5 || ajaxobj.gameId == 6){
                            window.location.href = "pksaiche.html?roomId="+roomId;
                        }else{
                            window.location.href = "gamePage.html?roomId="+roomId;
                        }
                    }else{
                        tips(ajaxobj.msg);
                        return;
                    }

            },
            error:function(data)
            {
                   tips("网络连接异常");
            }
        });
	})
	
	$(".mask,.icon-guanbi").click(function(){//点击遮罩层时做相应的动作
		$(".mask").fadeOut();
		$(".tips").removeClass("active");
		$(".roomNum").removeClass("active");
	})

	//提示方法
	function tips(txt){
		 layer.open({
		    content: txt,
		    skin: 'msg',
		    time: 2 //2秒后自动关闭
		  });
	}

	
	

})

