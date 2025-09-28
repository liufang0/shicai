<?php
 
namespace Home\Controller;
use Think\Server;

header('content-type:text/html;charset=utf-8');
class Workermanbj28Controller extends Server {

	protected $socket = 'websocket://0.0.0.0:15534';
	
	/*添加定时器
	 *监控连接状态
	 * */
	public function onWorkerStart(){
		$auth = auth_check(C('auth_code'),C('siteurl'));
		// if (!$auth) {
			// echo "未授权或授权已过期";exit;
		// }

		$beginToday=strtotime('00:00:00');
		$endToday=strtotime("23:54:59");
		$caiji = M('caiji')->where("game='bj28'")->limit(0,1)->order("id desc")->find();
		$data =  bj28_format($caiji);
		
		$time_interval = 1;
		$bj28data = json_decode($data,true);
		$nexttime = $bj28data['next']['delayTimeInterval']+strtotime($bj28data['next']['awardTime']);
		 
		if($nexttime-time()>C('bj28_stop_time') && $nexttime-time()<285){
			F('bj28_state',1);
			setconfig('bj28_state',1);
		}else{
			F('bj28_state',0);
			setconfig('bj28_state',0);
		}
		if(!F('bj28data')){
			F('bj28data',$bj28data);
		}
		if(!F('is_send')){
			F('is_send',1);
		}
		echo F('bj28_state');
		/*开奖*/
		\Workerman\Lib\Timer::add($time_interval, function(){
		
			$beginToday=strtotime('00:00:00');
			$endToday=strtotime("23:59:59");
			F('game','bj28');

			$bj28data = F('bj28data');
			$next_time = $bj28data['next']['delayTimeInterval']+strtotime($bj28data['next']['awardTime']);
			$awardtime = $bj28data['current']['awardTime'];
			// var_dump($next_time-time());
			if($next_time-time()>C('bj28_stop_time') && $next_time-time()<280){
				F('bj28_state',1);
				setconfig('bj28_state',1);
			}else{
				F('bj28_state',0);
				setconfig('bj28_state',0);
			}
			
			if($next_time-time()== 30+C('bj28_stop_time')){
				$new_message = array(
					'type' => 'admin',
					'head_img_url'=>'/Public/main/img/kefu.jpg',
					'from_client_name' => 'GM管理员',
					'content'=>$bj28data['next']['periodNumber'].'期距离封盘还有30秒,请各位玩家抓紧下注',
					'time' => date('Y-m-d H:i:s')
				);
				foreach ($this->worker->connections as $conn) {
					$conn -> send(json_encode($new_message));
				}
			}

			if($next_time-time()==C('bj28_stop_time')){
				F('bj28_state',0);
				setconfig('bj28_state',0);
				$new_message = array(
					'type' => 'admin',
					'head_img_url'=>'/Public/main/img/kefu.jpg',
					'from_client_name' => 'GM管理员',
					'content'=>$bj28data['next']['periodNumber'].'期已封盘,请各位玩家停止下注',
					'time' => date('Y-m-d H:i:s')
				);
				foreach ($this->worker->connections as $conn) {
					$conn -> send(json_encode($new_message));
				}
				$this->add_message($new_message);/*添加信息*/
			}
			
			if($next_time-time()<285 && $next_time-time()>100 && F('is_send')==0){
				//结算
				//开奖结果
				$current_number = M('number')->where("game='bj28'")->order('id DESC')->find();
				//$number1 = explode(',', $current_number['awardnumbers']);
				//$tema_number = $number1[0] + $number1[1] + $number1[2];
				$tema_number = $current_number['tema'];
				if ($tema_number <= 13) {
					if ($tema_number%2 == 0) {
						$current_number['zuhe'] = '小双';
					} else {
						$current_number['zuhe'] = '小单';
					}
				} else {
					if ($tema_number%2 == 0) {
						$current_number['zuhe'] = '大双';
					} else {
						$current_number['zuhe'] = '大单';
					}
				}

				if ($tema_number >=0 && $tema_number <=5) {
					$current_number['jdx'] = '极小';
				} else if($tema_number >= 22 && $tema_number <=27) {
					$current_number['jdx'] = '极大';
				}  else {
					$current_number['jdx'] = '';
				}

				//当前局所有竞猜
				$today_time = strtotime(date('Y-m-d',time()));
				$list = M('order')->where("number = {$current_number['periodnumber']} && time > '{$today_time}' && state = 1 && is_add = 0 && game='bj28'")->order("time ASC")->select();
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
						
						$fangjian=$list[$i]['fangjian'];
						if($fangjian=='a'){
							$fangjian='';
						}
						echo $fangjian;
						echo C('bj28'.$fangjian.'_dxds');
						//分类
						switch($list[$i]['type']){
							
							//大小单双  大100  小100  
							case 1:
								$start1 = substr($list[$i]['jincai'], 0,3);
								$starts1 = substr($list[$i]['jincai'],3);
								$num1 = 0;

								if ($start1 == '大' || $start1 == '小') {
									if ($start1 == $current_number['tema_dx']) {
										$num1 = 1;
									}
								} else {
									if ($start1 == $current_number['tema_ds']) {
										$num1 = 1;
									}
								}

								if($num1>0){
									if ($current_number['tema'] == '13' || $current_number['tema'] == '14') {
										if(C('bj28'.$fangjian.'_1314_open') == '1'){
											$user_points = M('order')->field("sum(del_points) as sum_del")->where("userid = {$userid} and state=1  and number = {$current_number['periodnumber']}")->find();
											if(intval($user_points['sum_del']) < intval(C('bj28'.$fangjian.'_dxds_1314zz'))){
												$points1 = $num1*$starts1*C('bj28'.$fangjian.'_dxds_md1');
											}else if(intval($user_points['sum_del']) >= intval(C('bj28'.$fangjian.'_dxds_1314zz')) && intval($user_points['sum_del']) < intval(C('bj28'.$fangjian.'_dxds_1314zz2'))){
												$points1 = $num1*$starts1*C('bj28'.$fangjian.'_dxds_md2');
											}else if(intval($user_points['sum_del']) >= intval(C('bj28'.$fangjian.'_dxds_1314zz2'))){
												$points1 = $num1*$starts1*C('bj28'.$fangjian.'_dxds_md3');
											}
										}else{
											$points1 = $num1*$starts1*C('bj28'.$fangjian.'_dxds_md');
										}
									} else {
										$points1 = $num1*$starts1*C('bj28'.$fangjian.'_dxds');
									}
									
									$res1 = $this->add_points($id,$userid,$points1);
									if($res1){
										$this->send_msg('pointsadd',$points1,$userid);
									}
								}
								break;

							//组合  大单100  小100  
							case 2:
								$start2 = substr($list[$i]['jincai'], 0,6);
								$starts2 = substr($list[$i]['jincai'],6);
								$num2 = 0;

							
								if ($start2 == $current_number['zuhe']) {
									$num2 = 1;
								}

								if($num2>0){
									if ($current_number['tema'] == '13' || $current_number['tema'] == '14') {
										$points2 = $num2*$starts2*C('bj28'.$fangjian.'_zuhe_md');
									} else {
										if ($start2 == '大单' || $start2 == '小双'){
											$points2 = $num2*$starts2*C('bj28'.$fangjian.'_zuhe_1');
										} else {
											$points2 = $num2*$starts2*C('bj28'.$fangjian.'_zuhe_2');
										}
									}
									
									$res2 = $this->add_points($id,$userid,$points2);
									if($res2){
										$this->send_msg('pointsadd',$points2,$userid);
									}
								}
								break;


							//极大小  极大100  
							case 3:
								$start3 = substr($list[$i]['jincai'], 0,6);
								$starts3 = substr($list[$i]['jincai'],6);
								$num3 = 0;
								if ($start3 == $current_number['jdx']) {
									$num3 = 1;
								}

								if($num3>0){
									$points3 = $num3*$starts3*C('bj28'.$fangjian.'_jdx');
									$res3 = $this->add_points($id,$userid,$points3);
									if($res3){
										$this->send_msg('pointsadd',$points3,$userid);
									}
								}
								break;


							//庄闲和    庄100  和100
							case 4:
								$start4 = substr($list[$i]['jincai'], 0,3);
								$starts4 = substr($list[$i]['jincai'],3);

								$num4 = 0;

								if ($start4 == $current_number['zx']) {
									$num4 = 1;
								}

								if($num4 > 0 ){
									if ($start4 == '庄' || $start4 == '闲') {
										if ($current_number['zx'] == '和') {
											$points4 = $num4*$starts4*1;
										} else {
											$points4 = $num4*$starts4*C('bj28'.$fangjian.'_zx_1');
										}
									} else {
										$points4 = $num4*$starts4*C('bj28'.$fangjian.'_zx_2');
									}

									$res4 = $this->add_points($id,$userid,$points4);
									if($res4){
										$this->send_msg('pointsadd',$points4,$userid);
									}
								}
								break;


							//豹子对子顺子 100  白字100  
							case 5:
								$start5 = substr($list[$i]['jincai'], 0,6);
								$starts5 = substr($list[$i]['jincai'],6);
								$num5 = 0;
								if ($start5 == $current_number['q3']) {
									$num5 = 1;
								}

								if($num5>0){
									if ($start5 == '豹子') {
										$points5 = $num5*$starts5*C('bj28'.$fangjian.'_bds_1');
									} else if($start5 == '顺子') {
										$points5 = $num5*$starts5*C('bj28'.$fangjian.'_bds_2');
									} else if($start5 == '对子') {
										$points5 = $num5*$starts5*C('bj28'.$fangjian.'_bds_3');
									} else if($start5 == '半顺') {
										$points5 = $num5*$starts5*C('bj28'.$fangjian.'_bds_4');
									} else if($start5 == '杂六') {
										$points5 = $num5*$starts5*C('bj28'.$fangjian.'_bds_5');
									}
									
									$res5 = $this->add_points($id,$userid,$points5);
									if($res5){
										$this->send_msg('pointsadd',$points5,$userid);
									}
								}
								break;


							//特码数字 3点100  
							case 6:
								$start6 = explode('点', $list[$i]['jincai']);

								$num6 = 0;
								if ($start6[0] == $current_number['tema']) {
									$num6 = 1;
								}

								if($num6>0){
									if ($start6[0] == '0' || $start6[0] == '27') {
										$points6 = $num6*$start6[1]*C('bj28'.$fangjian.'_tema_0');
									} else if($start6[0] == '1' || $start6[0] == '26') {
										$points6 = $num6*$start6[1]*C('bj28'.$fangjian.'_tema_1');
									} else if($start6[0] == '2' || $start6[0] == '25') {
										$points6 = $num6*$start6[1]*C('bj28'.$fangjian.'_tema_2');
									} else if($start6[0] == '3' || $start6[0] == '24') {
										$points6 = $num6*$start6[1]*C('bj28'.$fangjian.'_tema_3');
									} else if($start6[0] == '4' || $start6[0] == '23') {
										$points6 = $num6*$start6[1]*C('bj28'.$fangjian.'_tema_4');
									} else if($start6[0] == '5' || $start6[0] == '22') {
										$points6 = $num6*$start6[1]*C('bj28'.$fangjian.'_tema_5');
									} else if($start6[0] == '6' || $start6[0] == '21') {
										$points6 = $num6*$start6[1]*C('bj28'.$fangjian.'_tema_6');
									} else if($start6[0] == '7' || $start6[0] == '20') {
										$points6 = $num6*$start6[1]*C('bj28'.$fangjian.'_tema_7');
									} else if($start6[0] == '8' || $start6[0] == '19') {
										$points6 = $num6*$start6[1]*C('bj28'.$fangjian.'_tema_8');
									} else if($start6[0] == '9' || $start6[0] == '18') {
										$points6 = $num6*$start6[1]*C('bj28'.$fangjian.'_tema_9');
									} else if($start6[0] == '10' || $start6[0] == '17') {
										$points6 = $num6*$start6[1]*C('bj28'.$fangjian.'_tema_10');
									} else if($start6[0] == '11' || $start6[0] == '16') {
										$points6 = $num6*$start6[1]*C('bj28'.$fangjian.'_tema_11');
									} else if($start6[0] == '12' || $start6[0] == '15') {
										$points6 = $num6*$start6[1]*C('bj28'.$fangjian.'_tema_12');
									} else if($start6[0] == '13' || $start6[0] == '14') {
										$points6 = $num6*$start6[1]*C('bj28'.$fangjian.'_tema_13');
									}	

									$res6 = $this->add_points($id,$userid,$points6);
									if($res6){
										$this->send_msg('pointsadd',$points6,$userid);
									}
								}
								break;					
						}
					}
				}
				
				F('is_send',1);
				F('bj28_state',0);

				
				$info = explode(',', $current_number['awardnumbers']);
				
				$numberOne = $info[0];
				$numberTwo = $info[1];
				$numberThree = $info[2];
					
					
				$content = $current_number['periodnumber']."期结算已完毕!号码：".$numberOne."+".$numberTwo."+".$numberThree."=".$current_number['tema'];
				$new_message = array(
					'delay' => '20',
					'type' => 'system',
					'head_img_url'=>'/Public/main/img/system.jpg',
					'from_client_name' => '客服',
					'content'=> $content,
					'time' => date('Y-m-d H:i:s')
				);
				/* foreach ($this->worker->connections as $conn) {
					$conn -> send(json_encode($new_message));
				} */
				//$this->add_message($new_message);/*添加信息*/
				
				$new_message = array(
					'delay'=>'0',
					'type' => 'admin',
					'head_img_url'=>'/Public/main/img/kefu.jpg',
					'from_client_name' => 'GM管理员',
					'content'=>$bj28data['next']['periodNumber'].'期已开盘,请各位玩家开始下注',
					'time' => date('Y-m-d H:i:s')
				);
				
	            foreach ($this->worker->connections as $conn) {
					$conn -> send(json_encode($new_message));
				}
				$this->add_message($new_message);/*添加信息*/
			}
			
    	});
		

		
		//ping 统计人数
		\Workerman\Lib\Timer::add($time_interval, function(){
        	//ping客户端(获取房间内所有用户列表 )
            $clients_list = $this->worker->connections;
			$num = count($clients_list);
			$new_message = array(
				'type' => 'ping', 
				'content' => $num,
				'time' => date('Y-m-d H:i:s')
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
				'time' => date('Y-m-d H:i:s')
			);
			foreach ($this->worker->connections as $conn) {
				$conn -> send(json_encode($new_message));
			}
			
    	}); */
		
		//存每期结果
		\Workerman\Lib\Timer::add(5, function(){
			$caiji = M('caiji')->where("game='bj28'")->limit(0,1)->order("id desc")->find();
			$data = json_decode(bj28_format($caiji),true);

			if(F('bj28PeriodNumber')!=$data['current']['periodNumber']){
				$today_time = date('Y-m-d',time()) . " 00:00:00";
				$res = M('number')->where("game = 'bj28' and periodnumber = {$data['current']['periodNumber']} and awardtime > '{$today_time}'")->find();
				// var_dump($res);
				if(!$res){
					$map['awardnumbers'] = $data['current']['awardNumbers'];
					$map['awardtime'] = $data['current']['awardTime'];
					$map['time'] = strtotime($data['current']['awardTime']);
					$map['periodnumber'] = $data['current']['periodNumber'];
					$map['number'] = $map['awardnumbers'];
					
					$info = explode(',', $map['awardnumbers']);
					$numberOne = $info[0];
					$numberTwo = $info[1];
					$numberThree = $info[2];
					
					$map['tema'] = $numberOne+$numberTwo+$numberThree ;

					if($map['tema'] % 2 == 0){
						$map['tema_ds'] = '双';
					}
					else{
						$map['tema_ds'] = '单';
					}
				
					if($map['tema']>=14){
						$map['tema_dx'] = '大';
					}else{
						$map['tema_dx'] = '小';
					}
				
					if($numberOne>$numberTwo){
						$map['zx'] = '庄';
					} else if($numberOne == $numberTwo){
						$map['zx'] = '和';
					}else{
						$map['zx'] = '闲';
					}

					$map['q3'] = bj28_qzh(array($numberOne,$numberTwo,$numberThree));

					$map['game'] = $data['game'];
					$res1 = M('number')->add($map);
					if($res1){
						F('bj28PeriodNumber',$data['current']['periodNumber']);
						F('bj28data',$data);
						F('is_send',0);
					}
				}else{

						F('bj28PeriodNumber',$data['current']['periodNumber']);
						F('bj28data',$data);
						F('is_send',0);
				

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
					'time' => date('Y-m-d H:i:s')
				);
				$connection -> send(json_encode($new_message)); */
				break;	
			case 'say' :
				$userid = $connection->uid;
				/*是否竞猜时间*/
				$bj28_state = F('bj28_state');
				if($bj28_state == 0){
					/* $time_error_message = array(
						'uid'  => $connection->uid,
						'type' => 'say',
						'head_img_url'=>$message_data['headimgurl'],
						'from_client_name' => $message_data['client_name'],
						'content' => $message_data['content'], 
						'time' => date('Y-m-d H:i:s')
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
						'time' => date('Y-m-d H:i:s')
					);
					$connection -> send(json_encode($time_message));
					$time_message['type'] = 'error';
					$this->add_message($time_message);/*添加信息*/
				}else{
					$time = time();
					if ($time > strtotime("23:55:30")) {
						$content_msg =  "螞蟻收益最后一期已关闭";
						$new_message = array(
							'uid'  => $connection->uid,
							'type' => 'admin',
							'head_img_url'=>'/Public/main/img/kefu.jpg',
							'from_client_name' => 'GM管理员',
							'content' => $content_msg,
							'time' => date('Y-m-d H:i:s')
						);
						$connection -> send(json_encode($new_message));
						$new_message['type'] = 'error';
						$this->add_message($new_message);/*添加信息*/
						break;
					} 
				
					/*检测格式和金额*/
					$res = check_format_bj28($message_data['content'],C('bj28qi_min_point'),C('bj28qi_max_point'),C('bj28_xz_open'),C('bj28_xz_max'));
					if($res['error']==0 || $res['error']==2){
						$error_message = array(
							'uid'=>$connection->uid,
							'type' => 'say',
							'head_img_url'=> $message_data['headimgurl'],
							'from_client_name' => $message_data['client_name'],
							'content' => $message_data['content'], 
							'time' => date('Y-m-d H:i:s')
						);
						$connection -> send(json_encode($error_message));
						$error_message['type'] = 'say_error';
						$this->add_message($error_message);/*添加信息*/
						
						if ($res['error'] == 0) {
							$content_msg =  '「'.$message_data['content'].'」'.'单笔点数最低'.C('bj28qi_min_point').',竞猜失败';
						} else {
							$content_msg =  '「'.$message_data['content'].'」'.'单笔点数最高'.$res['xz_max'].',竞猜失败';
						}

						$new_message = array(
							'uid'  => $connection->uid,
							'type' => 'admin',
							'head_img_url'=>'/Public/main/img/kefu.jpg',
							'from_client_name' => 'GM管理员',
							'content' => $content_msg,
							'time' => date('Y-m-d H:i:s')
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
								'time' => date('Y-m-d H:i:s')
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
								'time' => date('Y-m-d H:i:s')
							);
							$connection -> send(json_encode($points_tips));
							$points_tips['type'] = 'error';
							$this->add_message($points_tips);/*添加信息*/
						}else{
							$bj28data = F('bj28data');
							$user = M('user')->where("id = $userid")->find();
							//当前玩法是否超过设置金额
							$wf_points = M('order')->field("sum(del_points) as sum_del")->where("userid = {$userid} and type={$res['type']} and state=1  and number = {$bj28data['next']['periodNumber']}")->find();

							$wf_max_points = 1000000000000;
							// switch ($res['type']) {
								// case '1':
									// $wf_max_points = C('bj28_xz_max')['dxds'];
									// break;

								// case '2':
									// $wf_max_points = C('bj28_xz_max')['zuhe'];
									// break;

								// case '3':
									// $wf_max_points = C('bj28_xz_max')['jdx'];
									// break;

								// case '4':
									// $wf_max_points = C('bj28_xz_max')['zx'];
									// break;

								// case '5':
									// $wf_max_points = C('bj28_xz_max')['bds'];
									// break;

								// case '6':
									// $wf_max_points = C('bj28_xz_max')['tema'];
									// break;

								// default:
									// $wf_max_points = 0;
									// break;
							// }

							if (($wf_points['sum_del'] + $res['points']) > $wf_max_points) {
								$points_tips = array(
									'uid'  => $connection->uid,
									'type' => 'admin',
									'head_img_url'=>'/Public/main/img/kefu.jpg',
									'from_client_name' => 'GM管理员',
									'content' => '「'.$message_data['content'].'」'.'
									当前玩法每期最高点数'.$wf_max_points.',您已超过，竞猜失败',
									'time' => date('Y-m-d H:i:s')
								);
								$connection -> send(json_encode($points_tips));
								$points_tips['type'] = 'error';
								$this->add_message($points_tips);/*添加信息*/
								break;
							}

							//查看已投注金额
							$user_points = M('order')->field("sum(del_points) as sum_del")->where("userid = {$userid} and state=1  and number = {$bj28data['next']['periodNumber']}")->find();

							if ((intval($user_points['sum_del'])+$res['points']) > C('bj28qi_max_point')) {
								$points_tips = array(
									'uid'  => $connection->uid,
									'type' => 'admin',
									'head_img_url'=>'/Public/main/img/kefu.jpg',
									'from_client_name' => 'GM管理员',
									'content' => '「'.$message_data['content'].'」'.'每期最高点数'.$res['bj28qi_max_points'].',您已超过，竞猜失败',
									'time' => date('Y-m-d H:i:s')
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
							$map['number'] = $bj28data['next']['periodNumber'];
							$map['jincai'] = $message_data['content'];
							$map['del_points'] = $res['points'];
							$map['balance'] = $user['points']-$map['del_points'];
							$map['nickname'] = $message_data['client_name'];
							$map['username'] = $user['username'];
							$map['fangjian'] = $message_data['fangjian'];
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
											'time' => date('Y-m-d H:i:s')
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
									'time' => date('Y-m-d H:i:s')
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
										'time' => date('Y-m-d H:i:s')
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
									'time' => date('Y-m-d H:i:s')
								);
								// var_dump($new_message6);
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
								'time' => date('Y-m-d H:i:s')
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
								'time' => date('Y-m-d H:i:s')
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
								'time' => date('Y-m-d H:i:s')
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
					$bj28_state = F('bj28_state');
					$new_message = array(
						'type' => 'say',
						'head_img_url'=>'/Uploads'.$robot[0]['headimgurl'],
						'from_client_name' => $robot[0]['nickname'],
						'content'=>$mess[0]['content'],
						'time'=>date('Y-m-d H:i:s')
					);

					if($bj28_state==1){
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
		$count = M('robot_message')->where("type=3")->count();
     	$rand = mt_rand(0,$count-1); //产生随机数。
     	$limit = $rand.','.'1'; 
    	$data = M('robot_message')->where("type=3")->limit($limit)->select();  
		return $data;
	}
	
	public function robot(){
		$time = time() - 1800; //半小时之前
		$count = M('robot')->count();
     	$rand = mt_rand(0,$count-1); //产生随机数。
     	$limit = $rand.','.'1'; 
    	$data = M('robot')->limit($limit)->select();
    	//$qqe['is_game'] = 'bj28';
    	//$qqe['time'] = time();
    	//$res = M('robot')->where("id = {$data[0]['id']}")->save($qqe);
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
					'time' => date('Y-m-d H:i:s')
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
			$new_message['game'] = 'bj28';
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
			'time' => date('Y-m-d H:i:s')
		);
		if(isset($this->worker->uidConnections[$userid])){
	        $connection = $this->worker->uidConnections[$userid];
	        $connection->send(json_encode($message_points));
	    }
	} 
	
	public function SITE($name){
		$CC=include("Application\Common\Conf\site.php");
		return $CC[$name];
	}
	
	
}
?>