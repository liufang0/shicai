<?php

namespace Home\Controller;
use Think\Server;

header('content-type:text/html;charset=utf-8');
class WorkermansscController extends Server {

	protected $socket = 'websocket://0.0.0.0:15533';
	
	/*添加定时器
	 *监控连接状态
	 * */
	public function onWorkerStart(){
		$auth = auth_check(C('auth_code'),C('siteurl'));
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

		$beginToday=strtotime('09:00:00');
		$endToday=strtotime("23:59:59");
		$caiji = M('caiji')->where("game='ssc'")->limit(0,1)->order("id desc")->find();
		$data =  ssc_format($caiji);
		
		$time_interval = 1;
		$scdata = json_decode($data,true);
		$nexttime = $scdata['next']['delayTimeInterval']+strtotime($scdata['next']['awardTime']);
		$time = time();
		if ($time < strtotime("21:57:00") && $time > strtotime("02:00:00")) {
			$st_time = 580;
		} else {
			$st_time = 280;
		}

		if($nexttime-time()>C('ssc_stop_time') && $nexttime-time()<$st_time){
			F('ssc_state',1);
		}else{
			F('ssc_state',0);
		}
		if(!F('sscdata')){
			F('sscdata',$scdata);
		}
		if(!F('is_send')){
			F('is_send',1);
		}
		
		/*开奖*/
		\Workerman\Lib\Timer::add($time_interval, function(){
			$beginToday=strtotime('09:00:00');
			$endToday=strtotime("23:59:59");
			F('game','ssc');

			$sscdata = F('sscdata');
			$next_time = $sscdata['next']['delayTimeInterval']+strtotime($sscdata['next']['awardTime']);
			$awardtime = $sscdata['current']['awardTime'];

			$time = time();
			if ($time < strtotime("21:57:00") && $time > strtotime("02:00:00")) {
				$st_time = 580;
			} else {
				$st_time = 280;
			}

			if($next_time-time()>C('ssc_stop_time') && $next_time-time()<$st_time ){
				F('ssc_state',1);
			}else{
				F('ssc_state',0);
			}

			if($next_time-time()==30+C('ssc_stop_time')){
				$new_message = array(
					'type' => 'admin',
					'head_img_url'=>'/Public/main/img/kefu.jpg',
					'from_client_name' => 'GM管理员',
					'content'=>'期号:'.$sscdata['next']['periodNumber'].'<br/>'.'--距离封盘还有30秒--',
					'time'=>date('H:i:s')
				);
				foreach ($this->worker->connections as $conn) {
					$conn -> send(json_encode($new_message));
				}
			}

			if($next_time-time()==C('ssc_stop_time')){
				F('ssc_state',0);
				$new_message = array(
					'type' => 'admin',
					'head_img_url'=>'/Public/main/img/kefu.jpg',
					'from_client_name' => 'GM管理员',
					'content'=>'期号:'.$sscdata['next']['periodNumber'].'关闭，请耐心等待开奖',
					'time'=>date('H:i:s')
				);
				foreach ($this->worker->connections as $conn) {
					$conn -> send(json_encode($new_message));
				}
			}
			if((($next_time-time()<$st_time && $next_time-time()>$st_time-200) || (time() > strtotime("01:54:00") && time() < strtotime("01:57:00"))) && F('is_send')==0){
				//结算
				//开奖结果
				$current_number = M('number')->where("game = 'ssc'")->order('id DESC')->find();
				$number1 = explode(',', $current_number['awardnumbers']);
				
				for($i=0;$i<count($number1);$i++){
					if($number1[$i] % 2 == 0){
						$number[$i]['ds'] = '双';
					}else{
						$number[$i]['ds'] = '单';
					}
					if($number1[$i]>=5){
						$number[$i]['dx'] = '大';
					}else{
						$number[$i]['dx'] = '小';
					}
				}	
				//当前局所有竞猜
				$today_time = strtotime(date('Y-m-d',time()));
				$list = M('order')->where("number = {$current_number['periodnumber']} && state = 1 && is_add = 0 && game = 'ssc'")->order("time ASC")->select();
				
				if($list){

					for($i=0;$i<count($list);$i++){
						$id = $list[$i]['id'];
						$userid = $list[$i]['userid'];
						if($list[$i]['t_id'] and C('fenxiao_set') == 2){
							M('user')->where("id = {$list[$i]['t_id']}")->setInc('yong', $list[$i]['del_points']*C('fenxiao')*0.01);
							M('user')->where("id = {$list[$i]['t_id']}")->setInc('t_add', $list[$i]['del_points']*C('fenxiao')*0.01);
							$fx_data = array(
								'uid' => $userid,
								't_uid' => $list[$i]['t_id'],
								'orderid' => $id,
								'push_money' => $list[$i]['del_points'],
								'rate' => C('fenxiao')*0.01,
								'money' => $list[$i]['del_points']*C('fenxiao')*0.01,
								'time' => time()
							);
							M('push_money')->add($fx_data);
						}

						$set_add = M('order')->where("id={$id}")->setField(array('is_add'=>1));
						
						//分类
						switch($list[$i]['type']){
							//定位球(12345/89/20)
							case 1:
								$start1 = explode('/', $list[$i]['jincai']);

								if (count($start1) == 3) {
									$chehao1 = str_split($start1[1]);
									$starts1 = str_split($start1[0]);
									$ya2 = $start1[2];
								} else {
									$chehao1 = str_split($start1[0]);
									$starts1 = str_split('1');
									$ya2 = $start1[1];
								}
								
								$num1 = 0;
								for($s=0;$s<count($chehao1);$s++){
									for($a=0;$a<count($starts1);$a++){
										$hao1 = $starts1[$a]-1;
										if($chehao1[$s]==$number1[$hao1]){
											$num1++;
										}
									}
								}
								if($num1>0){
									$points1 = $num1*$ya2*C('ssc_dwq');
									$res1 = $this->add_points($id,$userid,$points1);
									if($res1){
										$this->send_msg('pointsadd',$points1,$userid);
									}
								} 
								break;

							//龙虎合(龙100)
							case 2:
								$start2 = substr($list[$i]['jincai'], 0,3);
								$starts2 = substr($list[$i]['jincai'], 3);
								$num2 = 0;
								if ($start2 == $current_number['lh']) {
										$num2 = 1;
								}

								if($num2>0){
									if ($start2 == '龙' || $start2 == '虎') {
										$points2 = $num2*$starts2*C('ssc_lhh_1');
									} else if($start2 == '合') {
										$points2 = $num2*$starts2*C('ssc_lhh_2');
									}

									$res2= $this->add_points($id,$userid,$points2);
									if($res2){
										$this->send_msg('pointsadd',$points2,$userid);
									}
								}
								break;
								
								
							
							//定位大小单双(12345/双/100)
							case 3:
								$start3 = explode('/', $list[$i]['jincai']);
								$num3 = 0;
								$starts3= str_split($start3[0]);
								if($start3[1]=='单' || $start3[1]=='双'){
									for($a=0;$a<count($starts3);$a++){
										$hao3 = $starts3[$a]-1;
										if($number[$hao3]['ds']==$start3[1]){
											$num3++;
										}
									}
								}else{
									for($a=0;$a<count($starts3);$a++){
										$hao3 = $starts3[$a]-1;
										if($number[$hao3]['dx']==$start3[1]){
											$num3++;
										}
									}
								}
								if($num3>0){
									$points3 = $num3*$start3[2]*C('ssc_dxds');
									$res3 = $this->add_points($id,$userid,$points3);
									if($res3){
										$this->send_msg('pointsadd',$points3,$userid);
									}
								}
								break;
						
							//总数大小单双  大100  小100  
							case 4:
								$start4 = substr($list[$i]['jincai'], 0,3);
								$starts4 = substr($list[$i]['jincai'],3);
								$num4 = 0;

								if ($start4 == '大' || $start4 == '小') {
									if ($start4 == $current_number['tema_dx']) {
										$num4++;
									}
								} else {
									if ($start4 == $current_number['tema_ds']) {
										$num4++;
									}
								}

								if($num4>0){
									$points4 = $num4*$starts4*C('ssc_zdxds');
									$res4 = $this->add_points($id,$userid,$points4);
									if($res4){
										$this->send_msg('pointsadd',$points4,$userid);
									}
								}
								break;

							//前中后  前豹子100  中顺子100
							case 5:
								$start5 = substr($list[$i]['jincai'], 0,3); 
								$starts5 = substr($list[$i]['jincai'], 3,6);
								$money5 = substr($list[$i]['jincai'], 9);
								$num5 = 0;

								if ($start5 == '前') {
									if ($starts5 == $current_number['q3']) {
										$num5 = 1;
									}
								} else if($start5 == "中") {
									if ($starts5 == $current_number['z3']) {
										$num5 = 1;
									}
								} else if($start5 == '后') {
									if ($starts5 == $current_number['h3']) {
										$num5 = 1;
									}
								}

								if($num5>0){
									if ($starts5 == '豹子') {
										$points5 = $num5*$money5*C('ssc_qzh_1');
									} 
									if ($starts5 == '顺子') {
										$points5 = $num5*$money5*C('ssc_qzh_2');
									} 
									if ($starts5 == '对子') {
										$points5 = $num5*$money5*C('ssc_qzh_3');
									} 
									if ($starts5 == '半顺') {
										$points5 = $num5*$money5*C('ssc_qzh_4');
									} 
									if ($starts5 == '杂六') {
										$points5 = $num5*$money5*C('ssc_qzh_5');
									} 

									$res5 = $this->add_points($id,$userid,$points5);
									if($res5){
										$this->send_msg('pointsadd',$points5,$userid);
									}
								} 
								break;
						
							//和值特码区段(BC/100)
							case 6:
								$start6 = explode('/', $list[$i]['jincai']);
								$num6 = 0;
								if(strlen($start6[0])>1){
									$starts6 = str_split($start6[0]);
									for($a=0;$a<count($starts6);$a++){
										if($current_number['tema_dw']==$starts6[$a]){
											if($starts6[$a]=='A' || $starts6[$a]=='C'){
												$points6 = $start6[1]*C('ssc_tema_qd_1');
											}else{
												$points6 = $start6[1]*C('ssc_tema_qd_2');
											}
											$num9 = 1;
										}
									}
								}else{
									if($current_number['tema_dw']==$start6[0]){
										if($start6[0]=='A' || $start6[0]=='C'){
											$points6 = $start6[1]*C('ssc_tema_qd_1');
										}else{
											$points6 = $start6[1]*C('ssc_tema_qd_2');
										}
										$num6 = 1;
									}
								}
								if($num6>0 && $points6){
									$res6 = $this->add_points($id,$userid,$points6);
									if($res6){
										$this->send_msg('pointsadd',$points6,$userid);
									}
								}
								break;							
						
						//二字玩法(13/13.37/100)
						case 7:
							$start7 = explode('/', $list[$i]['jincai']);
							$num71 = 0;
							$num72 = 0;
					
							$zu = explode('.', $start7[1]);
							$zu1 = str_split($zu[0]);
							$zu2 = str_split($zu[1]);
							$ws = str_split($start7[0]);
							for($a=0;$a<count($zu1);$a++){
								if ($number1[$ws[0]-1] == $zu1[$a]) {
									$num71++;
								}
							}
							for($b=0;$b<count($zu2);$b++){
								if ($number1[$ws[1]-1] == $zu2[$b]) {
									$num72++;
								}
							}
							if($num71>0 && $num72>0){
								$points7 = $num71*$num72*$start7[2]*C('ssc_he');
								$res7 = $this->add_points($id,$userid,$points7);
								if($res7){
									$this->send_msg('pointsadd',$points7,$userid);
								}
							}
							break;
						}
					}
				}
				
				F('is_send',1);
				F('ssc_state',0);
				$content = $current_number['periodnumber']."期结算已完毕！<br/>
							号码：".$current_number['awardnumbers'];
				$new_message = array(
					'delay' => '20',
					'type' => 'system',
					'head_img_url'=>'/Public/main/img/system.jpg',
					'from_client_name' => '客服',
					'content'=> $content,
					'time'=>date('H:i:s')
				);
				foreach ($this->worker->connections as $conn) {
					$conn -> send(json_encode($new_message));
				}


				$new_message = array(
					'delay'=>'0',
					'type' => 'admin',
					'head_img_url'=>'/Public/main/img/kefu.jpg',
					'from_client_name' => 'GM管理员',
					'content'=>'期号:'.$sscdata['next']['periodNumber'].'开放，祝各位中大奖',
					'time'=>date('H:i:s')
				);
				
	            foreach ($this->worker->connections as $conn) {
					$conn -> send(json_encode($new_message));
				}


			}
			$this->add_message($new_message);/*添加信息*/
    	});
		

		
		//ping 统计人数
		\Workerman\Lib\Timer::add($time_interval, function(){
        	//ping客户端(获取房间内所有用户列表 )
            $clients_list = $this->worker->connections;
			$num = count($clients_list);
			$new_message = array(
				'type' => 'ping', 
				'content' => $num,
				'time' => date('H:i:s')
			);
			//if($num!=F('online')){
				//F('online',$num);
				foreach ($this->worker->connections as $conn) {
					$conn -> send(json_encode($new_message));
				}
			//}
    	});
		
		//系统公告
		/* \Workerman\Lib\Timer::add(300, function(){
			$new_message = array(
				'type' => 'system',
				'head_img_url'=>'/Public/main/img/system.jpg',
				'from_client_name' => '客服',
				'content'=>'由于各地网络情况不同，开奖动画仅作为参考，可能存在两秒的误差，不影响最终开奖结果！',
				'time'=>date('H:i:s')
			);
			foreach ($this->worker->connections as $conn) {
				$conn -> send(json_encode($new_message));
			}
			
    	}); */
		
		//存每期结果
		\Workerman\Lib\Timer::add(5, function(){
			$caiji = M('caiji')->where("game='ssc'")->limit(0,1)->order("id desc")->find();
			$data = json_decode(ssc_format($caiji),true);
		
			if(F('periodNumber')!=$data['current']['periodNumber']){
				$today_time = date('Y-m-d',time()) . " 00:00:00";
				$res = M('number')->where("periodnumber = '{$data['current']['periodNumber']}' and awardtime > '{$today_time}'")->find();
				if(!$res){
					$map['awardnumbers'] = $data['current']['awardNumbers'];
					$map['awardtime'] = $data['current']['awardTime'];
					$map['time'] = strtotime($data['current']['awardTime']);
					$map['periodnumber'] = $data['current']['periodNumber'];
					$info = explode(',', $map['awardnumbers']);
				
					//龙虎合
					$map['number'] = serialize($info);
					if($info[0]>$info[4]){
						$map['lh'] = '龙';
					}else if($info[0] == $info[4]){
						$map['lh'] = '合';
					}else{
						$map['lh'] = '虎';
					}
					$map['tema'] = $info[0]+$info[1]+$info[2]+$info[3]+$info[4];

					//大小单双
					if($map['tema'] % 2 == 0){
						$map['tema_ds'] = '双';
					}
					else{
						$map['tema_ds'] = '单';
					}
				
					if($map['tema']>=23){
						$map['tema_dx'] = '大';
					}else{
						$map['tema_dx'] = '小';
					}

					//区段
					if($map['tema']>=0 && $map['tema']<=15){
						$map['tema_dw'] = 'A';
					}
					if($map['tema']>=16 && $map['tema']<=29){
						$map['tema_dw'] = 'B';
					}
					if($map['tema']>=30 && $map['tema']<=45){
						$map['tema_dw'] = 'C';
					}
				
					//前中后
					$map['q3'] = ssc_qzh(array($info[0],$info[1],$info[2]));
					$map['z3'] = ssc_qzh(array($info[1],$info[2],$info[3]));
					$map['h3'] = ssc_qzh(array($info[2],$info[3],$info[4]));

					$map['game'] = $data['game'];
					$res1 = M('number')->add($map);
					if($res1){
						F('periodNumber',$data['current']['periodNumber']);
						F('sscdata',$data);
						F('is_send',0);

						//采集到开奖数据，客服发布通知
						// $content = "开奖采集数据,请等待系统开奖结算<br/>
						// 			期号：".$map['periodnumber']." <br/>
						// 			号码：".$map['awardnumbers'];
						// $new_message = array(
						// 	'type' => 'system',
						// 	'head_img_url'=>'/Public/main/img/system.jpg',
						// 	'from_client_name' => '客服',
						// 	'content'=> $content,
						// 	'time'=>date('H:i:s')
						// );
						// foreach ($this->worker->connections as $conn) {
						// 	$conn -> send(json_encode($new_message));
						// }
						// $this->add_message($new_message);
					}
				}
			}
    	});

	}
	
	/*
	 * 客户端连接时
	 * 
	 * */
	public function onConnect($connection){
		$connection->onWebSocketConnect = function($connection , $http_header)
		{
        
		};
	}
	
	/**
	 * onMessage
	 * @access public
	 * 转发客户端消息
	 * @param  void
	 * @param  void
	 * @return void
	 */
	public function onMessage($connection, $data) {
		$user = session('user');
		
		// 客户端传递的是json数据
		$message_data = json_decode($data, true);
		if (!$message_data) {
			return;
		}
				
		// 1:表示执行登陆操作 2:表示执行说话操作 3:表示执行退出操作
		// 根据类型执行不同的业务
		switch($message_data['type']){
			// 客户端回应服务端的心跳
			case 'pong' :
				break;
			case 'login' :
				// 把昵称放到session中
				$client_name = htmlspecialchars($message_data['client_name']);
				
				/* 保存uid到connection的映射，这样可以方便的通过uid查找connection，
		        * 实现针对特定uid推送数据
		        */
				$connection->uid = $message_data['client_id'];
				$this->worker->uidConnections[$connection->uid] = $connection;
				
				//session($connection->uid,$client_name);
				
				/* $new_message = array(
					'type' => 'admin',
					'head_img_url'=>'/Public/main/img/kefu.jpg',
					'from_client_name' => 'GM管理员',
					'content' => C('welcome'), 
					'time' => date('H:i:s')
				);
				$connection -> send(json_encode($new_message)); */
				break;	
			case 'say' :
				$userid = $connection->uid;
				/*是否竞猜时间*/
				$ssc_state = F('ssc_state');
				if($ssc_state == 0){
					$time_error_message = array(
						'uid'  => $connection->uid,
						'type' => 'say',
						'head_img_url'=>$message_data['headimgurl'],
						'from_client_name' => $message_data['client_name'],
						'content' => $message_data['content'], 
						'time' => date('H:i:s')
					);
					$connection -> send(json_encode($time_error_message));
					$time_error_message['type'] = 'say_error'; 
					$this->add_message($time_error_message);/*添加信息*/
					
					$time_message = array(
						'uid'  => $connection->uid,
						'type' => 'admin',
						'head_img_url'=>'/Public/main/img/kefu.jpg',
						'from_client_name' => 'GM管理员',
						'content' => '「'.$message_data['content'].'」'.'非竞猜时间，竞猜失败', 
						'time' => date('H:i:s')
					);
					$connection -> send(json_encode($time_message));
					$time_message['type'] = 'error';
					$this->add_message($time_message);/*添加信息*/
				}else{
					$time = time();
					if (F('game') == 'ssc' && $time > strtotime("02:00:00") &&$time < strtotime("10:00:00")) {
						$content_msg =  "重庆时时彩未开盘，请等待开盘";
						$new_message = array(
							'uid'  => $connection->uid,
							'type' => 'admin',
							'head_img_url'=>'/Public/main/img/kefu.jpg',
							'from_client_name' => 'GM管理员',
							'content' => $content_msg,
							'time' => date('H:i:s')
						);
						$connection -> send(json_encode($new_message));
						$new_message['type'] = 'error';
						$this->add_message($new_message);/*添加信息*/
						break;
					}
				
					/*检测格式和金额*/
					$res = check_format_ssc($message_data['content'],C('sscqi_min_point'),C('sscqi_max_point'),C('ssc_xz_open'),C('ssc_xz_max'));
					if($res['error']==0 || $res['error']==2){
						$error_message = array(
							'uid'=>$connection->uid,
							'type' => 'say',
							'head_img_url'=> $message_data['headimgurl'],
							'from_client_name' => $message_data['client_name'],
							'content' => $message_data['content'], 
							'time' => date('H:i:s')
						);
						$connection -> send(json_encode($error_message));
						$error_message['type'] = 'say_error';
						$this->add_message($error_message);/*添加信息*/
						
						if ($res['error'] == 0) {
							$content_msg =  '「'.$message_data['content'].'」'.'单笔点数最低'.C('ssc_min_point').',竞猜失败';
						} else {
							$content_msg =  '「'.$message_data['content'].'」'.'单笔点数最高'.$res['xz_max'].',竞猜失败';
						}

						$new_message = array(
							'uid'  => $connection->uid,
							'type' => 'admin',
							'head_img_url'=>'/Public/main/img/kefu.jpg',
							'from_client_name' => 'GM管理员',
							'content' => $content_msg,
							'time' => date('H:i:s')
						);
						$connection -> send(json_encode($new_message));
						$new_message['type'] = 'error';
						$this->add_message($new_message);/*添加信息*/
					}else if($res['type']){
						/*查询积分*/
						$jifen = M('user')->where("id = $userid")->find();
						if($jifen['points']<$res['points']){
							$points_error = array(
								'uid'=>$connection->uid,
								'type' => 'say',
								'head_img_url'=> $message_data['headimgurl'],
								'from_client_name' => $message_data['client_name'],
								'content' => $message_data['content'], 
								'time' => date('H:i:s')
							);
							$connection -> send(json_encode($points_error));
							$points_error['type'] = 'say_error';
							$this->add_message($points_error);/*添加信息*/
							
							$points_tips = array(
								'uid'  => $connection->uid,
								'type' => 'admin',
								'head_img_url'=>'/Public/main/img/kefu.jpg',
								'from_client_name' => 'GM管理员',
								'content' => '「'.$message_data['content'].'」'.'点数不足，竞猜失败', 
								'time' => date('H:i:s')
							);
							$connection -> send(json_encode($points_tips));
							$points_tips['type'] = 'error';
							$this->add_message($points_tips);/*添加信息*/
						}else{
							$sscdata = F('sscdata');
							$user = M('user')->where("id = $userid")->find();
							//当前玩法是否超过设置金额
							$wf_points = M('order')->field("sum(del_points) as sum_del")->where("userid = {$userid} and type={$res['type']} and state=1  and number = {$sscdata['next']['periodNumber']}")->find();

							$wf_max_points = 0;
							switch ($res['type']) {
								case '1':
									$wf_max_points = C('ssc_xz_max')['dwq'];
									break;

								case '2':
									$wf_max_points = C('ssc_xz_max')['lhh'];
									break;

								case '3':
									$wf_max_points = C('ssc_xz_max')['dxds'];
									break;

								case '4':
									$wf_max_points = C('ssc_xz_max')['zdxds'];
									break;

								case '5':
									$wf_max_points = C('ssc_xz_max')['qzh'];
									break;

								case '6':
									$wf_max_points = C('ssc_xz_max')['tema_qd'];
									break;
								case '7':
									$wf_max_points = C('ssc_xz_max')['he'];
									break;
								
								default:
									$wf_max_points = 0;
									break;
							}

							//车号限制  123/23/100
							if ($res['type'] == '1') {
								$type_list = M('order')->where("userid = {$userid} and state=1  and number = {$sscdata['next']['periodNumber']} and type = 1")->select();
								$type_data = array();
								$type_list[] = array('jincai'=>$message_data['content']);
								foreach ($type_list as $key => $value) {
									$type_str = $value['jincai'];
									$type_jincai = explode('/', $value['jincai']);
									if (count($type_jincai) == 2) {
										$type_str = '1/'.$type_str;
									}
									$ex_arr = explode('/', $type_str);
									$mc = str_split($ex_arr[0]);
									$ch = str_split($ex_arr[1]);
									foreach ($mc as  $b) {
										foreach ($ch as  $n) {
											$type_data['n'.$b]['m'.$n]+=$ex_arr[2];
											if ($type_data['n'.$b]['m'.$n] > $wf_max_points) {
												$points_tips = array(
													'uid'  => $connection->uid,
													'type' => 'admin',
													'head_img_url'=>'/Public/main/img/kefu.jpg',
													'from_client_name' => 'GM管理员',
													'content' => '「'.$message_data['content'].'」'.'定位球玩法，每个数字最高点数'.$wf_max_points.',您已超过，竞猜失败',
													'time' => date('H:i:s')
												);
												$connection -> send(json_encode($points_tips));
												$points_tips['type'] = 'error';
												$this->add_message($points_tips);/*添加信息*/
												return false;
											}
										}
									}
								}
							} else {
								if (($wf_points['sum_del'] + $res['points']) > $wf_max_points) {
									$points_tips = array(
										'uid'  => $connection->uid,
										'type' => 'admin',
										'head_img_url'=>'/Public/main/img/kefu.jpg',
										'from_client_name' => 'GM管理员',
										'content' => '「'.$message_data['content'].'」'.'
										当前玩法每期最高点数'.$wf_max_points.',您已超过，竞猜失败',
										'time' => date('H:i:s')
									);
									$connection -> send(json_encode($points_tips));
									$points_tips['type'] = 'error';
									$this->add_message($points_tips);/*添加信息*/
									break;
								}
							}

							if (($wf_points['sum_del'] + $res['points']) > $wf_max_points) {
								$points_tips = array(
									'uid'  => $connection->uid,
									'type' => 'admin',
									'head_img_url'=>'/Public/main/img/kefu.jpg',
									'from_client_name' => 'GM管理员',
									'content' => '「'.$message_data['content'].'」'.'
									当前玩法每期最高点数'.$wf_max_points.',您已超过，竞猜失败',
									'time' => date('H:i:s')
								);
								$connection -> send(json_encode($points_tips));
								$points_tips['type'] = 'error';
								$this->add_message($points_tips);/*添加信息*/
								break;
							}

							//查看已投注金额
							$user_points = M('order')->field("sum(del_points) as sum_del")->where("userid = {$userid} and state=1  and number = {$sscdata['next']['periodNumber']}")->find();

							if ((intval($user_points['sum_del'])+$res['points']) > C('sscqi_max_point')) {
								$points_tips = array(
									'uid'  => $connection->uid,
									'type' => 'admin',
									'head_img_url'=>'/Public/main/img/kefu.jpg',
									'from_client_name' => 'GM管理员',
									'content' => '「'.$message_data['content'].'」'.'每期最高点数'.$res['sscqi_max_points'].',您已超过，竞猜失败',
									'time' => date('H:i:s')
								);
								$connection -> send(json_encode($points_tips));
								$points_tips['type'] = 'error';
								$this->add_message($points_tips);/*添加信息*/
								break;
							}

							$map['userid'] = $userid;
							$map['type'] = $res['type'];
							$map['state'] = 1;
							$map['is_add'] = 0;
							$map['is_under'] = $user['is_under'];
							$map['time'] = time();
							$map['number'] = $sscdata['next']['periodNumber'];
							$map['jincai'] = $message_data['content'];
							$map['del_points'] = $res['points'];
							$map['balance'] = $user['points']-$map['del_points'];
							$map['nickname'] = $message_data['client_name'];
							$map['username'] = $user['username'];
							$map['game'] = F('game');
							$map['t_id'] = $user['t_id'];
							
							/*添加竞猜*/
							if ($user['is_robot']) {
								$return = true;
							} else {
								$return = M('order')->add($map);
							}
							if($return){
								/*减分*/
								if (!$user['is_robot']) {
									$res_points = M('user')->where("id = $userid")->setDec('points',$map['del_points']);
									
									if($res_points){
										$points_del = array(
											'type' => 'points',
											'content' => $res['points'], 
											'time' => date('H:i:s')
										);
										$connection -> send(json_encode($points_del));
									}
								}

								$new_message2 = array(
									'uid'=>$connection->uid,
									'type' => 'say',
									'status' => $user['is_under'],
									'head_img_url'=> $message_data['headimgurl'],
									'from_client_name' => $message_data['client_name'],
									'content' => $message_data['content'], 
									'time' => date('H:i:s')
								);
								if ($map['is_under']) {
									foreach ($this->worker->uidConnections as $con) {
										$con -> send(json_encode($new_message2));
									}
								}
								
								$add_return = $this->add_message($new_message2);/*添加信息*/
								
								if($add_return){
									/*成功通知*/
									$new_message1 = array(
										'uid'  => $connection->uid,
										'type' => 'admin',
										'head_img_url'=>'/Public/main/img/kefu.jpg',
										'from_client_name' => 'GM管理员',
										'content' => '「'.$message_data['content'].'」'.',竞猜成功', 
										'time' => date('H:i:s')
									);
									$connection -> send(json_encode($new_message1));
									$new_message1['type'] = 'error';
									$this->add_message($new_message1);/*添加信息*/
								}
							}
						}		
					}else{
						if (substr($message_data['content'],0,1) == '@') {
							$uid_info = explode('|',$message_data['content'])[0];
							$uid = substr($uid_info,1);
							$user = M('user')->where("id={$uid}")->find();
							if ($user) {
								$new_message6 = array(
									'uid'=>$user['id'],
									'type' => 'say',
									'status' => 1,
									'head_img_url'=> $message_data['headimgurl'],
									'from_client_name' => $message_data['client_name'],
									'content' => $message_data['content'], 
									'time' => date('H:i:s')
								);
								foreach ($this->worker->uidConnections as $con) {
									$con -> send(json_encode($new_message6));
								}
							}
						}

						if (C('is_say')) {
							$new_message2 = array(
								'uid'=>$connection->uid,
								'type' => 'say',
								'status' => 1,
								'head_img_url'=> $message_data['headimgurl'],
								'from_client_name' => $message_data['client_name'],
								'content' => $message_data['content'], 
								'time' => date('H:i:s')
							);
							foreach ($this->worker->uidConnections as $con) {
								$con -> send(json_encode($new_message2));
							}
						} else {
							$format_error_message = array(
								'uid'  => $connection->uid,
								'type' => 'say',
								'head_img_url'=>$message_data['headimgurl'],
								'from_client_name' => $message_data['client_name'],
								'content' => $message_data['content'], 
								'time' => date('H:i:s')
							);
							$connection -> send(json_encode($format_error_message));
							$format_error_message['type'] = 'say_error';
							$this->add_message($format_error_message);/*添加信息*/
							
							$new_message3 = array(
								'uid'  => $connection->uid,
								'type' => 'admin',
								'head_img_url'=>'/Public/main/img/kefu.jpg',
								'from_client_name' => 'GM管理员',
								'content' => '「'.$message_data['content'].'」'.'格式不正确,竞猜失败', 
								'time' => date('H:i:s')
							);
							$connection -> send(json_encode($new_message3));
							$new_message3['type'] = 'error';
							$this->add_message($new_message3);/*添加信息*/
						}
						
					}
				}
				break;
				
			case 'robot':
				if(C('robot')==1){
					$mess = $this->robot_message();
					$robot = $this->robot();
					$ssc_state = F('ssc_state');
					$new_message = array(
						'type' => 'say',
						'head_img_url'=>'/Uploads'.$robot[0]['headimgurl'],
						'from_client_name' => $robot[0]['nickname'],
						'content'=>$mess[0]['content'],
						'time'=>date('H:i:s')
					);
					if($ssc_state==1){
						foreach ($this->worker->uidConnections as $con) {
							$con -> send(json_encode($new_message));
						}
						$this->add_message($new_message);
					}
			}
				break;	
		}
	}
	
	
	public function robot_message(){
		$count = M('robot_message')->where("type=2")->count();
     	$rand = mt_rand(0,$count-1); //产生随机数。
     	$limit = $rand.','.'1'; 
    	$data = M('robot_message')->where("type=2")->limit($limit)->select();  
		return $data;
	}
	
	public function robot(){
		$count = M('robot')->count();
     	$rand = mt_rand(0,$count-1); //产生随机数。
     	$limit = $rand.','.'1'; 
    	$data = M('robot')->limit($limit)->select();  
		return $data;
	}
	
	/**
	 * onClose
	 * 关闭连接
	 * @access public
	 * @param  void
	 * @return void
	 */
	public function onClose($connection) {
		$user = session($connection->id);
		foreach ($this->worker->uidConnections as $con) {
			if (!empty($user)) {
				$new_message = array(
					'type' => 'logout',
					'from_client_name' => $user,
					'time' => date('H:i:s')
				);
				$con -> send(json_encode($new_message));
			}
		}
		
		if(isset($connection->uid)){
	        // 连接断开时删除映射
	        unset($this->worker->uidConnections[$connection->uid]);
    	}
	}
	
	
	/*存竞猜记录和信息*/
	protected function add_order($mew_message){
		$res = M('order')->add($mew_message);
		return $res;
	}

	protected function add_message($new_message){
		if (!empty($new_message)) {
			$new_message['game'] = 'ssc';
			$res = M('message')->add($new_message);
			return $res;
		}
		
	}
	
	
	/*
	 * 竞猜成功  加分
	 * */
	public function add_points($order_id,$userid,$points){
		$res = M('user')->where("id = $userid")->setInc('points',$points);
		if($res){
			$res1 = M('order')->where("id = $order_id")->setField(array('is_add'=>'1','add_points'=>$points));
		}
		if($res && $res1){
			return 1;
		}
	}
	
	/*竞猜成功通知
	 * */
	public function send_msg($type,$points,$userid){
		$message_points = array(
			'type' => $type,
			'points'=>	$points,
			'time'=>date('H:i:s')
		);
		if(isset($this->worker->uidConnections[$userid])){
	        $connection = $this->worker->uidConnections[$userid];
	        $connection->send(json_encode($message_points));
	    }
	} 

	
	
	
}
?>