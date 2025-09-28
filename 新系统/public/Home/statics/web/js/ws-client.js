//var url = "ws://192.168.1.202:7272";
var wSport = $("#wsPort").attr("data-port");
function getReason(event) {
//	console.log(event);
	var reason;
	// See http://tools.ietf.org/html/rfc6455#section-7.4.1
	if(event.code == 1000) {
		reason = "正常关闭，意味着连接建立的目的已完成。";
	} else if(event.code == 1001) {
		reason = "终端离开，例如服务器关闭或浏览器导航到其他页面。";
	} else if(event.code == 1002) {
		reason = "终端因为协议错误而关闭连接。";
	} else if(event.code == 1003) {
		reason = "终端因为接收到不能接受的数据而关闭（例如，只明白文本数据的终端可能发送这个，如果它接收到二进制消息）。";
	} else if(event.code == 1004) {
		reason = "保留。这个特定含义可能在以后定义。";
	} else if(event.code == 1005) {
		reason = "保留。且终端必须不在控制帧里设置作为状态码。它是指定给应用程序而非作为状态码 使用的，用来指示没有状态码出现。";
	} else if(event.code == 1006) {
		reason = "同上。保留。且终端必须不在控制帧里设置作为状态码。它是指定给应用程序而非作为状态码使用的，用来指示连接非正常关闭，例如，没有发生或接收到关闭帧。";
	} else if(event.code == 1007) {
		reason = "终端因为接收到的数据没有消息类型而关闭连接。";
	} else if(event.code == 1008) {
		reason = "终端因为接收到的消息背离它的政策而关闭连接。这是一个通用的状态码，用在没有更合适的状态码或需要隐藏具体的政策细节时。";
	} else if(event.code == 1009) {
		reason = "终端因为接收到的消息太大以至于不能处理而关闭连接。";
	} else if(event.code == 1010) {
		reason = "客户端因为想和服务器协商一个或多个扩展，而服务器不在响应消息返回它（扩展）而关闭连接。需要的扩展列表应该出现在关闭帧的/reason/部分。注意，这个状态码不是由服务器使用，因为它会导致WebSocket握手失败:" + event.reason;
	} else if(event.code == 1011) {
		reason = "服务器因为遇到非预期的情况导致它不能完成请求而关闭连接。";
	} else if(event.code == 1015) {
		reason = "保留，且终端必须不在控制帧里设置作为状态码。它是指定用于应用程序希望用状态码来指示连接因为TLS握手失败而关闭。";
	} else {
		reason = "未知原因：" + +event.reason;
	}
	return event.code + ":" + reason;
}
var initWebSocket = function() {
	if(window.WebSocket) {
		socket = new WebSocket(url);
		socket.onmessage = function(event) {
			console.log("WebsocketClient接收到消息：" + event.data);
			if(event.data !="ping"){
				if(event.data==''){
					return;
				}
                var msgData = JSON.parse(event.data);
                //debugger;
                store.processMsg(msgData);
			}
		};
		socket.onopen = function(event) {
			setInterval(function() {
				var param = {
					"commandid": "3012",
					"uid": userinfo.userid,
				};
				wsSendMsg(param);
			}, 30000);

			if(userinfo != null) {

				var param = {
					"commandid": "3002",
					"uid": userinfo.userid,
					"roomid":userinfo.room_id
				};
				wsSendMsg(param);

				var param = {
					"commandid": "3017",
					"uid": userinfo.userid,
					"roomid":userinfo.room_id,
					"lottery_type":userinfo.lottery_type,
					"offSet":offSet
				};
				wsSendMsg(param);
			}


			//console.log("WebsocketClient已打开");
		};
		socket.onclose = function(event) {
			var reason = getReason(event);
			console.log("WebsocketClient被关闭了，原因是：" + reason);
			//reconnect();
		};
		socket.onerror = function(event) {
			console.log("WebsocketClient发生错误：" + JSON.stringify(event));
		};
	} else {
		$.alert('此浏览器不支持websocket');
	}
}

var retryCount = 0;
function reconnect() {
	retryCount++;
	//console.log("连接WebSocket第" + retryCount + "次重试");
	setTimeout(function() {
		initWebSocket();
	}, 250);
}

var msgQueue = new Array();// 消息发送队列
var wsSendMsg = function(param) {
	// 将消息加入消息队列
	msgQueue.unshift(param);
	var wsState = socket.readyState;
	if (wsState == WebSocket.CONNECTING) {
		// WebSocket处于CONNECTING的状态时,等待WebSocket的状态变为OPEN
	} else if(wsState == WebSocket.OPEN) {
		// WebSocket处于OPEN的状态时,将消息队列中的消息逐个取出来发送
		for (var i = 0; i < msgQueue.length; i++) {
			var msg = msgQueue.pop();
			var _msg = JSON.stringify(msg);
			//console.log("发送的消息为：" + _msg);
			socket.send(_msg);
		}
	} else if(wsState == WebSocket.CLOSING) {
		// WebSocket处于CLOSING的状态时,等待WebSocket的状态变为CLOSED
	} else if(wsState == WebSocket.CLOSED) {
		// WebSocket处于CLOSED的状态时,重新连接
		//reconnect();
	}
}


