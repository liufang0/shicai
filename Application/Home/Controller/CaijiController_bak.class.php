<?php

namespace Home\Controller;
use Think\Server;

header('content-type:text/html;charset=utf-8');
class CaijiController extends Server {

	protected $socket = 'websocket://0.0.0.0:15525';
	/*添加定时器
	 *监控连接状态
	 * */
	public function onWorkerStart(){
		$auth = auth_check(C('auth_code'),C('siteurl'));
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}
		
		if (C('pk10_kj_select') == 1) {
			\Workerman\Lib\Timer::add(6, function(){
				$time = time();
				// if ($time > strtotime("09:00:00") and $time < strtotime("23:59:59")) {
					$url = "http://api.164pk.com/Home/Get/getPk10";
					$cpk = http_get($url);
					$cpk = substr($cpk,3);
					$cr_data = json_decode($cpk,true);
					$periodnumber = $cr_data['current']['periodNumber'];
					$awardtime = $cr_data['current']['awardTime'];
					$awardnumbers = $cr_data['current']['awardNumbers'];

					if ($periodnumber && $awardnumbers && $awardtime) {
						$data = array(
							'periodnumber' => $periodnumber,
							'awardnumbers' => $awardnumbers,
							'awardtime' => $awardtime,
							'game' => 'pk10',
							'addtime' => time()
						);

						$caijinum = M('caiji')->where("game = 'pk10'")->limit(0,1)->order("id desc")->find();
						if (strval($caijinum['periodnumber']) != $periodnumber  && $periodnumber > $caijinum['periodnumber']) {
							M('caiji')->add($data);
						}
					}	
				// }
			
			});

		} else {
			\Workerman\Lib\Timer::add(6, function(){
				$time = time();
				if ($time > strtotime("09:00:00") and $time < strtotime("23:59:59")) {
					$url = C('pk10_api');
					$cpk = http_get($url);
					$cr_data = json_decode($cpk);
					foreach ($cr_data as $key => $value) {
						$periodnumber = $key;
						$awardtime = $value->dateline;
						$awardnumbers = $value->number;
						break;
					}

					if ($periodnumber && $awardnumbers && $awardtime) {
						$data = array(
							'periodnumber' => $periodnumber,
							'awardnumbers' => $awardnumbers,
							'awardtime' => $awardtime,
							'game' => 'pk10',
							'addtime' => time()
						);

						$caijinum = M('caiji')->where("game = 'pk10'")->limit(0,1)->order("id desc")->find();
						if (strval($caijinum['periodnumber']) != $periodnumber  && $periodnumber > $caijinum['periodnumber']) {
							M('caiji')->add($data);
						}
					}			
				} 
	    	});
		}

		if (C('xyft_kj_select') == 1) {
			//xyft开奖采集
			\Workerman\Lib\Timer::add(6, function(){
				$time = time();
				// if ($time > strtotime("00:00:00") and $time < strtotime("04:04:00")) {
					$url = "http://api.164pk.com/Home/Get/apiXyft";
					$cpk = http_get($url);
					$cpk = substr($cpk,3);
					$cr_data = json_decode($cpk,true);
					$periodnumber = $cr_data['current']['periodNumber'];
					$awardtime = $cr_data['current']['awardTime'];
					$awardnumbers = $cr_data['current']['awardNumbers'];

					if ($periodnumber && $awardnumbers && $awardtime) {
						$data = array(
							'periodnumber' => $periodnumber,
							'awardnumbers' => $awardnumbers,
							'awardtime' => $awardtime,
							'game' => 'xyft',
							'addtime' => time()
						);

						$caijinum = M('caiji')->where("game = 'xyft'")->limit(0,1)->order("id desc")->find();
						if (strval($caijinum['periodnumber']) != $periodnumber  && $periodnumber > $caijinum['periodnumber']) {
							M('caiji')->add($data);
						}
					}		
				// }
				
	    	});
		} else {
			\Workerman\Lib\Timer::add(6, function(){
				$time = time();
				// if ($time > strtotime("00:00:00") and $time < strtotime("04:04:00")) {
					$url = C('xyft_api');
					$cpk = http_get($url);
					$cr_data = json_decode($cpk);
					foreach ($cr_data as $key => $value) {
						$periodnumber = $key;
						$awardtime = $value->dateline;
						$awardnumbers = $value->number;
						break;
					}

					if ($periodnumber && $awardnumbers && $awardtime) {
						$data = array(
							'periodnumber' => $periodnumber,
							'awardnumbers' => $awardnumbers,
							'awardtime' => $awardtime,
							'game' => 'xyft',
							'addtime' => time()
						);

						$caijinum = M('caiji')->where("game = 'xyft'")->limit(0,1)->order("id desc")->find();
						if (strval($caijinum['periodnumber']) != $periodnumber  && $periodnumber > $caijinum['periodnumber']) {
							M('caiji')->add($data);
						}
					}			
				// } 
	    	});
		}

		//重庆时时彩
		if (C('ssc_kj_select') == 1) {
			\Workerman\Lib\Timer::add(6, function(){
				$time = time();
					$url = "http://api.164pk.com/Home/Get/apiSsc";
					$cpk = http_get($url);
					$cpk = substr($cpk,3);
					$cr_data = json_decode($cpk,true);
					$periodnumber = $cr_data['periodnumber'];
					$awardtime = $cr_data['awardtime'];
					$awardnumbers = $cr_data['awardnumbers'];

					if ($periodnumber && $awardnumbers && $awardtime) {
						$data = array(
							'periodnumber' => $periodnumber,
							'awardnumbers' => $awardnumbers,
							'awardtime' => $awardtime,
							'game' => 'ssc',
							'addtime' => time()
						);

						$caijinum = M('caiji')->where("game = 'ssc'")->limit(0,1)->order("id desc")->find();
						if (strval($caijinum['periodnumber']) != $periodnumber  && $periodnumber > $caijinum['periodnumber']) {
							M('caiji')->add($data);
						}
					}
	    	});
		} else {
			\Workerman\Lib\Timer::add(6, function(){
				$time = time();
					$url = C('ssc_api');
					$cpk = http_get($url);
					$cr_data = json_decode($cpk);
					foreach ($cr_data as $key => $value) {
						$periodnumber = $key;
						$awardtime = $value->dateline;
						$awardnumbers = $value->number;
						break;
					}

					if ($periodnumber && $awardnumbers && $awardtime) {
						$data = array(
							'periodnumber' => $periodnumber,
							'awardnumbers' => $awardnumbers,
							'awardtime' => $awardtime,
							'game' => 'ssc',
							'addtime' => time()
						);

						$caijinum = M('caiji')->where("game = 'ssc'")->limit(0,1)->order("id desc")->find();
						if (strval($caijinum['periodnumber']) != $periodnumber  && $periodnumber > $caijinum['periodnumber']) {
							M('caiji')->add($data);
						}
					}		
	    	});
		}



				//螞蟻收益采集
		if (C('bj28_kj_select') == 1) {
			//xyft开奖采集
			\Workerman\Lib\Timer::add(6, function(){
				$time = time();
					$url = "http://api.164pk.com/Home/Get/apiBj28";
					$cpk = http_get($url);
					$cpk = substr($cpk,3);
					$cr_data = json_decode($cpk,true);
					$periodnumber = $cr_data['periodnumber'];
					$awardtime = $cr_data['awardtime'];
					$awardnumbers = $cr_data['awardnumbers'];

					if ($periodnumber && $awardnumbers && $awardtime) {
						$data = array(
							'periodnumber' => $periodnumber,
							'awardnumbers' => $awardnumbers,
							'awardtime' => $awardtime,
							'game' => 'bj28',
							'addtime' => time()
						);

						$caijinum = M('caiji')->where("game = 'bj28'")->limit(0,1)->order("id desc")->find();
						if (strval($caijinum['periodnumber']) != $periodnumber  && $periodnumber > $caijinum['periodnumber']) {
							M('caiji')->add($data);
						}
					}
	    	});
		} else {
			\Workerman\Lib\Timer::add(6, function(){
				$time = time();
					$url = C('bj28_api');
					$cpk = http_get($url);
					$cr_data = json_decode($cpk);
					foreach ($cr_data as $key => $value) {
						$periodnumber = $key;
						$awardtime = $value->dateline;

						$number = explode(',',$value->number);
						$one = array_sum(array_slice($number,0,6));
						$two = array_sum(array_slice($number,6,6));
						$three = array_sum(array_slice($number,12,6));
						
						$one_end = substr($one,-1);
						$two_end = substr($two,-1);
						$three_end = substr($three,-1);

						$awardnumbers = "$one_end,$two_end,$three_end";
						break;
					}

					if ($periodnumber && $awardnumbers && $awardtime) {
						$data = array(
							'periodnumber' => $periodnumber,
							'awardnumbers' => $awardnumbers,
							'awardtime' => $awardtime,
							'game' => 'bj28',
							'addtime' => time()
						);

						$caijinum = M('caiji')->where("game = 'bj28'")->limit(0,1)->order("id desc")->find();
						if (strval($caijinum['periodnumber']) != $periodnumber  && $periodnumber > $caijinum['periodnumber']) {
							M('caiji')->add($data);
						}
					}		
	    	});
		}


				//螞蟻收益采集
		if (C('jnd28_kj_select') == 1) {
			//xyft开奖采集
			\Workerman\Lib\Timer::add(6, function(){
				$time = time();
					$url = "http://api.164pk.com/Home/Get/apiJnd28";
					$cpk = http_get($url);
					$cpk = substr($cpk,3);
					$cr_data = json_decode($cpk,true);
					$periodnumber = $cr_data['periodnumber'];
					$awardtime = $cr_data['awardtime'];
					$awardnumbers = $cr_data['awardnumbers'];

					if ($periodnumber && $awardnumbers && $awardtime) {
						$data = array(
							'periodnumber' => $periodnumber,
							'awardnumbers' => $awardnumbers,
							'awardtime' => $awardtime,
							'game' => 'jnd28',
							'addtime' => time()
						);

						$caijinum = M('caiji')->where("game = 'jnd28'")->limit(0,1)->order("id desc")->find();
						if (strval($caijinum['periodnumber']) != $periodnumber  && $periodnumber > $caijinum['periodnumber']) {
							M('caiji')->add($data);
						}
					}
	    	});
		} else {
			\Workerman\Lib\Timer::add(6, function(){
				$time = time();
					$url = C('jnd28_api');
					$cpk = http_get($url);
					$cr_data = json_decode($cpk);
					foreach ($cr_data as $key => $value) {
						$periodnumber = $key;
						$awardtime = $value->dateline;

						$number = explode(',',$value->number);

						$two = $number[2]+$number[5]+$number[8]+$number[11]+$number[14]+$number[17]; 
						$three = $number[3]+$number[6]+$number[9]+$number[12]+$number[15]+$number[18]; 
						$one = $number[1]+$number[4]+$number[7]+$number[10]+$number[13]+$number[16]; 

						
						$one_end = substr($one,-1);
						$two_end = substr($two,-1);
						$three_end = substr($three,-1);

						$awardnumbers = "$one_end,$two_end,$three_end";
						break;
					}

					if ($periodnumber && $awardnumbers && $awardtime) {
						$data = array(
							'periodnumber' => $periodnumber,
							'awardnumbers' => $awardnumbers,
							'awardtime' => $awardtime,
							'game' => 'jnd28',
							'addtime' => time()
						);

						$caijinum = M('caiji')->where("game = 'jnd28'")->limit(0,1)->order("id desc")->find();
						if (strval($caijinum['periodnumber']) != $periodnumber  && $periodnumber > $caijinum['periodnumber']) {
							M('caiji')->add($data);
						}
					}		
	    	});
		}

		//螞蟻收益采集
		if (C('k3_kj_select') == 1) {
			//xyft开奖采集
			\Workerman\Lib\Timer::add(6, function(){
				$time = time();
					$url = "http://api.164pk.com/Home/Get/apiK3";
					$cpk = http_get($url);
					$cpk = substr($cpk,3);
					$cr_data = json_decode($cpk,true);
					$periodnumber = $cr_data['periodnumber'];
					$awardtime = $cr_data['awardtime'];
					$awardnumbers = $cr_data['awardnumbers'];

					if ($periodnumber && $awardnumbers && $awardtime) {
						$data = array(
							'periodnumber' => $periodnumber,
							'awardnumbers' => $awardnumbers,
							'awardtime' => $awardtime,
							'game' => 'k3',
							'addtime' => time()
						);

						$caijinum = M('caiji')->where("game = 'k3'")->limit(0,1)->order("id desc")->find();
						if (strval($caijinum['periodnumber']) != $periodnumber  && $periodnumber > $caijinum['periodnumber']) {
							M('caiji')->add($data);
						}
					}
	    	});
		} else {
			\Workerman\Lib\Timer::add(6, function(){
				$time = time();
					$url = C('k3_api');
					$cpk = http_get($url);
					$cr_data = json_decode($cpk);
					foreach ($cr_data as $key => $value) {
						$periodnumber = $key;
						$awardtime = $value->dateline;
						$awardnumbers = $value->number;
						break;
					}

					if ($periodnumber && $awardnumbers && $awardtime) {
						$data = array(
							'periodnumber' => $periodnumber,
							'awardnumbers' => $awardnumbers,
							'awardtime' => $awardtime,
							'game' => 'jnd28',
							'addtime' => time()
						);

						$caijinum = M('caiji')->where("game = 'k3'")->limit(0,1)->order("id desc")->find();
						if (strval($caijinum['periodnumber']) != $periodnumber  && $periodnumber > $caijinum['periodnumber']) {
							M('caiji')->add($data);
						}
					}		
	    	});
		}


	\Workerman\Lib\Timer::add(1, function(){
		//开启自动返水
		if (C('is_auto_fs') == '1') {
			if (time() == strtotime('09:00:00')) {
				if (C('who_fs') == '1') {
					//自动返水
					$agents = M('user')->where("status = 1 and is_agent = 1")->select();

					foreach ($agents as $a => $b) {
						$time = date('Y-m-d',time()-3600*24);
					
						$aid = $b['id'];
						//查询当天是否已返水
						$is_fs = M('fs_date')->where("fs_date='{$time}' and a_id = {$aid}")->find();
						if ($is_fs) {
							continue;
						}


						//查询当天所有的订单,以人分组
						$start_time = strtotime($time.' 00:00:00');
						$end_time = strtotime($time.' 23:59:59');

						$map['time'] = array(array('egt',$start_time),array('elt',$end_time),'and');
						$map['state'] = 1;
						$map['t_id'] = $aid;

						$order = M("order");
						$list = $order->field("userid as uid,nickname,SUM(add_points) AS add_points,SUM(del_points) AS del_points")->where($map)->group('userid')->select();

						$fs_date = array(
							'fs_date' => $time,
							'add_time' => time()
						);
						$fs_date['water'] = 0;

						$t_userinfo = M('user')->field('pkft_fs,ssc_fs,pcdd_fs,k3_fs,yong')->where("id = {$aid}")->find();

						foreach ($list as $key => $value) {
							$fs_date['water'] += $value['del_points'];
							if ($value['game'] == 'pk10' || $value['game'] == 'xyft') {
								$fs_water['pkft'] += $value['del_points'] * $t_userinfo['pkft_fs'] *0.01;
							} else if($value['game'] == 'ssc') {
								$fs_water['ssc'] += $value['del_points'] * $t_userinfo['ssc_fs'] *0.01;
							} else if($value['game'] == 'k3') {
								$fs_water['ssc'] += $value['del_points'] * $t_userinfo['k3_fs'] *0.01;
							}
							else {
								$fs_water['pcdd'] += $value['del_points'] * $t_userinfo['pcdd_fs'] *0.01;
							}
						}

						$fs_date['fs_money'] = $fs_water['pkft'] + $fs_water['ssc'] + $fs_water['k3'] + $fs_water['pcdd'] ;

						$fs_date['a_id'] = $aid;

						//是否代理返水，从代理的佣金中扣
						$yong_res = M('user')->where("id = {$aid}")->setDec("yong",$fs_date['fs_money']);

						$fs_res = M('fs_date')->add($fs_date);

						foreach ($list as $key => $value) {
							$list[$key]['fs_id'] = $fs_res;
							$list[$key]['date'] = $time;
							$list[$key]['water'] = $value['del_points'];

							if ($value['game'] == 'pk10' || $value['game'] == 'xyft') {
								$list[$key]['money'] = $value['del_points']  * $t_userinfo['pkft_fs'] *0.01;
							} else if($value['game'] == 'ssc') {
								$list[$key]['money'] = $value['del_points']  * $t_userinfo['ssc_fs'] *0.01;
							}  else if($value['game'] == 'k3') {
								$list[$key]['money'] = $value['del_points']  * $t_userinfo['k3'] *0.01;
							}
							else {
								$list[$key]['money'] = $value['del_points']  * $t_userinfo['pcdd_fs'] *0.01;
							}
							
							//给会员凡事操作
							$res = M('user')->where("id={$value['uid']}")->setInc('fanshui', $list[$key]['money']);
						}

						$fs_detail_res = M('fs_details')->addAll($list);
					}
				} else {
					$time = date('Y-m-d',time()-3600*24);
					
					//查询当天是否已返水
					$is_fs = M('fs_date')->where("fs_date='{$time}'")->find();
					if ($is_fs) {
						return;
					}

					//查询当天所有的订单,以人分组
					$start_time = strtotime($time.' 00:00:00');
					$end_time = strtotime($time.' 23:59:59');

					$map['time'] = array(array('egt',$start_time),array('elt',$end_time),'and');
					$map['state'] = 1;

					$order = M("order");
					$list = $order->field("userid as uid,nickname,SUM(add_points) AS add_points,SUM(del_points) AS del_points,game")->where($map)->group('userid')->select();

					$fs_date = array(
						'fs_date' => $time,
						'add_time' => time()
					);
					$fs_date['water'] = 0;

					foreach ($list as $key => $value) {
						$fs_date['water'] += $value['del_points'];
						if ($value['game'] == 'pk10' || $value['game'] == 'xyft') {
							$fs_water['pkft'] += $value['del_points'] * C('pkft_fs_rate') *0.01;
						} else if($value['game'] == 'ssc') {
							$fs_water['ssc'] += $value['del_points'] * C('ssc_fs_rate') *0.01;
						} else {
							$fs_water['pcdd'] += $value['del_points'] * C('pcdd_fs_rate') *0.01;
						}
					}

					$fs_date['fs_money'] = $fs_water['pkft'] + $fs_water['ssc'] + $fs_water['pcdd'] ;


					$fs_res = M('fs_date')->add($fs_date);

					foreach ($list as $key => $value) {
						$list[$key]['fs_id'] = $fs_res;
						$list[$key]['date'] = $time;
						$list[$key]['water'] = $value['del_points'];

						if ($value['game'] == 'pk10' || $value['game'] == 'xyft') {
							$list[$key]['money'] = $value['del_points']  * C('pkft_fs_rate') *0.01;
						} else if($value['game'] == 'ssc') {
							$list[$key]['money'] = $value['del_points']  * C('ssc_fs_rate') *0.01;
						} else {
							$list[$key]['money'] = $value['del_points']  * C('pcdd_fs_rate') *0.01;
						}
						
						//给会员凡事操作
						$res = M('user')->where("id={$value['uid']}")->setInc('fanshui', $list[$key]['money']);
					}

					$fs_detail_res = M('fs_details')->addAll($list);
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
		
	}
	
	/**
	 * onClose
	 * 关闭连接
	 * @access public
	 * @param  void
	 * @return void
	 */
	public function onClose($connection) {
		
	}
}
?>