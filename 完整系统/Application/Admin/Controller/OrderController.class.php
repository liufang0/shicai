<?php

namespace Admin\Controller;
use Think\Controller;

class OrderController extends BaseController{
	
	public function array_sort($array,$key){
		if(is_array($array)){
			$key_array = null;
			$new_array = null;
			for($i=0;$i<count($array);$i++){
				$key_array[$array[$i][$key]] = $i;
			}
			krsort($key_array);
			$j=0;
			foreach($key_array as $k=>$v){
				$new_array[$j] = $array[$v];
				$j++;
			}
			unset($key_array);
			return $new_array;
		}else{
			return $array;
		}
	}
	
	public function index(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

		$nickname = I('nickname');
		$uid = I('uid');
		$starttime =str_replace("+"," ",I('starttime'));
		$endtime = str_replace("+"," ",I('endtime'));
		$game = I('game');

		if($nickname){
			if (is_numeric($nickname)) {
				$map['username'] = $nickname;
			} else{
				$map['nickname'] = $nickname;
			}
		}
		
		if($uid){
			$map['userid'] = $uid;
		}

		if ($game) {
			$map['game'] = $game;
		}

		if($starttime && $endtime){
			$start = strtotime($starttime);
			$end = strtotime($endtime);
			$map['time'] = array(array('egt',$start),array('elt',$end),'and');
		}
		$map['is_under'] = 1;
		$order = M('order');
		$count = $order->where($map)->count();
		$page = new \Think\Page($count,10);
		$show = $page->show();
		$list1 = array();
		$list1 = $order->where($map)->limit($page->firstRow.','.$page->listRows)->order("id DESC")->select();


		for($i=0;$i<count($list1);$i++){
			if($list1[$i]['userid']){
				$list1[$i]['user'] = M('user')->where("id = {$list1[$i]['userid']}")->find();
			}else{
				$list1[$i]['user'] = M('user')->where("id = {$list1[$i]['uid']}")->find();
			}
		}
		
		$this->assign('list',$list1);
		$this->assign('show',$show);
		$this->assign('nickname',$nickname);
		$this->assign('uid',$uid);
		$this->assign('game',$game);
		$this->assign('starttime',$starttime);
		$this->assign('endtime',$endtime);
		$this->display();
	}

	public function del(){
		$id = I('id');
		if($id){
			$order = M('order')->where("id={$id}")->find();
			$res = M('order')->where("id = $id")->delete();
			M('user')->where("id = {$order['t_id']}")->setDec('yong',$order['del_points']*C('fenxiao')*0.01);
			M('user')->where("id = {$order['t_id']}")->setDec('t_add', $order['del_points']*C('fenxiao')*0.01);
			M('push_money')->where("orderid={$id}")->delete();
			
			if($res){
				$this->success('删除成功！');
			}else{
				$this->error('删除失败！');
			}
		}
	}

	public function del_order(){
		if(IS_POST){
			if(!IS_AJAX){
				$this->error('提交方式不正确！');
			}else{
				$starttime = I('starttime');
				$endtime = I('endtime');
				if (!$starttime || !$endtime) {
					$this->error("请选择时间");
				}

				$map['time'] = array(array('egt',strtotime($starttime)),array('elt',strtotime($endtime)),'and');
				$res = M('order')->where($map)->delete();
				if ($res) {
					$this->success('删除成功!',U('Admin/Order/index'),1);
				} else {
					$this->error('删除失败');
				}
			}
		} else {
			$this->display();
		}
	}



	public function admin_cancel(){
		$id = I('id');
		if($id){
			$order = M('order')->where("id={$id}")->find();
			if ($order['add_points']) {
				$add = $order['add_points']-$order['del_points'];
				$res = M('user')->where("id = {$order['userid']}")->setDec('points',$add);
				$res1 = M('order')->where("id = $id")->setField(array('is_add'=>'1','del_points'=>0,'add_points'=>0));
			} else{
				$points = $order['del_points'];
				$res = M('user')->where("id = {$order['userid']}")->setInc('points',$points);
				$res1 = M('order')->where("id = $id")->setField(array('is_add'=>'1','del_points'=>0,'add_points'=>0));
			}
		
			if($res && $res1){
				$this->success('取消成功');
			} else {
				$this->error('取消失败！');
			}
		}
	}
	
	//每日输赢
	public function win_lose(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

		$map['state'] = 1;

		$time = I('time');
		$start = strtotime(str_replace("+"," ",I('starttime'))) ? : mktime(0,0,0,date('m'),date('d'),date('Y'));
		$end = strtotime(str_replace("+"," ",I('endtime')))  ? : mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
		
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

		$map['time'] = array(array('egt',$start),array('elt',$end),'and');
		$map['game'] = I('game');
		$order = M('order');
		$list = $order->field("SUM(add_points) AS add_points,SUM(del_points) AS del_points")->where($map)->find();
	
		$this->assign('nickname',$nickname);
		$this->assign('game',I('game'));
		$this->assign('del',$list['del_points']);
		$this->assign('add',$list['add_points']) ;
		$this->assign('ying',$list['del_points']-$list['add_points']);
		$this->assign('start',date('Y-m-d H:i:s',$start));
		$this->assign('end',date('Y-m-d H:i:s',$end));
		$this->display();
	}
	
	
	//用户每日输赢
	public function user_win_lose(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

		$nickname = I('nickname');
		$time = I('time');
		$map = array();
		if($nickname){
			if (is_numeric($nickname)) {
				$map['username'] = $nickname;
			} else{
				$map['nickname'] = $nickname;
			}
		}

		$map['state'] = 1;
		$time = I('time');
		$start = strtotime(str_replace("+"," ",I('starttime'))) ? : mktime(0,0,0,date('m'),date('d'),date('Y'));
		$end = strtotime(str_replace("+"," ",I('endtime'))) ? : mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
		
		if($time){
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

		$map['time'] = array(array('egt',$start),array('elt',$end),'and');
		$map['game'] = I('game');
		$order = M('order');

		$count = $order->where($map)->group('userid')->select();
		$page = new \Think\Page(count($count),10);
		$show = $page->show();

		$list = $order->field("userid,nickname,SUM(add_points) AS add_points,SUM(del_points) AS del_points")->where($map)->group('userid')->limit($page->firstRow.','.$page->listRows)->select();
		foreach ($list as $key => $value) {
			$list[$key]['ying'] = $value['add_points'] - $value['del_points'];
			$userinfo = M('user')->where("id={$value['userid']}")->field("headimgurl,username,t_id")->find();
			$list[$key]['headimgurl'] = $userinfo['headimgurl'];	
			$list[$key]['username'] = $userinfo['username'];	
			$list[$key]['t_id'] = $userinfo['t_id'];	
			$list[$key]['remark'] = $userinfo['remark'];	
		}
		
		$this->assign('game',I('game'));
		$this->assign('nickname',$nickname);
		$this->assign('list',$list);
		$this->assign('time',$time);
		$this->assign('show',$show);
		$this->assign('start',date('Y-m-d H:i:s',$start));
		$this->assign('end',date('Y-m-d H:i:s',$end));
		$this->display();
	}
}


?>