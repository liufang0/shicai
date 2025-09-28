<?php
namespace Home\Controller;
use Think\Server;
class WorkermanftController extends Server {
	protected $socket = 'websocket://0.0.0.0:15532';
	
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
		$caiji = M('caiji')->where("game='xyft'")->limit(0,1)->order("id desc")->find();
		$data =  xyft_format($caiji);
		
		$time_interval = 1;
		$pkdata = json_decode($data,true);
		$nexttime = $pkdata['next']['delayTimeInterval']+strtotime($pkdata['next']['awardTime']);
		if($nexttime-time()>C('ft_stop_time')-6 && $nexttime-time()<280){
			F('xyft_state',1);
			setconfig('xyft_state',1);
		}else{
			F('xyft_state',0);
			setconfig('xyft_state',0);
		}
		if(!F('xyftdata')){
			F('xyftdata',$pkdata);
		}
		if(!F('is_send')){
			F('is_send',1);
		}
		
		/*开奖*/
		\Workerman\Lib\Timer::add($time_interval, function(){
		
			$beginToday=strtotime('09:00:00');
			$endToday=strtotime("23:59:59");
			F('game','xyft');
			$xyftdata = F('xyftdata');
			$next_time = $xyftdata['next']['delayTimeInterval']+strtotime($xyftdata['next']['awardTime']);
			$awardtime = $xyftdata['current']['awardTime'];
			if($next_time-time()>C('ft_stop_time')-6 && $next_time-time()<280){
				F('xyft_state',1);
				setconfig('xyft_state',1);
			}else{
				F('xyft_state',0);
				setconfig('xyft_state',0);
			}
			
			if($next_time-time()==24+C('ft_stop_time')){
				$new_message = array(
					'type' => 'admin',
					'head_img_url'=>'/Public/main/img/kefu.jpg',
					'from_client_name' => 'GM管理员',
					'content'=>'期号:'.$xyftdata['next']['periodNumber'].'<br/>'.'--距离封盘还有30秒--',
					'time'=>date('H:i:s')
				);
				foreach ($this->worker->connections as $conn) {
					$conn -> send(json_encode($new_message));
				}
			}
			if($next_time-time()==C('ft_stop_time')-6){
				F('xyft_state',0);
				setconfig('xyft_state',0);
				$new_message = array(
					'type' => 'admin',
					'head_img_url'=>'/Public/main/img/kefu.jpg',
					'from_client_name' => 'GM管理员',
					'content'=>'期号:'.$xyftdata['next']['periodNumber'].'关闭，请耐心等待开奖',
					'time'=>date('H:i:s')
				);
				foreach ($this->worker->connections as $conn) {
					$conn -> send(json_encode($new_message));
				}
				$this->add_message($new_message);/*添加信息*/
			}
			if((($next_time-time()<285 && $next_time-time()>90) || (time() > strtotime("04:02:00") && time() < strtotime("04:06:00"))) && F('is_send')==0){
				//结算
				//开奖结果
				$current_number = M('number')->where("game='xyft'")->order('id DESC')->find();
				$number1 = explode(',', $current_number['awardnumbers']);
				$lh = unserialize($current_number['lh']);
				for($i=0;$i<count($number1);$i++){
					if($number1[$i] % 2 == 0){
						$number[$i]['ds'] = '双';
						if($number1[$i]>=6){
							$number[$i]['zuhe'] = '大双';
						}else{
							$number[$i]['zuhe'] = '小双';
						}
					}else{
						$number[$i]['ds'] = '单';
						if($number1[$i]>=6){
							$number[$i]['zuhe'] = '大单';
						}else{
							$number[$i]['zuhe'] = '小单';
						}
					}
					if($number1[$i]>=6){
						$number[$i]['dx'] = '大';
					}else{
						$number[$i]['dx'] = '小';
					}
				}	
				//当前局所有竞猜
				$today_time = strtotime(date('Y-m-d',time()));
				$list = M('order')->where("number = {$current_number['periodnumber']} && time > '{$today_time}' && state = 1 && is_add = 0 && game='xyft'")->order("time ASC")->select();
				
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
							//车号大小单双(12345/双/100)
							case 1:
								$ex_info = explode('大', $list[$i]['jincai']);
								if (count($ex_info) > 1) {
									$start1 = $ex_info;
									$info_dxds = '大';
								}
								$ex_info = explode('小', $list[$i]['jincai']);
								if (count($ex_info) > 1) {
									$start1 = $ex_info;
									$info_dxds = '小';
								}
								$ex_info = explode('单', $list[$i]['jincai']);
								if (count($ex_info) > 1) {
									$start1 = $ex_info;
									$info_dxds = '单';
								}
								$ex_info = explode('双', $list[$i]['jincai']);
								if (count($ex_info) > 1) {
									$start1 = $ex_info;
									$info_dxds = '双';
								}
								// $start1 = explode('/', $list[$i]['jincai']);
								$num1 = 0;
								if ($start1[0] == '') {
									$starts1 = array('1');
								} else {
									$starts1 = str_split($start1[0]);
								}
								
								if($info_dxds=='单' || $info_dxds=='双'){
									for($a=0;$a<count($starts1);$a++){
										if($starts1[$a]==0){
											$hao1 = '9';
										}else{
											$hao1 = $starts1[$a]-1;
										}
										if($number[$hao1]['ds']==$info_dxds){
											$num1++;
										}
									}
								}else{
									for($a=0;$a<count($starts1);$a++){
										if($starts1[$a]==0){
											$hao1 = '9';
										}else{
											$hao1 = $starts1[$a]-1;
										}
										if($number[$hao1]['dx']==$info_dxds){
											$num1++;
										}
									}
								}
								if($num1>0){
									$points1 = $num1*$start1[1]*C('ft_dxds');
									$res1 = $this->add_points($id,$userid,$points1);
									if($res1){
										$this->send_msg('pointsadd',$points1,$userid);
									}
								} 
								break;
								
							//车号(12345/89/20)
							case 2:
								$start2 = explode('/', $list[$i]['jincai']);
								
								if (count($start2) == 3) {
									$chehao2 = str_split($start2[1]);
									$starts2 = str_split($start2[0]);
									$ya = $start2[2];
								} else {
									$chehao2 = str_split($start2[0]);
									$starts2 = str_split('1');
									$ya = $start2[1];
								}
								$num2 = 0;
								for($s=0;$s<count($chehao2);$s++){
									for($a=0;$a<count($starts2);$a++){
										if($starts2[$a]==0){
											$hao2 = '9';
										}else{
											$hao2 = $starts2[$a]-1;
										}
										if($chehao2[$s]==0){
											$chehao2[$s]=10;
										}
										if($chehao2[$s]==$number1[$hao2]){
											$num2++;
										}
									}
								}
								if($num2>0){
									$points2 = $num2*$ya*C('ft_chehao');
									$res2 = $this->add_points($id,$userid,$points2);
									if($res2){
										$this->send_msg('pointsadd',$points2,$userid);
									}
								} 
								break;
								
							//组合(890/大单/50)
							case 3:
								$start3 = explode('/', $list[$i]['jincai']);
								$starts3 = str_split($start3[0]);
								$num3 = 0;
								for($a=0;$a<count($starts3);$a++){
									if($starts3[$a]==0){
										$hao3 = '9';
									}else{
										$hao3 = $starts3[$a]-1;
									}
									if($number[$hao3]['zuhe']==$start3[1]){
										$num3++;
									}
								}
								if($num3>0){
									if($start3[1]=='大单' || $start3[1]=='小双'){
										$points3 = $num3*$start3[2]*C('ft_zuhe_1');
									}else{
										$points3 = $num3*$start3[2]*C('ft_zuhe_2');
									}
									$res3 = $this->add_points($id,$userid,$points3);
									if($res3){
										$this->send_msg('pointsadd',$points3,$userid);
									}
								} 
								break;
								
							//龙虎(123/龙/100)
							case 4:
								$start4 = explode('/', $list[$i]['jincai']);
								$starts4 = str_split($start4[0]);
								$num4 = 0;
								for($a=0;$a<count($starts4);$a++){
									if($starts4[$a]==0){
										$hao4 = '9';
									}else{
										$hao4 = $starts4[$a]-1;
									}
									if($lh[$hao4]==$start4[1]){
										$num4++;
									}
								}
								if($num4>0){
									$points4 = $num4*$start4[2]*C('ft_lh');
									$res4 = $this->add_points($id,$userid,$points4);
									if($res4){
										$this->send_msg('pointsadd',$points4,$userid);
									}
								} 
								break;
								
							//冠亚庄闲(庄/200)
							case 5:
								$start5 = explode('/', $list[$i]['jincai']);
								if($current_number['zx'] == $start5[0]){
									$points5 = $start5[1]*C('ft_zx');
									$res5 = $this->add_points($id,$userid,$points5);
									if($res5){
										$this->send_msg('pointsadd',$points5,$userid);
									}
								}
								break;
								
							//冠亚号码(组/1-9.3-7/100)
							case 6:
								$start6 = explode('/', $list[$i]['jincai']);
								$num6 = 0;
								if(strlen($start6[1])>3){
									$zu = explode('.', $start6[1]);
									for($a=0;$a<count($zu);$a++){
										$gy = explode('-', $zu[$a]);
										if($gy[0]==0){
											$gy[0]=10;
										}
										if($gy[1]==0){
											$gy[1]=10;
										}
										if($gy[0]==$number1[0] && $gy[1]==$number1[1] || $gy[0]==$number1[1] && $gy[1]==$number1[0]){
											$num6 = 1;
										}
									}
								}else{
									$gy = explode('-', $start6[1]);
									if($gy[0]==0){
										$gy[0]=10;
									}
									if($gy[1]==0){
										$gy[1]=10;
									}
									if($gy[0]==$number1[0] && $gy[1]==$number1[1] || $gy[0]==$number1[1] && $gy[1]==$number1[0]){
										$num6 = 1;
									}
								}
								if($num6>0){
									$points6 = $num6*$start6[2]*C('ft_gy');
									$res6 = $this->add_points($id,$userid,$points6);
									if($res6){
										$this->send_msg('pointsadd',$points6,$userid);
									}
								} 
								break;
								
							//特码大小单双(和双100)
							case 7:
								$start7 = substr($list[$i]['jincai'], 3,3); 
								$starts7 = substr($list[$i]['jincai'], 6);
								$num7 = 0;
								if($current_number['tema'] == 11 && C('ft_gy_set') == 2){
									$num7 = 2;
								}else if($start7=='大' || $start7=='小'){
									if($current_number['tema_dx']==$start7){
										$num7 = 1;
									}
								}else if($start7=='单' || $start7=='双'){
									if($current_number['tema_ds']==$start7){
										$num7 = 1;
									}
								}
								if($num7>0){
									if ($num7 == 1) {
										if (C('ft_gy_set') == 1) {
											if($start7=='大' || $start7=='双'){
												$points7 = $starts7*C('ft_tema_1');
											}else{
												$points7 = $starts7*C('ft_tema_2');
											}
										} else{
											$points7 = $starts7*C('ft_tema');
										}
									} else{
										$points7 = $starts7*1;
									}
									
									$res7 = $this->add_points($id,$userid,$points7);
									if($res7){
										$this->send_msg('pointsadd',$points7,$userid);
									}
								}
								break;
								
							//特码数字(和567/100)
							case 8:
								$tema1 = array('03','04','18','19');
								$tema2 = array('5','6','16','17');
								$tema3 = array('7','8','14','15');
								$tema4 = array('9','10','12','13');
								$tema5 = array('11');
								
								$start8 = explode('/', $list[$i]['jincai']);
								$starts8 = substr($start8[0], 3);
								$num8 = 0;
								$points8 = 0;
								$num_str8 = fv_split($starts8);
								$info_str8 = array();
								for($a=0;$a<count($num_str8);$a++){
									if ($a >= 1) {
										if ($num_str8[$a-1] == '1') {
											if ( $num_str[$a-2] == '1' && $num_str[$a] == '1') {
											} else {
												continue;
											}
										}
									}
									if ($num_str8[$a] == '1') {
										$info_str8[] = $num_str8[$a] . $num_str8[$a+1];
									} else {
										$info_str8[] = $num_str8[$a];
									}
								}
								for($a=0;$a<count($info_str8);$a++){
									if($current_number['tema']==$info_str8[$a]){
										if(in_array($info_str8[$a], $tema1)){
											$points8 += intval($start8[1]*C('ft_tema_sz_1'));
										}
										if(in_array($info_str8[$a], $tema2)){
											$points8 += intval($start8[1]*C('ft_tema_sz_2'));
										}
										if(in_array($info_str8[$a], $tema3)){
											$points8 += intval($start8[1]*C('ft_tema_sz_3'));
										}
										if(in_array($info_str8[$a], $tema4)){
											$points8 += intval($start8[1]*C('ft_tema_sz_4'));
										}
										if(in_array($info_str8[$a], $tema5)){
											$points8 += intval($start8[1]*C('ft_tema_sz_5'));
										}
										$num8++;
									}
								}
								if($num8>0){
									$res8 = $this->add_points($id,$userid,$points8);
									if($res8){
										$this->send_msg('pointsadd',$points8,$userid);
									}
								} 
								break;
								
							//特码区段(BC/100)
							case 9:
								$start9 = explode('/', $list[$i]['jincai']);
								$num9 = 0;
								if(strlen($start9[0])>1){
									$starts9 = str_split($start9[0]);
									for($a=0;$a<count($starts9);$a++){
										if($current_number['tema_dw']==$starts9[$a]){
											if($starts9[$a]=='A' || $starts9[$a]=='C'){
												$points9 = $start9[1]*C('ft_tema_qd_1');
											}else{
												$points9 = $start9[1]*C('ft_tema_qd_2');
											}
											$num9 = 1;
										}
									}
								}else{
									if($current_number['tema_dw']==$start9[0]){
										if($start9[0]=='A' || $start9[0]=='C'){
											$points9 = $start9[1]*C('ft_tema_qd_1');
										}else{
											$points9 = $start9[1]*C('ft_tema_qd_2');
										}
										$num9 = 1;
									}
								}
								if($num9>0 && $points9){
									$res9 = $this->add_points($id,$userid,$points9);
									if($res9){
										$this->send_msg('pointsadd',$points9,$userid);
									}
								}
								break;							
						}
					}
				}
				
				F('is_send',1);
				F('xyft_state',0);
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
				$this->add_message($new_message);/*添加信息*/
				$new_message = array(
					'delay'=>'0',
					'type' => 'admin',
					'head_img_url'=>'/Public/main/img/kefu.jpg',
					'from_client_name' => 'GM管理员',
					'content'=>'期号:'.$xyftdata['next']['periodNumber'].'开放，祝各位中大奖',
					'time'=>date('H:i:s')
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
			$caiji = M('caiji')->where("game='xyft'")->limit(0,1)->order("id desc")->find();
			$data = json_decode(xyft_format($caiji),true);
		
			if(F('xyftPeriodNumber')!=$data['current']['periodNumber']){
				$today_time = date('Y-m-d',time()) . " 00:00:00";
				$res = M('number')->where("game = 'xyft' and periodnumber = {$data['current']['periodNumber']} and awardtime > '{$today_time}'")->find();
				if(!$res){
					$map['awardnumbers'] = $data['current']['awardNumbers'];
					$map['awardtime'] = $data['current']['awardTime'];
					$map['time'] = strtotime($data['current']['awardTime']);
					$map['periodnumber'] = $data['current']['periodNumber'];
					$info = explode(',', $map['awardnumbers']);
					for($i=0;$i<count($info);$i++){
						if($info[$i]<10){
							$info[$i] = substr($info[$i], 1);
						}
					}
					$map['number'] = serialize($info);
					if($info[0]>$info[9]){
						$lh[0] = '龙';
					}else{
						$lh[0] = '虎';
					}
					if($info[1]>$info[8]){
						$lh[1] = '龙';
					}else{
						$lh[1] = '虎';
					}
					if($info[2]>$info[7]){
						$lh[2] = '龙';
					}else{
						$lh[2] = '虎';
					}
					if($info[3]>$info[6]){
						$lh[3] = '龙';
					}else{
						$lh[3] = '虎';
					}
					if($info[4]>$info[5]){
						$lh[4] = '龙';
					}else{
						$lh[4] = '虎';
					}
					$map['lh'] = serialize($lh);
					$map['tema'] = $info[0]+$info[1];
					if (C('ft_gy_set') == 1) {
						if($map['tema'] % 2 == 0){
							$map['tema_ds'] = '双';
						}
						else{
							$map['tema_ds'] = '单';
						}
					} else {
						if($map['tema'] % 2 == 0){
							$map['tema_ds'] = '双';
						}else if($map['tema'] == 11){
							$map['tema_ds'] = '和';
						}
						else{
							$map['tema_ds'] = '单';
						}
					}
					
					if (C('ft_gy_set') == 1) {
						if($map['tema']>=12){
							$map['tema_dx'] = '大';
						}else{
							$map['tema_dx'] = '小';
						}
					} else {
						if($map['tema']>=12){
							$map['tema_dx'] = '大';
						}else if($map['tema'] == 11){
							$map['tema_dx'] = '和';
						}else{
							$map['tema_dx'] = '小';
						}
					}
					if($map['tema']>=3 && $map['tema']<=7){
						$map['tema_dw'] = 'A';
					}
					if($map['tema']>=8 && $map['tema']<=14){
						$map['tema_dw'] = 'B';
					}
					if($map['tema']>=15 && $map['tema']<=19){
						$map['tema_dw'] = 'C';
					}
					if($info[0]>$info[1]){
						$map['zx'] = '庄';
					}else{
						$map['zx'] = '闲';
					}
					$map['game'] = $data['game'];
					$res1 = M('number')->add($map);
					if($res1){
						F('xyftPeriodNumber',$data['current']['periodNumber']);
						F('xyftdata',$data);
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
				$xyft_state = F('xyft_state');
				if($xyft_state == 0){
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
					// if (F('game') == 'pk10' && $time > strtotime("23:57:30")) {
					// 	$content_msg =  "北京赛车最后一期已关闭";
					// 	$new_message = array(
					// 		'uid'  => $connection->uid,
					// 		'type' => 'admin',
					// 		'head_img_url'=>'/Public/main/img/kefu.jpg',
					// 		'from_client_name' => 'GM管理员',
					// 		'content' => $content_msg,
					// 		'time' => date('H:i:s')
					// 	);
					// 	$connection -> send(json_encode($new_message));
					// 	$new_message['type'] = 'error';
					// 	$this->add_message($new_message);/*添加信息*/
					// 	break;
					// } 
				
					/*检测格式和金额*/
					$res = check_format($message_data['content'],C('ftqi_min_point'),C('ftqi_max_point'),C('ft_xz_open'),C('ft_xz_max'));
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
							$content_msg =  '「'.$message_data['content'].'」'.'单笔点数最低'.C('ftqi_min_point').',竞猜失败';
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
							$xyftdata = F('xyftdata');
							$user = M('user')->where("id = $userid")->find();
							//当前玩法是否超过设置金额
							$wf_points = M('order')->field("sum(del_points) as sum_del")->where("userid = {$userid} and type={$res['type']} and state=1  and number = {$xyftdata['next']['periodNumber']}")->find();
							$wf_max_points = 1000000000000;
							// switch ($res['type']) {
								// case '1':
									// $wf_max_points = C('ft_xz_max')['dxds'];
									// break;
								// case '2':
									// $wf_max_points = C('ft_xz_max')['chehao'];
									// break;
								// case '3':
									// $wf_max_points = C('ft_xz_max')['zuhe'];
									// break;
								// case '4':
									// $wf_max_points = C('ft_xz_max')['lh'];
									// break;
								// case '5':
									// $wf_max_points = C('ft_xz_max')['zx'];
									// break;
								// case '6':
									// $wf_max_points = C('ft_xz_max')['gy'];
									// break;
								// case '7':
									// $wf_max_points = C('ft_xz_max')['tema'];
									// break;
								// case '8':
									// $wf_max_points = C('ft_xz_max')['tema_sz'];
									// break;
								// case '9':
									// $wf_max_points = C('ft_xz_max')['tema_qd'];
									// break;
								
								// default:
									// $wf_max_points = 0;
									// break;
							// }
							//车号限制  123/23/100
							if ($res['type'] == '2') {
								$type_list = M('order')->where("userid = {$userid} and state=1  and number = {$xyftdata['next']['periodNumber']} and type = 2")->select();
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
													'content' => '「'.$message_data['content'].'」'.'车号玩法，每个数字最高点数'.$wf_max_points.',您已超过，竞猜失败',
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
							//查看已投注金额
							$user_points = M('order')->field("sum(del_points) as sum_del")->where("userid = {$userid} and state=1  and number = {$xyftdata['next']['periodNumber']}")->find();
							if ((intval($user_points['sum_del'])+$res['points']) > C('ftqi_max_point')) {
								$points_tips = array(
									'uid'  => $connection->uid,
									'type' => 'admin',
									'head_img_url'=>'/Public/main/img/kefu.jpg',
									'from_client_name' => 'GM管理员',
									'content' => '「'.$message_data['content'].'」'.'每期最高点数'.C('ftqi_max_point').',您已超过，竞猜失败',
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
							$map['number'] = $xyftdata['next']['periodNumber'];
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
								'content' => "<font color='red'>非下注聊天信息：</font><br/>".$message_data['content'], 
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
					$xyft_state = F('xyft_state');
					$new_message = array(
						'type' => 'say',
						'head_img_url'=>'/Uploads'.$robot[0]['headimgurl'],
						'from_client_name' => $robot[0]['nickname'],
						'content'=>$mess[0]['content'],
						'time'=>date('H:i:s')
					);
					if($xyft_state==1){
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
		$count = M('robot_message')->where("type=1")->count();
     	$rand = mt_rand(0,$count-1); //产生随机数。
     	$limit = $rand.','.'1'; 
    	$data = M('robot_message')->where("type=1")->limit($limit)->select();  
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
			$new_message['game'] = 'xyft';
			$res = M('message')->add($new_message);
			return $res;
		}
		$res = M('message')->add($new_message);
		return $res;
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