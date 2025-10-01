<?php

namespace Admin\Controller;
use Think\Controller;

class IntegralController extends BaseController{
	
	public function lists(){
		$nickname = I('nickname');
		$uid = I('uid');
		$integral = M('integral');
		$time = I('time');
		$map = array();
		$start = strtotime(str_replace("+"," ",I('starttime')));
		$end = strtotime(str_replace("+"," ",I('endtime')));
		
		if($time){
			// $start = strtotime($time.'00:00:00');
			// $end = strtotime($time.'23:59:59');
			if ($time == 'today') {
				$start=mktime(0,0,0,date('m'),date('d'),date('Y'));
				$end=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
			} elseif($time == 'yestoday') {
				$start=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
				$end=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
			} elseif($time == 'week') {
				$start=mktime(0,0,0,date('m'),date('d')-6,date('Y'));
				$end=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
			}  elseif($time == 'month') {
				$start=mktime(0,0,0,date('m'),date('d')-29,date('Y'));
				$end=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
			}
		}

		if ($start && $end) {
			$map['time'] = array(array('egt',$start),array('elt',$end),'and');
		}

		
		$sum_data = array(
			'add' => 0,
			'xia' => 0,
		);
	
		if($nickname){
			if (is_numeric($nickname)) {
				$map['username'] = $nickname;
				$count = $integral->where($map)->count();
			$sum_add = $integral->field("sum(points) as sum_add")->where("type = 1 and username like '%{$nickname}%'")->where($map)->find();
			$sum_data['add'] = $sum_add['sum_add'];
			$sum_xia = $integral->field("sum(points) as sum_add")->where("type = 0 and username like '%{$nickname}%'")->where($map)->find();
			$sum_data['xia'] = $sum_xia['sum_add'];
			$page = new \Think\Page($count,10);
			$show = $page->show();
			$list = $integral->where($map)->limit($page->firstRow.','.$page->listRows)->order("id DESC")->select();
				
			}else{
				$map['nickname'] = $nickname;
				$count = $integral->where($map)->count();
			$sum_add = $integral->field("sum(points) as sum_add")->where("type = 1 and nickname like '%{$nickname}%'")->where($map)->find();
			$sum_data['add'] = $sum_add['sum_add'];
			$sum_xia = $integral->field("sum(points) as sum_add")->where("type = 0 and nickname like '%{$nickname}%'")->where($map)->find();
			$sum_data['xia'] = $sum_xia['sum_add'];
			$page = new \Think\Page($count,10);
			$show = $page->show();
			$list = $integral->where($map)->limit($page->firstRow.','.$page->listRows)->order("id DESC")->select();
			}


		}elseif($uid){
			
			$map['uid'] = $uid;
				$count = $integral->where($map)->count();
				$sum_add = $integral->field("sum(points) as sum_add")->where("type = 1")->where($map)->find();
				$sum_data['add'] = $sum_add['sum_add'];
				$sum_xia = $integral->field("sum(points) as sum_add")->where("type = 0")->where($map)->find();
				$sum_data['xia'] = $sum_xia['sum_add'];
				$page = new \Think\Page($count,10);
				$show = $page->show();
				$list = $integral->where($map)->limit($page->firstRow.','.$page->listRows)->order("id DESC")->select();
		}else{
			$count = $integral->where($map)->count();
			$page = new \Think\Page($count,10);
			$show = $page->show();
			$sum_add = $integral->field("sum(points) as sum_add")->where("type = 1")->where($map)->find();
			$sum_data['add'] = $sum_add['sum_add'];
			$sum_xia = $integral->field("sum(points) as sum_add")->where("type = 0")->where($map)->find();
			$sum_data['xia'] = $sum_xia['sum_add'];
			$list = $integral->where($map)->limit($page->firstRow.','.$page->listRows)->order("id DESC")->select();
		}
		for($i=0;$i<count($list);$i++){
			$list[$i]['user'] = M('user')->where("id = {$list[$i]['uid']}")->find();
		}

		$startline =  $start ? date('Y-m-d H:i:s',$start) : '';
		$endline =  $start ? date('Y-m-d H:i:s',$end) : '';

		$this->assign('nickname',$nickname);
		$this->assign('start',$startline);
		$this->assign('end',$endline);
		$this->assign('list',$list);
		$this->assign('show',$show);
		$this->assign('sum_data',$sum_data);
		$this->assign('nickname',$nickname);
		$this->assign('uid',$uid);
		$this->assign('time',$time);
		$this->display('lists');
	}
	
	protected function send($content){
		// 指明给谁推送，为空表示向所有在线用户推送
		$to_uid = "";
		// 推送的url地址，上线时改成自己的服务器地址
		$push_api_url = "http://127.0.0.1:12224/";
		$post_data = array(
		   "type" => "publish",
		   "content" => json_encode($content),
		   "to" => $to_uid, 
		);
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $push_api_url );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
		curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Expect:"));
		$return = curl_exec ( $ch );
		curl_close ( $ch );
		return $return;
	}
	
	public function gonggao(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}
    	
		if(IS_POST){
			$gongao = I('gonggao');
			$message  = array(
				'time'=>date('H:i:s'),
				'content'=>$gongao
			);
			$res = $this->send($message);
			$new_message = array(
				'type' => 'system',
				'head_img_url'=>'/Public/main/img/system.jpg',
				'from_client_name' => '客服',
				'content'=>$gongao,
				'time'=>date('H:i:s')
			);
			if($res){
				$message = M('message');
				$message->add($new_message);
				$this->success('成功');
			}else{
				$this->error('失败');
			}
		}else{
			$this->display();
		}
	}
	
	public function xxgonggao(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}
    	
		if(IS_POST){
			$gongao = I('gonggao');
			$title = I('title');
			
			$new_message = array(
				'title'=>$title,
				'content'=>$gongao,
				'time'=>date('H:i:s')
			);
			
			$message = M('xxmessage');
			$message->add($new_message);
			$this->success('成功');
			

			
		}else{
			$this->display();
		}
	}
	
	public function gdxx(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}
    	$message = M('gdxx');
		if(IS_POST){
			$content = I('gonggao');
	
			$new_message['content'] = $content ;
		
			$message->where('id=1')->save($new_message);

			
			$this->success('成功');
		}else{
			$list = $message->where('id=1')->find();
			$this->assign('content',$list);
			$this->display();
		}
	}
	
	public function index(){
		if(IS_POST){
			if(!IS_AJAX){
				$this->error('提交方式不正确');
			}else{
				$userid = I('userid');
				$points = I('points');
				$type = I('type');
				if(!preg_match('/^[1-9]\d*$/',$points)){
					$this->error('充值点数为正整数');
				}
				$res2 = M('user')->where("id = $userid")->setInc('points',$points);
				if($res2){
					$info = M('user')->where("id = $userid")->find();
					
					//充值记录
					$data['uid'] = $userid;
					$data['t_id'] = $info['t_id'];
					$data['time'] = time();
					$data['points'] = $points;
					$data['type'] = '1';
					$data['nickname'] = $info['nickname'];
					$data['username'] = $info['username'];
					$data['ip'] = get_client_ip();
					$data['balance'] = $info['points'];
					M('integral')->add($data);
					
					//是否有人推荐
					if($info['t_id'] and C('fenxiao_set') == 1){
						if($points>=C('fenxiao_min')){//最低充值
							M('user')->where("id = {$info['t_id']}")->setInc('yong',$points*C('fenxiao')*0.01);
							M('user')->where("id = {$info['t_id']}")->setInc('t_add',$points*C('fenxiao')*0.01);
							$fx_data = array(
								'uid' => $userid,
								't_uid' => $info['t_id'],
								'push_money' => $points,
								'rate' => C('fenxiao')*0.01,
								'money' => $points*C('fenxiao')*0.01,
								'time' => time()
							);
							M('push_money')->add($fx_data);
						}
					}
					$log = array(
						'username' => session('admin')['username'],
						'type' => 1,
						'addtime' => time(),
						'content' => "给玩家".$data['nickname']."上分，上分金额{$points}"
					);
					M('admin_log')->add($log);

					$message = array(
						'to'=>$userid,
						'type' => 'system',
						'head_img_url'=>'/Public/main/img/kefu.jpg',
						'from_client_name' => '客服',
						'time'=>date('H:i:s'),
						'content'=>'玩家「'.$info['nickname'].'」上分已受理，请注意查看点数'
					);
					M('message')->add($message);
					$message['points'] = $points;
					$this->send($message);
					if ($type == 2) {
						$fid = I('fid');
						$fen_add = array(
							'balance' => $data['balance'],
							'status' => 1,
							'sftime' => time()
						);
						M('fenadd')->where("id={$fid}")->save($fen_add);

						$this->success('充值成功,跳转中~',U('Admin/Fen/addlist'),1);
					}else{
						$this->success('充值成功,跳转中~',U('Admin/Member/index'),1);
					}
					
				}else{
					$this->error('充值失败！');
				}
			}
		}else{
			$id = I('id');
			$type = I('type');
			$fid = I('fid');
			$userinfo = M('user')->where("id = $id")->find();
			$points = M('fenadd')->where("id= $fid")->find();
			$points = explode('.',$points['money'])[0];
			$this->assign('userinfo',$userinfo);
			$this->assign('type',$type);
			$this->assign('points',$points);
			$this->assign('fid',$fid);
			$this->display();
		}
		
	}
	
	public function index2(){
		$file_tmp="tmp.txt";
		file_put_contents($file_tmp, '测试写入',FILE_APPEND);
		//软件接口配置
		$key_="ert34543";//接口KEY  自己修改下 软件上和这个设置一样就行
		$md5key="3gdf3ewr";//MD5加密字符串 自己修改下 软件上和这个设置一样就行
		//软件接口地址 http://域名/mcbpay/apipay.php?payno=#name&tno=#tno&money=#money&sign=#sign&key=接口KEY
	
		$getkey=$_REQUEST['key'];//接收参数key
		$tno=$_REQUEST['tno'];//接收参数tno 交易号
		$payno=$_REQUEST['payno'];//接收参数payno 一般是用户名 用户ID
		$money=$_REQUEST['money'];//接收参数money 付款金额
		$sign=$_REQUEST['sign'];//接收参数sign
		$typ=(int)$_REQUEST['typ'];//接收参数typ
		if($typ==1){
			$typname='手工充值';
		}else if($typ==2){
			$typname='支付宝充值';
		}else if($typ==3){
			$typname='财付通充值';
		}else if($typ==4){
			$typname='手Q充值';
		}else if($typ==5){
			$typname='微信充值';
		}
		
		if(!$tno)exit('没有订单号');
		if(!$payno)exit('没有付款说明');
		if($getkey!=$key_)exit('KEY错误');
		if(strtoupper($sign)!=strtoupper(md5($tno.$payno.$money.$md5key)))exit('签名错误');
			
		$userid = $payno;
		$points = intval($money);
		if(!preg_match('/^[1-9]\d*$/',$points)){
			$this->error('充值点数为正整数');
		}
		$res2 = M('user')->where("id = $userid")->setInc('points',$points);
		if($res2){
			$info = M('user')->where("id = $userid")->find();
			
			//充值记录
			$data['uid'] = $userid;
			$data['t_id'] = $info['t_id'];
			$data['time'] = time();
			$data['points'] = $points;
			$data['type'] = '1';
			$data['nickname'] = $info['nickname'];
			$data['username'] = $info['username'];
			$data['ip'] = get_client_ip();
			$data['balance'] = $info['points'];
			M('integral')->add($data);
			
			//是否有人推荐
			if($info['t_id'] and C('fenxiao_set') == 1){
				if($points>=C('fenxiao_min')){//最低充值
					M('user')->where("id = {$info['t_id']}")->setInc('yong',$points*C('fenxiao')*0.01);
					M('user')->where("id = {$info['t_id']}")->setInc('t_add',$points*C('fenxiao')*0.01);
					$fx_data = array(
						'uid' => $userid,
						't_uid' => $info['t_id'],
						'push_money' => $points,
						'rate' => C('fenxiao')*0.01,
						'money' => $points*C('fenxiao')*0.01,
						'time' => time()
					);
					M('push_money')->add($fx_data);
				}
			}
			$log = array(
				'username' => session('admin')['username'],
				'type' => 1,
				'addtime' => time(),
				'content' => "给玩家".$data['nickname']."上分，上分金额{$points}"
			);
			M('admin_log')->add($log);

			$message = array(
				'to'=>$userid,
				'type' => 'system',
				'head_img_url'=>'/Public/main/img/kefu.jpg',
				'from_client_name' => '客服',
				'time'=>date('H:i:s'),
				'content'=>'玩家「'.$info['nickname'].'」上分已受理，请注意查看点数'
			);
			M('message')->add($message);
			$message['points'] = $points;
			$this->send($message);
			if ($type == 2) {
				$fid = I('fid');
				$fen_add = array(
					'balance' => $data['balance'],
					'status' => 1,
					'sftime' => time()
				);
				M('fenadd')->where("id={$fid}")->save($fen_add);

				$this->success('充值成功,跳转中~',U('Admin/Fen/addlist'),1);
			}else{
				$this->success('充值成功,跳转中~',U('Admin/Member/index'),1);
			}
			
		}else{
			$this->error('充值失败！');
		}
		
	}

	public function agent_index(){
		if(IS_POST){
			if(!IS_AJAX){
				$this->error('提交方式不正确');
			}else{
				$userid = I('userid');
				$points = I('points');
				$type = I('type');
				if(!preg_match('/^[1-9]\d*$/',$points)){
					$this->error('充值点数为正整数');
				}
				$res2 = M('user')->where("id = $userid")->setInc('agent_fen',$points);
				if($res2){
					$this->success('充值成功,跳转中~',U('Admin/Member/push'),1);
				}else{
					$this->error('充值失败！');
				}
			}
		}else{
			$id = I('id');
			$userinfo = M('user')->where("id = $id")->find();
			$this->assign('userinfo',$userinfo);
			$this->display();
		}
		
	}

	protected function send_fen($content){
		// 指明给谁推送，为空表示向所有在线用户推送
		$to_uid = "";
		// 推送的url地址，上线时改成自己的服务器地址
		$push_api_url = "http://127.0.0.1:12225/";
		$post_data = array(
		   "type" => "publish",
		   "content" => json_encode($content),
		   "to" => $to_uid, 
		);
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $push_api_url );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
		curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Expect:"));
		$return = curl_exec ( $ch );
		curl_close ( $ch );
		return $return;
	}

	
	public function under(){
		if(IS_POST){
			if(!IS_AJAX){
				$this->error('提交方式不正确！');
			}else{
				$userid = I('userid');
				$points = I('points');
				$type = I('type');
				
				 
				if(!preg_match('/^[1-9]\d*$/',$points)){
					$this->error('兑换点数为正整数');
				}
				$info = M('user')->where("id = $userid")->find();
				if($info['points']<$points){
					$this->error('点数不足');
				}
				$res2 = M('user')->where("id = $userid")->setDec('points',$points);
				//$res2 = M('user')->where("id = $userid")->find();
				//$res2 = true;
				if($res2){
					
					//下分记录
					$data['uid'] = $userid;
					$data['t_id'] = $info['t_id'];
					$data['time'] = time();
					$data['points'] = $points;
					$data['type'] = '0';
					$data['nickname'] = $info['nickname'];
					$data['username'] = $info['username'];
					$data['ip'] = get_client_ip();
					$data['balance'] = $info['points'];
					M('integral')->add($data);
					
					$log = array(
						'username' => session('admin')['username'],
						'type' => 2,
						'addtime' => time(),
						'content' => "给玩家".$data['nickname']."下分，下分金额{$points}"
					);
					M('admin_log')->add($log);

					$message = array(
						'to'=>$userid,
						'type' => 'system',
						'head_img_url'=>'/Public/main/img/kefu.jpg',
						'from_client_name' => '客服',
						'time'=>date('H:i:s'),
						'content'=>'玩家「'.$info['nickname'].'」回分已受理，请确认'
					);
					M('message')->add($message);
					$message['points'] = $points*(-1);
					$this->send($message);
					if ($type=2) {
						$fid = I('fid');
						$fen_add = array(
							'balance' => $data['balance'],
							'status' => 1,
							'xftime' => time()
						);
						M('fenxia')->where("id={$fid}")->save($fen_add);

						$message  = array(
							'time'=>date('H:i:s'),
							'type' => 4,
							'content'=>"下分信息处理"
						);
						$res = $this->send_fen($message);

						$this->success('下分成功,跳转中~',U('Admin/Fen/xialist'),1);
					}else{
						$this->success('下分成功,跳转中~',U('Admin/Member/index'),1);
					}
					
				}else{
					$this->error('下分失败！');
				}
			}
		}else{
			$id = I('id');
			$type = I('type');
			$fid = I('fid');
			$userinfo = M('user')->where("id = $id")->find();
			$points = M('fenxia')->where("id= $fid")->find();
			$points = explode('.',$points['money'])[0];
			$this->assign('userinfo',$userinfo);
			$this->assign('type',$type);
			$this->assign('points',$points);
			$this->assign('fid',$fid);
			$this->display();
		}
	}
	
}


?>