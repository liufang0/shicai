<?php
namespace Home\Controller;
use Think\Server;
class WorkermanlhcController extends Server {
	protected $socket = 'websocket://0.0.0.0:15561';
	
	/*添加定时器
	 *监控连接状态
	 * */
	public function onWorkerStart(){
		$auth = auth_check(C('auth_code'),C('siteurl'));
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}
		$beginToday=strtotime('16:00:00');
		$endToday=strtotime("21:35:00");
		$caiji = M('caiji')->where("game='lhc'")->limit(0,1)->order("id desc")->find();
		$data =  lhc_format2($caiji);
		
		$time_interval = 1;
		$lhcdata = json_decode($data,true);
		$nexttime = $lhcdata['next']['delayTimeInterval']+strtotime($lhcdata['next']['awardTime']);
		
		//if($nexttime-time()>C('lhc_stop_time')*60-6 && $nexttime-time()<18000 && time()>$beginToday && time()<$endToday){
		if($nexttime-time()>C('lhc_stop_time')*60-6 && time()<strtotime($lhcdata['next']['awardTime']) ){
			F('lhc_state',1);
			setconfig('lhc_state',1);
		}else{
			F('lhc_state',0);
			setconfig('lhc_state',0);
		}
		if(!F('lhcdata')){
			F('lhcdata',$lhcdata);
		}
		if(!F('is_send')){
			F('is_send',1);
		}
		
		/*开奖*/
		\Workerman\Lib\Timer::add($time_interval, function(){
		
			$beginToday=strtotime('16:00:00');
			$endToday=strtotime("21:35:00");
			F('game','lhc');
			$lhcdata = F('lhcdata');
			$next_time = $lhcdata['next']['delayTimeInterval']+strtotime($lhcdata['next']['awardTime']);
			$awardtime = $lhcdata['current']['awardTime'];
			//if($next_time-time()>C('lhc_stop_time')*60-6 && $next_time-time()<18000 && time()>$beginToday && time()<$endToday ){
			if($next_time-time()>C('lhc_stop_time')*60-6 && time()<strtotime($lhcdata['next']['awardTime']) ){
				F('lhc_state',1);
				setconfig('lhc_state',1);
			}else{
				F('lhc_state',0);
				setconfig('lhc_state',0);
			}
			
			if($next_time-time()== 24+C('lhc_stop_time')*60){
				$new_message = array(
					'type' => 'admin',
					'head_img_url'=>'/Public/main/img/kefu.jpg',
					'from_client_name' => 'GM管理员',
					'content'=>'期号:'.$lhcdata['next']['periodNumber'].'<br/>'.'--距离封盘还有10分钟--',
					'time'=>date('H:i:s')
				);
				foreach ($this->worker->connections as $conn) {
					$conn -> send(json_encode($new_message));
				}
			}
			if($next_time-time()==C('lhc_stop_time')*60-6){
				F('lhc_state',0);
				setconfig('lhc_state',0);
				$new_message = array(
					'type' => 'admin',
					'head_img_url'=>'/Public/main/img/kefu.jpg',
					'from_client_name' => 'GM管理员',
					'content'=>'期号:'.$lhcdata['next']['periodNumber'].'关闭，请耐心等待开奖',
					'time'=>date('H:i:s')
				);
				foreach ($this->worker->connections as $conn) {
					$conn -> send(json_encode($new_message));
				}
			}
			echo($next_time-time()."\n");
			//if((($next_time-time()>18000) || (time() > strtotime("23:54:00") && time() < strtotime("23:59:00"))) && F('is_send')==0){
			if(($next_time-time()>86000) && F('is_send')==0){
				echo "start jie suan\n";
				//结算
				//开奖结果
				$current_number = M('number')->where("game='lhc'")->order('id DESC')->find();
				$number1 = explode(',', $current_number['awardnumbers']);
				$sebo = array(
					'1'=>'红波','2'=>'红波','7'=>'红波','8'=>'红波','12'=>'红波','13'=>'红波','18'=>'红波','19'=>'红波','23'=>'红波','24'=>'红波','29'=>'红波','30'=>'红波','34'=>'红波','35'=>'红波','40'=>'红波','45'=>'红波','46'=>'红波',
					'3'=>'蓝波','4'=>'蓝波','9'=>'蓝波','10'=>'蓝波','14'=>'蓝波','15'=>'蓝波','20'=>'蓝波','25'=>'蓝波','26'=>'蓝波','31'=>'蓝波','36'=>'蓝波','37'=>'蓝波','41'=>'蓝波','42'=>'蓝波','47'=>'蓝波','48'=>'蓝波',
					'5'=>'绿波','6'=>'绿波','11'=>'绿波','16'=>'绿波','17'=>'绿波','21'=>'绿波','22'=>'绿波','27'=>'绿波','28'=>'绿波','32'=>'绿波','33'=>'绿波','38'=>'绿波','39'=>'绿波','43'=>'绿波','44'=>'绿波','49'=>'绿波'
				);
				$shengxiao = array(
					1=>'狗', 13=>'狗', 25=>'狗', 37=>'狗', 49=>'狗', 
					12=>'猪', 24=>'猪', 36=>'猪', 48=>'猪', 
					11=>'鼠', 23=>'鼠', 35=>'鼠', 47=>'鼠', 
					10=>'牛', 22=>'牛', 34=>'牛', 46=>'牛', 
					9=>'虎', 21=>'虎', 33=>'虎', 45=>'虎',
					8=>'兔', 20=>'兔', 32=>'兔', 44=>'兔',
					7=>'龙', 19=>'龙', 31=>'龙', 43=>'龙',
					6=>'蛇', 18=>'蛇', 30=>'蛇', 42=>'蛇',
					5=>'马', 17=>'马', 29=>'马', 41=>'马',
					4=>'羊', 16=>'羊', 28=>'羊', 40=>'羊',
					3=>'猴', 15=>'猴', 27=>'猴', 39=>'猴',
					2=>'鸡', 14=>'鸡', 26=>'鸡', 38=>'鸡',
				);
				for($i=0;$i<count($number1);$i++){
					if($number1[$i] % 2 == 0){
						$number[$i]['ds'] = '双';
					}else{
						$number[$i]['ds'] = '单';
					}
					if($number1[$i]>=25){
						$number[$i]['dx'] = '大';
						if($number1[$i]>48){
							$number[$i]['dx'] = '和';
						}
					}else{
						$number[$i]['dx'] = '小';
					}
					$number[$i]['hll'] = $sebo[$number1[$i]];
					$number[$i]['sx'] = $shengxiao[$number1[$i]];
				}	
				//当前局所有竞猜
				$today_time = strtotime(date('Y-m-d',time()));
				$list = M('order')->where("number = {$current_number['periodnumber']} && time > '{$today_time}' && state = 1 && is_add = 0 && game='lhc'")->order("time ASC")->select();
				
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
							//正特码定位大小单双 1234567/双/100
							case 1:
								$start1 = explode('/', $list[$i]['jincai']);
								$num1 = 0;
								$num11 = 0;//为和
								$starts1= str_split($start1[0]);
								if($start1[1]=='单' || $start1[1]=='双'){
									for($a=0;$a<count($starts1);$a++){
										$hao1 = $starts1[$a]-1;
										if($number[$hao1]['ds']==$start1[1]){
											$num1++;
										}
									}
								}else{
									for($a=0;$a<count($starts1);$a++){
										$hao1 = $starts1[$a]-1;
										if($number[$hao1]['dx']==$start1[1]){
											$num1++;
										}else if($number[$hao1]['dx']=='和'){
											$num11++;
										}
									}
								}
								if($num11>0){
									$points1 = $start1[2]*1;
									$res1 = $this->add_points($id,$userid,$points1);
									if(res1){
										$this->send_msg('pointsadd',$points1,$userid);
									}
								}
								if($num1>0){
									$points1 = $num1*$start1[2]*C('lhc_dxds');
									$res1 = $this->add_points($id,$userid,$points1);
									if($res1){
										$this->send_msg('pointsadd',$points1,$userid);
									}
								}
								break;
								
							//特码大小单双 大/100
							case 2:
								
								$start2 = explode('/', $list[$i]['jincai']);
								$num2 = 0;
								$num21 = 0;//为和
								if($start2[0]=='单' || $start2[0]=='双'){
									if($number[6]['ds']==$start2[0]){
										$num2++;
									}
								}else{
									if($number[6]['dx']==$start2[0]){
										$num2++;
									}else if($number[6]['dx']=='和'){
										$num21++;
									}
								}
								if($num21>0){
									$points2 = $start2[1]*1;
									$res2 = $this->add_points($id,$userid,$points2);
									if(res2){
										$this->send_msg('pointsadd',$points2,$userid);
									}
								}
								if($num2>0){
									$points2 = $num2*$start2[1]*C('lhc_tdxds');
									$res2 = $this->add_points($id,$userid,$points2);
									if($res2){
										$this->send_msg('pointsadd',$points2,$userid);
									}
								}
								break;
								
							//特码红绿蓝 红波/100
							case 4:
								$start4 = explode('/', $list[$i]['jincai']);
								if($number[6]['hll']==$start4[0]){
									$points4 = $start4[1]*C('lhc_thll');
									$res4 = $this->add_points($id,$userid,$points4);
									if($res4){
										$this->send_msg('pointsadd',$points4,$userid);
									}
								}
								break;
								
							//特码生肖 鸡/100
							case 5:
								$start5 = explode('/', $list[$i]['jincai']);
								if($number[6]['sx']==$start5[0]){
									$lhc_tsx=0;
									if($start5[0]=='鼠'){
										$lhc_tsx=C('lhc_tshu');
									}else if($start5[0]=='牛'){
										$lhc_tsx=C('lhc_tniu');
									}else if($start5[0]=='虎'){
										$lhc_tsx=C('lhc_thu');
									}else if($start5[0]=='兔'){
										$lhc_tsx=C('lhc_ttu');
									}else if($start5[0]=='龙'){
										$lhc_tsx=C('lhc_tlong');
									}else if($start5[0]=='蛇'){
										$lhc_tsx=C('lhc_tshe');
									}else if($start5[0]=='马'){
										$lhc_tsx=C('lhc_tma');
									}else if($start5[0]=='羊'){
										$lhc_tsx=C('lhc_tyang');
									}else if($start5[0]=='猴'){
										$lhc_tsx=C('lhc_thou');
									}else if($start5[0]=='鸡'){
										$lhc_tsx=C('lhc_tji');
									}else if($start5[0]=='狗'){
										$lhc_tsx=C('lhc_tgou');
									}else if($start5[0]=='猪'){
										$lhc_tsx=C('lhc_tzhu');
									}
									$points5 = $start5[1]*$lhc_tsx;
									$res5 = $this->add_points($id,$userid,$points5);
									if($res5){
										$this->send_msg('pointsadd',$points5,$userid);
									}
								}
								break;
								
							//正码定位(多位) 13/34.45/100
							case 6:
								$start6 = explode('/', $list[$i]['jincai']);
								$num61 = 0;
							
								$zu = explode('.', $start6[1]);
								$starts6 = str_split($start6[0]);
								for($a=0;$a<count($starts6);$a++){
									$hao1 = $starts6[$a]-1;
									for($b=0;$b<count($zu);$b++){
										if ($number1[$hao1] == $zu[$b]) {
											$num61++;
										}
									}
								}
								if($num61>0){
									$points6 = $num61*$start6[2]*C('lhc_zhm');
									$res6 = $this->add_points($id,$userid,$points6);
									if($res6){
										$this->send_msg('pointsadd',$points6,$userid);
									}
								}
								break;
								
							//正码定位(单位) 13/34/100
							case 7:
								$start7 = explode('/', $list[$i]['jincai']);
								$starts7 = str_split($start7[0]);
								$ya2 = $start7[2];
								$num7 = 0;
								for($a=0;$a<count($starts7);$a++){
									$hao1 = $starts7[$a]-1;
									if($start7[1]==$number1[$hao1]){
										$num7++;
									}
								}
								if($num7>0){
									$points7 = $num7*$start7[2]*C('lhc_zhm');
									$res7 = $this->add_points($id,$userid,$points7);
									if($res7){
										$this->send_msg('pointsadd',$points7,$userid);
									}
								} 
								break;
								
							//特码定位 34/100 7/34/100 34.35/100 7/34.35/100
							case 8:
								$start8 = explode('/', $list[$i]['jincai']);
								$num8=0;
								
								if (count($start8) == 3) {// 7/34/100
									$zu8 = explode('.', $start8[1]);
									if (count($zu8) >1) { // 7/34.35/100
										for($a=0;$a<count($zu8);$a++){
											if ($number1[6] == $zu8[$a]) {
												$num8++;
											}
										}
									} else {
										if($number[6]==$start8[1]){
											$num8++;
										}
									}
									$ya8 = $start8[2];
								}else{// 34/100
									$zu8 = explode('.', $start8[0]);
									if (count($zu8) >1) { // 34.35/100
										for($a=0;$a<count($zu8);$a++){
											if ($number1[6] == $zu8[$a]) {
												$num8++;
											}
										}
									} else {
										if($number[6]==$start8[0]){
											$num8++;
										}
									}
									$ya8 = $start8[1];
								}
								
								
								if($num8>0){
									$points8 = $num8*$ya8*C('lhc_thm');
									$res8 = $this->add_points($id,$userid,$points8);
									if($res8){
										$this->send_msg('pointsadd',$points8,$userid);
									}
								}
								break;
															
						}
					}
				}
				
				F('is_send',1);
				F('lhc_state',0);
				$new_message = array(
					'delay'=>'0',
					'type' => 'admin',
					'head_img_url'=>'/Public/main/img/kefu.jpg',
					'from_client_name' => 'GM管理员',
					'content'=>'期号:'.$lhcdata['next']['periodNumber'].'开放，祝各位中大奖',
					'time'=>date('H:i:s')
				);
				
	            foreach ($this->worker->connections as $conn) {
					$conn -> send(json_encode($new_message));
				}
				$content = $current_number['periodnumber']."期结算已完毕！<br/>
							号码：".$current_number['awardnumbers'];
				$new_message = array(
					'delay' => '0',
					'type' => 'system',
					'head_img_url'=>'/Public/main/img/system.jpg',
					'from_client_name' => '客服',
					'content'=> $content,
					'time'=>date('H:i:s')
				);
				foreach ($this->worker->connections as $conn) {
					$conn -> send(json_encode($new_message));
				}
				// $this->add_message($new_message);
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
		/*\Workerman\Lib\Timer::add(300, function(){
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
			
    	});*/
		
		//存每期结果
		\Workerman\Lib\Timer::add(5, function(){
			$caiji = M('caiji')->where("game='lhc'")->limit(0,1)->order("id desc")->find();
			$data = json_decode(lhc_format2($caiji),true);
		
			if(F('lhcPeriodNumber')!=$data['current']['periodNumber']){
				$today_time = date('Y-m-d',time()) . " 00:00:00";
				$res = M('number')->where("game = 'lhc' and periodnumber = {$data['current']['periodNumber']} ")->find();
				if(!$res){
					$map['awardnumbers'] = $data['current']['awardNumbers'];
					$map['awardtime'] = $data['current']['awardTime'];
					$map['time'] = strtotime($data['current']['awardTime']);
					$map['periodnumber'] = $data['current']['periodNumber'];
					$info = explode(',', $map['awardnumbers']);
					$map['number'] = serialize($info);
					$map['lh'] = '';
					$map['tema'] = $info[6];
					$map['tema_ds'] = $info[6] % 2==0 ? '双' : '单';
					$map['tema_dx'] = $info[6]<=24 ? '小' : '大';
					
					$map['tema_dw'] = '';
					$map['zx'] = '';
					$map['game'] = $data['game'];
					$res1 = M('number')->add($map);
					var_dump($res1);
					if($res1){
						F('lhcPeriodNumber',$data['current']['periodNumber']);
						F('lhcdata',$data);
						F('is_send',0);
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
				
				/*$new_message = array(
					'type' => 'admin',
					'head_img_url'=>'/Public/main/img/kefu.jpg',
					'from_client_name' => 'GM管理员',
					'content' => C('welcome'), 
					'time' => date('H:i:s')
				);
				$connection -> send(json_encode($new_message));*/
				break;	
			case 'say' :
				$userid = $connection->uid;
				/*是否竞猜时间*/
				$lhc_state = F('lhc_state');
				if($lhc_state == 0){
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
					$lhcdata = F('lhcdata');
					$next_time = $lhcdata['next']['delayTimeInterval']+strtotime($lhcdata['next']['awardTime']);
					//if ($time > strtotime("21:35:30")) {
					if ($next_time-time()<C('lhc_stop_time')*60-6) {
						$content_msg =  "六合彩已关闭";
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
					$res = check_format_lhc($message_data['content'],C('lhcqi_min_point'),C('lhcqi_max_point'),C('lhc_xz_open'),C('lhc_xz_max'));
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
							$content_msg =  '「'.$message_data['content'].'」'.'单笔点数最低'.C('lhcqi_min_point').',竞猜失败';
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
							$lhcdata = F('lhcdata');
							$user = M('user')->where("id = $userid")->find();
							//当前玩法是否超过设置金额
							$wf_points = M('order')->field("sum(del_points) as sum_del")->where("userid = {$userid} and type={$res['type']} and state=1  and number = {$lhcdata['next']['periodNumber']}")->find();
							$wf_max_points = 0;
							switch ($res['type']) {
								case '1':
									$wf_max_points = C('lhc_xz_max')['dxds'];
									break;
								case '2':
									$wf_max_points = C('lhc_xz_max')['tdxds'];
									break;
								case '3':
									$wf_max_points = C('lhc_xz_max')['zhll'];
									break;
								case '4':
									$wf_max_points = C('lhc_xz_max')['thll'];
									break;
								case '5':
									//$wf_max_points = C('lhc_xz_max')['tsx'];
									$wf_max_points = $res['xz_max'];
									break;
								case '6':
								case '7':
									$wf_max_points = C('lhc_xz_max')['zhm'];
									break;
								case '8':
									$wf_max_points = C('lhc_xz_max')['thm'];
									break;
								
								default:
									$wf_max_points = 0;
									break;
							}
							echo $wf_max_points;
							//正码限制  123/23/100
							if ($res['type'] == '1'||$res['type'] == '3'||$res['type'] == '6') {
								$type_list = M('order')->where("userid = {$userid} and state=1  and number = {$lhcdata['next']['periodNumber']} and type = {$res['type']}")->select();
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
							$user_points = M('order')->field("sum(del_points) as sum_del")->where("userid = {$userid} and state=1  and number = {$lhcdata['next']['periodNumber']}")->find();
	
							if ((intval($user_points['sum_del'])+$res['points']) > C('lhcqi_max_point')) {
								$points_tips = array(
									'uid'  => $connection->uid,
									'type' => 'admin',
									'head_img_url'=>'/Public/main/img/kefu.jpg',
									'from_client_name' => 'GM管理员',
									'content' => '「'.$message_data['content'].'」'.'每期最高点数'.C('lhcqi_max_point').',您已超过，竞猜失败',
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
							$map['number'] = $lhcdata['next']['periodNumber'];
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
				if(C('robot')==11){
					$mess = $this->robot_message();
					$robot = $this->robot();
					$lhc_state = F('lhc_state');
					$new_message = array(
						'type' => 'say',
						'head_img_url'=>'/Uploads'.$robot[0]['headimgurl'],
						'from_client_name' => $robot[0]['nickname'],
						'content'=>$mess[0]['content'],
						'time'=>date('H:i:s')
					);
					if($lhc_state==1){
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
			$new_message['game'] = 'lhc';
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
			$userBalance = M('user')->where("id = $userid")->getField("points");
			$sql = 'update think_order set is_add=1,add_points='.$points.',balance='.($userBalance).' where id='.$order_id;
			$Model = M();
			$res1 = $Model->execute($sql);
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