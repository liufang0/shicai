/**
 * 
 */

$(function(){
	getSpan(0,0,0,0);//支付接口开关 ，0为开启，1为关闭！
	//支付宝，QQ，财付通，微信
	
	$('#ginfo').hide();
	
	$(".btnSpan").click(function(){
		var e = $(this).attr("title");
		//$("#"+e).attr("checked","checked");
		$(":radio").val([e]);
	})
	
	//*******************提交按钮************************
	$("#submit_buy").click(function(){
		
		var a = $('#kc').attr("alt");
		if(a <= 0){
			layer.msg('该商品库存不足，无法购买！');
			return;
		}
		var gName = $("#gid").val();//获取商品名称<展示>
		var gId = $("#gid option:selected").attr("id");//获取商品ID * 
		var price = $("#need").val();//获取商品价格<展示>
		var money =$("#gid option:selected").attr("title");;//获取商品价格  * 
		var num = $("#lx").val();//获取用户ID *
		var type = $("input:radio[name='type']:checked").val();//获取付款方式 *
		var payfs = $("input:radio[name='type']:checked").attr("title");//付款方式<展示>
		var out_trade_no = d();//订单编号 * 
		var number = parseInt($("#number").val());//获取数量
		var b = checkLx(num);//判断用户ID是否正确
		if(a < number){
			layer.msg('选择数量大于库存数量！');
			return;
		}
		if(number <= 0){
			layer.msg('选择数量请大于0件！');
			return;
		}
		if(b == false){
			layer.msg('请输入正确的用户ID');
			return;
		}
		if(type == null || type =="" ){
			layer.msg('请选择您的付款方式');
			return;
		}
		if(gId == "" || price == "" || gId == null || price == null){
			layer.msg('当前商品无法创建订单！');
			return;
		}
		var ii = layer.load(2, {shade:[0.1,'#fff']});

		//验证卡密信息
		$.ajax({
			type : "POST",
			url : "ajax.php?act=selKm",
			data : {"gid":gId},
			dataType : 'json',
			success : function(data) {
				if(data.code == -1){
					layer.close(ii);
					layer.msg('该商品卡密库存不足！无法购买！');
					return;
				}
			},
			error:function(data){
				 layer.close(ii);
				layer.msg('服务器错误');
				return;
				}
		});
		layer.close(ii);
		money = money * number;
		var u = "type="+type+"&name="+gName+"&money="+money+"&number="+number+"&out_trade_no="+out_trade_no+"&gid="+gId;
		
		var str = "请核对订单信息！<br>订单编号："+out_trade_no+
		"<br>商品名称："+gName+
		"<br>购买数量："+number+
		"<br>商品价格："+money+"￥"+
		"<br>用户ID："+num+
		"<br>付款方式："+payfs;
		
		layer.confirm(str, {
			  btn: ['立即付款123','取消付款'] //按钮
			}, function(){
				//异步创建订单
				var ii = layer.load(2, {shade:[0.1,'#fff']})
				$.ajax({
					type : "POST",
					url : "ajax.php?act=create",
					data : {"out_trade_no":out_trade_no,"gid":gId,"money":money,"rel":num,"type":type,"number":number},
					dataType : 'json',
					//timeout : 5000,
					success : function(data) {	
						 layer.close(ii);
						 if(data.code != 0){
								layer.alert('创建订单失败！'+data.msg);
								return false;
						 }
						
						 window.location.href ="other/submit.php?"+u;
						
					},
					error:function(data){
						 layer.close(ii);
						layer.msg('服务器错误');
						return false;
						}
				})
				//*********************************************
				
				
			});
	})
	//*******************提交结束************************
	
	//******************查询/提取************************
	$("#submit_query").click(function(){
		var t = $("#tqm").val();
		if(t ==null || t ==""){
			layer.msg("请输入提取卡密的凭证！");
			return false;
		}
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "ajax.php?act=tqKm",
			data : {"t":t},
			dataType : 'json',
			success : function(data) {
				 layer.close(ii);
				if(data.code == 0){
					$("#list").empty();
					$("#list").append(data.msg);
					$("#result2").slideDown();
				}else{
					layer.msg(data.msg);
				}
			},
			error:function(data){
				 layer.close(ii);
				layer.msg('服务器错误');
				return false;
				}
		})
	})
	$("#copykm").click(function(){
		//var km = $("#km").val();
		copyToClipboard( 'km');
		layer.alert("成功复制卡密到剪切板!");
	})
	//***************发邮箱*************
	$("#subimt_email").click(function(){
		var e = $("#txt_email").val();
		var reg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
		isok= reg.test(e);
		if (!isok) {
		    alert("邮箱格式不正确，请重新输入！");
		    return false;
		}
		var od_n = $("#td1").text();
		var od_b = $("#td2").text();
		var od_d = $("#td3").text();
		var od_k = $("#td4").text();
		//alert(od_n+"　"+"　"+od_b+"　"+od_d+"　"+od_k);
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "ajax.php?act=email",
			data : {"od_n":od_n,"od_b":od_b,"od_d":od_d,"od_k":od_k,"e":e},
			dataType : 'json',
			success : function(data) {					  
				  layer.close(ii);
				  layer.msg(data.msg);
			},
			error:function(data){
				layer.close(ii);
				layer.msg("发送失败！");
				}
		})

	})
	
	
})
 //获取商品
function getPoint(e){
		  	var d = $(e).val();//获取该分类的ID
		  	var ii = layer.load(2, {shade:[0.1,'#fff']});
		  	$.ajax({
				type : "POST",
				url : "ajax.php?act=selgo",
				data : {"tyid":d},
				dataType : 'json',
				success : function(data) {					  
					  layer.close(ii);
					
					//$("#gid").empty();
					//$("#gid").append(data.msg);
					$("#gid").html(data.msg);
					
					$('#ginfo').html("");
					$('#ginfo').hide();
				},
				error:function(data){
					layer.close(ii);
					layer.msg('服务器错误'+data);
					
					 
					}
			});
		  	
}
//获取库存
function getKc(){
	var kc  = $("#gid option:selected").attr("kc");
	$('#kc').val(kc);
	$('#kc').attr("alt",kc);
	
}
//获取商品介绍
function getInfo(){
	$('#ginfo').show();
	var alt  = $("#gid option:selected").attr("alt");
	if(alt == "" || alt == null){
		$('#ginfo').html("该商品无介绍");
	}else{
		$('#ginfo').html(alt);
	}
	
}
//获取价格
function getPrice(){
		 var price  = $("#gid option:selected").attr("title");
		 $('#need').val('￥'+price);
		 getKc();
		 getInfo();
		 getImgs();
}
function getPrice_zongzi(){
	 var price  = $("#gid option:selected").attr("title");
	 $('#need').text('￥'+price);
	 checknum_zongzi();
	 var kc  = $("#gid option:selected").attr("kc");
	 $('#kc').text(kc);
	 $('#kc').attr("alt",kc);
}
//获取商品图片
function getImgs(){
	var imgs  = $("#gid option:selected").attr("imgs");
	if(imgs == "" || imgs == null || imgs == "undefined"){
		imgs = "./assets/goodsimg/df.jpg";
	}
	$("#goodimgs").attr("src",imgs);
	//alert(imgs);
}
function getSpan(alipay,qqpay,tenpay,wxpay){
	var alipay = alipay;
	var qqpay = qqpay;
	var tenpay = tenpay;
	var wxpay = wxpay;
	var s = document.getElementsByTagName("span");
	//alert(s.length);
	for(var i=0;i<s.length;i++){
		var temp = $(s[i]).attr("title");
		if(temp == "alipay" && alipay == 1){
			$(s[i]).hide();
		}
		if(temp == "qqpay" && qqpay == 1){
			$(s[i]).hide();
		}
		if(temp == "tenpay" && tenpay == 1){
			$(s[i]).hide();
		}
		if(temp == "tenpay" && tenpay == 0){
			$(s[i]).show();
		}
		if(temp == "wxpay" && wxpay == 1){
			$(s[i]).hide();
		}
	}
}

//生成单号
function d(){
	 var d=new Date() ;
	 var e;
	 e=d.getFullYear()+""+d.getMonth()+""+d.getDate()+""+d.getHours()+""+d.getMinutes()+""+d.getMilliseconds()+""+randomNum(100,999);
	 return e;
}


function checkLx(num){
	var t=num;
	if(!isNaN(t) && t !=""){
	 return true;
	}else{
	  return false;
	}
}
function randomNum(minNum,maxNum){ 
 switch(arguments.length){ 
 case 1: 
  return parseInt(Math.random()*minNum+1); 
 break; 
 case 2: 
  return parseInt(Math.random()*(maxNum-minNum+1)+minNum); 
 break; 
 default: 
  return 0; 
 break; 
 } 
}

function copyToClipboard(elementId) {
    // 创建元素用于复制
    var aux = document.createElement("input");

    // 获取复制内容
    var content = document.getElementById(elementId).innerHTML || document.getElementById(elementId).value;
  
    // 设置元素内容
    aux.setAttribute("value", content);
  
    // 将元素插入页面进行调用
    document.body.appendChild(aux);
  
    // 复制内容
    aux.select();
  
    // 将内容复制到剪贴板
    document.execCommand("copy");
  
    // 删除创建元素
    document.body.removeChild(aux);
  }

function Addme() {
    url = document.URL;  //你自己的主页地址
    title = $("#bttt").text();  //你自己的主页名称
    window.external.AddFavorite(url, title);
}
function numstepUp(){
	   var num =  parseInt($("#number").val());
	   $("#number").val(num+1);
	  }
function numstepDown(){
	  var num =  parseInt($("#number").val());
	  if(num > 1){
		  $("#number").val(num-1);
	  }
}
function checknum(){
		var a = $('#kc').attr("alt");
		 var num =  parseInt($("#number").val());
		 
		  if(num <= 0 || num == null || num == ""|| isNaN(num)){
			  $("#number").val(1);
		  }else{
			  $("#number").val(num);
		}
		  if(a < num){
				 $("#number").val(a);
			}
		  
	}
function checknum_zongzi(){
	var a = $('#kc').attr("alt");
	 var num =  parseInt($("#number").val());
	 
	  if(num <= 0 || num == null || num == ""|| isNaN(num)){
		  $("#number").val(1);
	  }else{
		  $("#number").val(num);
	}
	if(a < num){
			 $("#number").val(a);
	}
	
	var number = parseInt($("#number").val());//获取数量
	var money =$("#gid option:selected").attr("title");;//获取商品价格  * 
	
	$("#allprice").text(number*money);
}

function zongzisub(){
	var a = $('#kc').attr("alt");
	if(a <= 0){
		layer.msg('该商品库存不足，无法购买！');
		return;
	}
	var gName = $("#gid").val();//获取商品名称<展示>
	var gId = $("#gid option:selected").attr("id");//获取商品ID * 
	var price = $("#need").val();//获取商品价格<展示>
	var money =$("#gid option:selected").attr("title");;//获取商品价格  * 
	var num = $("#lx").val();//获取用户ID *
	var type = $("input:radio[name='type']:checked").val();//获取付款方式 *
	var payfs = $("input:radio[name='type']:checked").attr("title");//付款方式<展示>
	var out_trade_no = d();//订单编号 * 
	var number = parseInt($("#number").val());//获取数量
	var b = checkLx(num);//判断用户ID是否正确
	if(a < number){
		layer.msg('选择数量大于库存数量！', {time: 3000, icon:6});
		return;
	}
	if(number <= 0){
		layer.msg('选择数量请大于0件！', {time: 3000, icon:6});
		return;
	}
	if(num.length < 5 || b == false || num == " "){
		layer.msg('请输入正确的用户ID', {time: 3000, icon:6});
		return;
	}
	if(type == null || type =="" ){
		layer.msg('请选择您的付款方式', {time: 3000, icon:6});
		return;
	}
	if(gId == "" || money == "" || gId == null || money == null){
		layer.msg('当前商品无法创建订单！', {time: 3000, icon:6});
		return;
	}
	var ii = layer.load(2, {shade:[0.1,'#fff']});

	//验证卡密信息
	$.ajax({
		type : "POST",
		url : "ajax.php?act=selKm",
		data : {"gid":gId},
		dataType : 'json',
		success : function(data) {
			if(data.code == -1){
				layer.close(ii);
				layer.msg('该商品卡密库存不足！无法购买！');
				return;
			}
		},
		error:function(data){
			 layer.close(ii);
			layer.msg('服务器错误');
			return;
			}
	});
	layer.close(ii);
	money = money * number;
	var u = "type="+type+"&name="+gName+"&money="+money+"&number="+number+"&out_trade_no="+out_trade_no+"&gid="+gId;
	
	var str = "请核对订单信息！<br>订单编号："+out_trade_no+
	"<br>商品名称："+gName+
	"<br>购买数量："+number+
	"<br>商品价格："+money+"￥"+
	"<br>用户ID："+num+
	"<br>付款方式："+payfs;
	
	layer.confirm(str, {
		  btn: ['立即付款3','取消付款'] //按钮
		}, function(){
			//异步创建订单
			var ii = layer.load(2, {shade:[0.1,'#fff']})
			$.ajax({
				type : "POST",
				url : "ajax.php?act=create",
				data : {"out_trade_no":out_trade_no,"gid":gId,"money":money,"rel":num,"type":type,"number":number},
				dataType : 'json',
				//timeout : 5000,
				success : function(data) {	
					 layer.close(ii);
					 if(data.code != 0){
							layer.alert('创建订单失败！'+data.msg);
							return false;
					 }
					 
					 /**
					 if(data.wdpay == 0){
						 window.location.href = data.httpUrl;
					 }else{
						
					 }**/
					
					 window.location.href ="other/submit.php?"+u;
					 
					
					
				},
				error:function(data){
					 layer.close(ii);
					layer.msg('服务器错误');
					return false;
					}
			})
			//*********************************************
			
			
		});
}

