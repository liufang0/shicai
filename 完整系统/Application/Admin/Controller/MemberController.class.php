<?php

namespace Admin\Controller;
use Think\Controller;

class MemberController extends BaseController{
	
	public function index(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

		$nickname = I('nickname');
		$uid = I('uid');
		if($nickname || $uid){
			if($nickname){
				if (is_numeric($nickname)) {
					$map['username'] = $nickname;
				} else{
					$map['nickname'] = $nickname;
				}
			}
			if($uid){
				$map['id'] = $uid;
			}
			$member = M('user');
			$count = $member->where($map)->count();
			$page = new \Think\Page($count,10);
			$show = $page->show();
			$list = $member->where($map)->limit($page->firstRow.','.$page->listRows)->order("id ASC")->select();
		}else{
			$member = M('user');
			$count = $member->count();
			$page = new \Think\Page($count,10);
			$show = $page->show();
			$list = $member->limit($page->firstRow.','.$page->listRows)->order("id ASC")->select();
		}
		
		//系统余分
		$yu_fen = M('user')->field("sum(points) as sum_points")->find();
		
		$this->assign('yufen',$yu_fen['sum_points']);
		$this->assign('show',$show);
		$this->assign('uid',$uid);
		$this->assign('nickname',$nickname);
		$this->assign('list',$list);
		$this->display();
	}
	
	public function disable(){
		$id = I('id');
		$res = M('user')->where("id = $id")->setField('status',0);
		if($res){
			$this->success('禁用成功！');
		}else{
			$this->error('禁用失败！');
		}
	}

	public function set_robot(){
		$id = I('id');
		$res = M('user')->where("id = $id")->setField('is_robot',1);
		if($res){
			$this->success('设置成功！');
		}else{
			$this->error('设置失败');
		}
	}

	public function cancel_robot(){
		$id = I('id');
		$res = M('user')->where("id = $id")->setField('is_robot',0);
		if($res){
			$this->success('取消成功！');
		}else{
			$this->error('取消失败！');
		}
	}

	public function set_agent(){
		$id = I('id');
		$res = M('user')->where("id = $id")->setField('is_agent',1);
		if($res){
			$this->success('设置代理成功！');
		}else{
			$this->error('设置代理失败');
		}
	}

	public function cancel_agent(){
		$id = I('id');
		$res = M('user')->where("id = $id")->setField('is_agent',0);
		if($res){
			$this->success('取消代理成功！');
		}else{
			$this->error('取消代理失败！');
		}
	}

	public function delete(){
		$id = I('id');
		$res = M('user')->where("id = $id")->delete();
		if ($res) {
			M('wx')->where("userid = $id")->delete();
		}
		if($res){
			$this->success('删除成功！');
		}else{
			$this->error('删除失败！');
		}
	}
	
	public function set_win(){
		$id = I('id');
		M('user')->where("1 = 1")->setField('xjp28_is_win',0);
		M('user')->where("id = $id")->setField('xjp28_is_lost',0);
		$res = M('user')->where("id = $id")->setField('xjp28_is_win',1);
		if($res){
			$this->success('设置赢家成功！');
		}else{
			$this->error('设置赢家失败！');
		}
	}
	
	public function cancel_set_win(){
		$id = I('id');
		$res = M('user')->where("id = $id")->setField('xjp28_is_win',0);
		if($res){
			$this->success('取消赢家成功！');
		}else{
			$this->error('取消赢家失败！');
		}
	}
	
	public function set_lost(){
		$id = I('id');
		M('user')->where("1 = 1")->setField('xjp28_is_lost',0);
		M('user')->where("id = $id")->setField('xjp28_is_win',0);
		$res = M('user')->where("id = $id")->setField('xjp28_is_lost',1);
		if($res){
			$this->success('设置输家成功！');
		}else{
			$this->error('设置输家失败！');
		}
	}
	
	public function cancel_set_lost(){
		$id = I('id');
		$res = M('user')->where("id = $id")->setField('xjp28_is_lost',0);
		if($res){
			$this->success('取消输家成功！');
		}else{
			$this->error('取消输家失败！');
		}
	}
	
	
	public function endisable(){
		$id = I('id');
		$res = M('user')->where("id = $id")->setField('status',1);
		if($res){
			$this->success('启用成功！');
		}else{
			$this->error('启用失败！');
		}
	}
	
	//推广  获取代理会员
	public function push(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

		$map = array();

		$nickname = I('nickname');
		if($nickname){
			if (is_numeric($nickname)) {
				$map['username'] = $nickname;
			} else{
				$map['nickname'] = $nickname;
			}
		}

		$map['is_agent'] = 1;
    	
		$member = M('user');
		$count = $member->where($map)->count();
		$page = new \Think\Page($count,10);
		$show = $page->show();
		$list = $member->where($map)->order("id ASC")->select();

		$siteurl = $_SERVER['SERVER_NAME'];
		//查看下线总数
		foreach ($list as $key => $value) {
			$t_account = M('user')->where("t_id = {$value['id']}")->count();
			$list[$key]['t_account'] = $t_account;
			$url = 'http://'.$siteurl.'?t='.$value['id'];
			$list[$key]['url'] = $url;
		}

		$list_new = $this->_arraysort($list,'t_account');
		$list_slice = array_slice($list_new, $page->firstRow, $page->listRows);

		$this->assign('show',$show);
		$this->assign('list',$list_slice);
		$this->assign('nickname',$nickname);
		$this->display();
	}


	public function set_tui(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

    	$id = I('id');

		if(IS_POST){
			if(!IS_AJAX){
				$this->error('提交方式不正确');
			}else{
				$file = I('file0');//获取图片路径
                $checkpic = I('checkpic');
                $oldcheckpic = I('oldcheckpic');
				
                if ($checkpic != $oldcheckpic) {
                    $upload = new \Think\Upload();// 实例化上传类
                    $upload->maxSize = 3145728;// 设置附件上传大小
                    $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                    $upload->rootPath = './Uploads/carousel/kefu/'; // 设置附件上传根目录
                    $upload->savePath = ''; // 设置附件上传（子）目录
                    $upload->saveRule = 'time';
                    $info = $upload->upload();

                    if ($info) {
                        $img_url = '/Uploads/carousel/kefu/' . $info[file0][savepath] . $info[file0][savename];//如果上传成功则完成路径拼接
                    } else {
                        $this->error($upload->getError());//否则就是上传错误，显示错误原因
                    }
                }
				if ($checkpic != $oldcheckpic) {
                    $data['qrcode'] = $img_url;
					
                }
				if($data['qrcode']){
					$result = M('user')->where("id={$id}")->save($data);
				}
				if($result){
					$this->success('修改成功');
				}else{
					$this->error('修改失败');
				}
			}
		}else{
			$info = M('user')->where("id={$id}")->find();
			$this->assign('info',$info);
			$this->display();
		}
	}

		//推广  获取代理会员
	public function pushxx(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

		$id = I('id');
		$member = M('user');
		$count = $member->where("t_id = {$id}")->count();
		$page = new \Think\Page($count,10);
		$show = $page->show();

		//直属下线列表
		$list = $member->where("t_id = {$id}")->limit($page->firstRow.','.$page->listRows)->order("id ASC")->select();


		foreach ($list as $key => $value) {
			$yong = M('push_money')->where("uid={$value['id']}")->field("SUM(money) as money")->find();
			$list[$key]['yong'] = $yong['money'] ? : 0;
		}
		
		//直属线下列表id
		$all_off = $member->field("id")->where("t_id = {$id}")->select();
		//三级下级列表id
		$ids = array();
		$ids2 = array();
		$ids3 = array();
		foreach ($all_off as $key => $value) {
			$ids[] = $value['id'];
			$all_off2 = $member->field("id")->where("t_id = {$value['id']}")->select();
			if (!empty($all_off2)) {
				foreach ($all_off2 as $key2 => $value2) {
					$ids2[] = $value2['id'];
					$all_off3 = $member->field("id")->where("t_id = {$value2['id']}")->select();
					if (!empty($all_off3)) {
						foreach ($all_off3 as $key3 => $value3) {
							$ids3[] = $value3['id'];
						}
					}
				}
			}
		}
		$all_ids = array_merge($ids,$ids2,$ids3);
		$ids = implode(',', $ids);
		$all_ids = implode(',', $all_ids);

		$time = I('time');
		$start = strtotime(str_replace("+"," ",I('starttime')));
		$end = strtotime(str_replace("+"," ",I('endtime')));
		
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

		$map = array();
		if ($start && $end) {
			$map['time'] = array(array('egt',$start),array('elt',$end),'and');
		}
		
		//每个会员的流水
		foreach ($list as $key => $value) {
			$user_order = M("order")->field("sum(add_points) as sum_add,sum(del_points) as sum_del")->where("userid={$value['id']} and state=1")->where($map)->find();
			$list[$key]['add_points'] = $user_order['sum_add'];
			$list[$key]['del_points'] = $user_order['sum_del'];
			$list[$key]['ying'] = $user_order['sum_add'] - $user_order['sum_del'];
		}

		$list_new = $this->_arraysort($list,'add_points');
		
		//所有下线上下分记录
		$add_fen = M('integral')->field("sum(points) as add_fen")->where("type = 1 and uid in (".$ids.")")->where($map)->find();

		$xia_fen = M('integral')->field("sum(points) as xia_fen")->where("type = 0 and uid in (".$ids.")")->where($map)->find();
		
		$integral['add_fen'] = $add_fen['add_fen'];
		$integral['xia_fen'] = $xia_fen['xia_fen'];

		//所有下线进项、出项流水
		$order = M("order")->field("sum(add_points) as sum_add,sum(del_points) as sum_del")->where("state=1 and userid in (".$ids.")")->where($map)->find();

		$order['ying'] = $order['sum_add'] - $order['sum_del'];

		//所得所佣金
		$yong = M('push_money')->field("sum(money) as sum_yong")->where("uid in (".$ids.")")->where($map)->find();	

		//所有下三级下线的数据统计
		$add_fen_all = M('integral')->field("sum(points) as add_fen")->where("type = 1 and uid in (".$all_ids.")")->where($map)->find();

		$xia_fen_all = M('integral')->field("sum(points) as xia_fen")->where("type = 0 and uid in (".$all_ids.")")->where($map)->find();
		
		$integral_all['add_fen'] = $add_fen_all['add_fen'];
		$integral_all['xia_fen'] = $xia_fen_all['xia_fen'];

		//所有下线进项、出项流水
		$order_all = M("order")->field("sum(add_points) as sum_add,sum(del_points) as sum_del")->where("state=1 and userid in (".$all_ids.")")->where($map)->find();

		$order_all['ying'] = $order_all['sum_add'] - $order_all['sum_del'];

		//所得所佣金
		$yong_all = M('push_money')->field("sum(money) as sum_yong")->where("uid in (".$all_ids.")")->where($map)->find();	

		$this->assign('id',$id);
		$this->assign('show',$show);
		$this->assign('list',$list_new);
		$this->assign('integral',$integral);
		$this->assign('order',$order);
		$this->assign('yong',$yong);
		$this->assign('integral_all',$integral_all);
		$this->assign('order_all',$order_all);
		$this->assign('yong_all',$yong_all);
		$this->assign('start',$start ?  date('Y-m-d H:i:s',$start) : '');
		$this->assign('end',$end ? date('Y-m-d  H:i:s',$end) : '');
		$this->display();
	}

	//推广  获取代理会员
	public function pushjs(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

		$id = I('id');
		$map = array();

		$map_member = array();
		$map_member['t_id'] = $id ? $id : 0;

		$username = I('username');
		if ($username) {
			$map_member['username'] = $username;
		}

		$member = M('user');
		$count = $member->where($map_member)->count();
		$page = new \Think\Page($count,10);
		$show = $page->show();
		$list = $member->where($map_member)->order("id ASC")->select();

		$siteurl = $_SERVER['SERVER_NAME'];

		$start = strtotime(str_replace("+"," ",I('starttime')));
		$end = strtotime(str_replace("+"," ",I('endtime')));
		
		if ($start && $end) {
			$map['time'] = array(array('egt',$start),array('elt',$end),'and');
		}


		//查看下线总数
		foreach ($list as $k => $v) {
			//直属线下列表id
			$all_off = $member->field("id")->where("t_id = {$v['id']}")->select();
			//三级下级列表id
			$ids = array();
			$ids2 = array();
			$ids3 = array();
			foreach ($all_off as $key => $value) {
				$ids[] = $value['id'];
				$all_off2 = $member->field("id")->where("t_id = {$value['id']}")->select();
				if (!empty($all_off2)) {
					foreach ($all_off2 as $key2 => $value2) {
						$ids2[] = $value2['id'];
						$all_off3 = $member->field("id")->where("t_id = {$value2['id']}")->select();
						if (!empty($all_off3)) {
							foreach ($all_off3 as $key3 => $value3) {
								$ids3[] = $value3['id'];
							}
						}
					}
				}
			}
			$all_ids = array_merge($ids,$ids2,$ids3);
			$ids = implode(',', $ids);
			$all_ids = implode(',', $all_ids);
			//所有下线进项、出项流水
			$order_all = M("order")->field("sum(add_points) as sum_add,sum(del_points) as sum_del")->where("state=1 and userid in (".$all_ids.")")->where($map)->find();

			$order_all['ying'] = $order_all['sum_add'] - $order_all['sum_del'];

			$list[$k]['sum_del'] = $order_all['sum_del'] ? : '';
			$list[$k]['ying'] = $order_all['ying'];
			
		}
		$list_new = $this->_arraysort($list,'sum_del');
		$list_slice = array_slice($list_new, $page->firstRow, $page->listRows);

		$this->assign('show',$show);
		$this->assign('username',$username);
		$this->assign('list',$list_slice);
		$this->assign('start',$start ?  date('Y-m-d H:i:s',$start) : '');
		$this->assign('end',$end ? date('Y-m-d  H:i:s',$end) : '');
		// $this->assign('nickname',$nickname);
		$this->display();
	}

	public function pushjs_dlrate(){
		$data = I();
		$res = M('user')->where("id = {$data['uid']}")->save(array("dl_rate"=>$data['rate']));
	}



    private function _arraysort($arr,$field){
        $field_arr = array();
        foreach ($arr as $key=>$value)
        {
            $field_arr[$key] = $value[$field];
        }

        if(!empty($field_arr)&&!empty($arr))
        {
            array_multisort($field_arr,SORT_DESC,$arr);
        }
        return $arr;
    }

	//
	public function pushmoney(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

		$id = I('id');
		$push_money = M('push_money');
		$count = $push_money->where("uid = {$id}")->count();
		$page = new \Think\Page($count,10);
		$show = $page->show();
		$list = $push_money->where("uid = {$id}")->limit($page->firstRow.','.$page->listRows)->order("id ASC")->select();



		$this->assign('id',$id);
		$this->assign('show',$show);
		$this->assign('list',$list);
		$this->display();
	}

	public function edit(){
		if(IS_POST){
			if(!IS_AJAX){
				$this->error('提交方式不正确！');
			}else{
				$userid = I('userid');
				$password = I('password');
				$nickname = I('nickname');
				$username = I('username');
				$remark = I('remark');
				$data = array(
					'nickname' => $nickname,
					'username' => $username,
					'remark' => $remark
				);
				if ($password) {
					$data['password'] = md5($password);
				}
				$res = M('user')->where("id=$userid")->save($data);
				if ($res) {
					$this->success('编辑成功,跳转中~',U('Admin/Member/index'),1);
				} else {
					$this->error("编辑失败");
				}
			}
		}else{
			$id = I('id');
			$userinfo = M('user')->where("id = $id")->find();
			if (!$userinfo['username']) {
				$username = mt_rand(100000,999999);
				$user_find = M('user')->where("username={$username}")->find();
				if ($user_find) {
					$username = mt_rand(100000,999999);
				}
				$userinfo['username'] = $username;
			}

			$this->assign('userinfo',$userinfo);
			$this->display();
		}
	}
	
	public function edityhk(){
		if(IS_POST){
			if(!IS_AJAX){
				$this->error('提交方式不正确！');
			}else{
				$userid = I('userid');
				$bankrealname = I('bankrealname');
				$bankname = I('bankname');
				$bankzh = I('bankzh');
				$bankid = I('bankid');
				$data = array(
					'bankrealname' => $bankrealname,
					'bankname' => $bankname,
					'bankzh' => $bankzh,
					'bankid' => $bankid
				);
				 
				$res = M('userbank')->where("userid=$userid")->save($data);
				if ($res) {
					$this->success('编辑成功,跳转中~',U('Admin/Member/index'),1);
				} else {
					$this->error("编辑失败");
				}
			}
		}else{
			$id = I('id');
			$userinfo = M('user')->where("id = $id")->find();
			$bank = M('userbank')->where("userid = $id")->find();
			 
			if (!$userinfo['username']) {
				$username = mt_rand(100000,999999);
				$user_find = M('user')->where("username={$username}")->find();
				if ($user_find) {
					$username = mt_rand(100000,999999);
				}
				$userinfo['username'] = $username;
			}

			$this->assign('userinfo',$userinfo);
			$this->assign('bank',$bank);
			$this->display();
		}
	}
	
	public function fenedit(){
		if(IS_POST){
			if(!IS_AJAX){
				$this->error('提交方式不正确！');
			}else{
				$userid = I('userid');
				$password = I('password');
				$nickname = I('nickname');
				$username = I('username');
				$remark = I('remark');
				$data = array(
					'nickname' => $nickname,
					'username' => $username,
					'remark' => $remark
				);
				if ($password) {
					$data['password'] = md5($password);
				}
				$res = M('user')->where("id=$userid")->save($data);
				if ($res) {
					$this->success('编辑成功,跳转中~',U('Admin/Member/index'),1);
				} else {
					$this->error("编辑失败");
				}
			}
		}else{
			$id = I('id');
			$start=mktime(0,0,0,date('m'),date('d'),date('Y'));
		$end=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
		$map['addtime'] = array(array('egt',$start),array('elt',$end),'and');
		$map['status']=1;
		$map['uid']=$id;
			$userinfo = M('fenadd')->where($map)->field("SUM(money) AS mmm")->find();
			$data['nickname']=$userinfo['mmm'];
			
			$userinfom = M('fenxia')->where($map)->field("SUM(money) AS mmm")->find();
			$data['username']=$userinfom['mmm'];
			$user=M('user');
			
			$dddmap['id']=$id;
			$userrow=$user->where($dddmap)->find();
		    $mmnickname=$userrow['nickname'];
		    $integral = M('integral');
			$dmap['time'] = array(array('egt',$start),array('elt',$end),'and');
			$dmap['nickname']=$mmnickname;
			$sum_add = $integral->field("sum(points) as sum_add")->where("type = 1 and nickname like '%{$mmnickname}%'")->where($dmap)->find();
			$sum_data['add'] = $sum_add['sum_add'];
			$data['nickname']=$userinfo['mmm']+$sum_data['add'];
			$sum_xia = $integral->field("sum(points) as sum_add")->where("type = 0 and nickname like '%{$mmnickname}%'")->where($dmap)->find();
			$sum_data['xia'] = $sum_xia['sum_add'];
			$data['username']=$sum_data['xia'];
			if($data['nickname']==null||$data['nickname']==''||$data['nickname']==0){
				$data['nickname']=0;
			}
			if($data['username']==null||$data['username']==''||$data['username']==0){
				$data['username']=0;
			}
			// if (!$userinfo['username']) {
				// $username = mt_rand(100000,999999);
				// $user_find = M('user')->where("username={$username}")->find();
				// if ($user_find) {
					// $username = mt_rand(100000,999999);
				// }
				// $userinfo['username'] = $username;
			// }

			$this->assign('userinfo',$data);
			$this->display();
		}
	}
	
	

	//用户返水
	public function fs(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}
		
		$fs = M('fs_date');
		$count = $fs->count();
		$page = new \Think\Page($count,10);
		$show = $page->show();
		$list = $fs->limit($page->firstRow.','.$page->listRows)->order("id desc")->select();

		$this->assign('show',$show);
		$this->assign('list',$list);
		$this->assign('nickname',$nickname);
		$this->display();
	}

	public function fs_action(){
		$time = I('time');
		if (!$time) {
			$this->error("请选择日期");
		}
		//查询当天是否已返水
		$is_fs = M('fs_date')->where("fs_date='{$time}'")->find();
		if ($is_fs) {
			$this->error("当天已返水，请勿重复返水");
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
			if ($value['game'] == '幸运飞艇' || $value['game'] == 'xyft') {
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

			if ($value['game'] == '幸运飞艇' || $value['game'] == 'xyft') {
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

		$this->success("会员返水成功，请查看");
	}

	//用户返水明细
	public function fs_detail(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}
    	
    	$id = I('fs_id');

		$fs = M('fs_details');
		$count = $fs->where("fs_id={$id}")->count();
		$page = new \Think\Page($count,10);
		$show = $page->show();
		$list = $fs->where("fs_id={$id}")->limit($page->firstRow.','.$page->listRows)->order("id desc")->select();

		$this->assign('show',$show);
		$this->assign('list',$list);
		$this->display();
	}

	public function update_sx(){
		$id = I('id');
		if(IS_POST){
			if(!IS_AJAX){
				$this->error('提交方式不正确！');
			}else{
				$data['nickname'] = I('nickname');
				$nickname = I('nickname');
				$member = M('user')->where("username='$nickname'")->find();
				$user = M('user')->where("id={$id}")->find();
				if (!$member) {
					$this->error("更改失败，未找到上线");
				}
				if($id == $member['t_id']){
					M('user')->where("username='$nickname'")->save(array("t_id"=>0));
				}
				$res = M('user')->where("id={$id}")->save(array("t_id"=>$member['id']));

				//增加修改上线日志
				$log = array(
					'username' => session('admin')['username'],
					'type' => 3,
					'addtime' => time(),
					'content' => "修改上线，将会员「".$user['nickname']."」上线更改到「{$nickname}」下面"
				);
				M('admin_log')->add($log);

				if($res){
					$this->success('更改上线成功!',U('Admin/Member/push'),1);
				}else{
					$this->error('更改上线失败');
				}
			}
		}else{
			$this->assign("id",$id);
			$this->display();
		}
	}
	
	public function update_xx(){
		$id = I('id');
		if(IS_POST){
			if(!IS_AJAX){
				$this->error('提交方式不正确！');
			}else{
				$data['nickname'] = I('nickname');
				$nickname = I('nickname');
				$member = M('user')->where("username='$nickname'")->find();
				$user = M('user')->where("id={$id}")->find();
				if (!$member) {
					$this->error("更改失败，未找到下线");
				}
				if($member['id'] == $user['t_id']){
					M('user')->where("id={$id}")->save(array("t_id"=>0));
				}
				$res = M('user')->where("username='$nickname'")->save(array("t_id"=>$user['id']));

				//增加修改下线日志
				$log = array(
					'username' => session('admin')['username'],
					'type' => 3,
					'addtime' => time(),
					'content' => "修改下线，将会员「{$nickname}」下线更改到「".$user['nickname']."」下面"
				);
				M('admin_log')->add($log);

				if($res){
					$this->success('更改下线成功!',U('Admin/Member/push'),1);
				}else{
					$this->error('更改下线失败');
				}
			}
		}else{
			$this->assign("id",$id);
			$this->display();
		}
	}


	public function bduser(){
		$list = M('user')->where("status=1 && username = ''")->select();
		// var_dump($list);exit;
		if ($list) {
			foreach ($list as $key => $value) {
				if (!$value['username']) {
						$username = mt_rand(100000,999999);
						M('user')->where("id={$value['id']}")->save(array('username'=>$username));
				}
			}
			$this->success('执行成功,跳转中~',U('Admin/Member/index'),1);
		}
	}

	//代理分红
	public function agent_fh(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

		$id = I('id');
		$map = array();

		$map_member = array();
		$map_member['t_id'] = $id ? $id : 0;
		$map_member['is_agent'] = '1';

		$username = I('username');
		if ($username) {
			$map_member['username'] = $username;
		}

		$member = M('user');
		$count = $member->where($map_member)->count();
		$page = new \Think\Page($count,10);
		$show = $page->show();
		$list = $member->where($map_member)->order("id ASC")->select();

		$siteurl = $_SERVER['SERVER_NAME'];


		$start = strtotime(str_replace("+"," ",I('starttime'))) ? : mktime(0,0,0,date('m'),1,date('Y'));
		$end = strtotime(str_replace("+"," ",I('endtime'))) ? : mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

		// $start = strtotime(str_replace("+"," ",I('starttime')));
		// $end = strtotime(str_replace("+"," ",I('endtime')));
		
		if ($start && $end) {
			$map['time'] = array(array('egt',$start),array('elt',$end),'and');
		}


		//查看下线总数
		foreach ($list as $k => $v) {
			//直属线下列表id
			$all_off = $member->field("id")->where("t_id = {$v['id']}")->select();
			//三级下级列表id
			$ids = array();
			foreach ($all_off as $key => $value) {
				$ids[] = $value['id'];
			}
			$ids = implode(',', $ids);
			//所有下线进项、出项流水
			$order_all = M("order")->field("sum(add_points) as sum_add,sum(del_points) as sum_del")->where("state=1 and userid in (".$ids.")")->where($map)->find();

			$order_all['ying'] = $order_all['sum_add'] - $order_all['sum_del'];

			$list[$k]['sum_del'] = $order_all['sum_del'] ? : '';
			$list[$k]['ying'] = $order_all['ying'];

			//应发分红
			if ($order_all['ying'] > 0) {
				$list[$k]['fh'] = 0;
			} else {
				$list[$k]['fh'] = abs($order_all['ying']) * C('fenhong') * 0.01;
			}
		}
		$list_new = $this->_arraysort($list,'sum_del');
		$list_slice = array_slice($list_new, $page->firstRow, $page->listRows);

		$this->assign('show',$show);
		$this->assign('username',$username);
		$this->assign('list',$list_slice);
		$this->assign('start',$start ?  date('Y-m-d H:i:s',$start) : '');
		$this->assign('end',$end ? date('Y-m-d H:i:s',$end) : '');
		// $this->assign('nickname',$nickname);
		$this->display();
	}

	public function agent_fh_action(){
		$data = I();
		$t_info = M('user')->where("id = {$data['uid']}")->find();
		$data['username'] = $t_info['username'];
		$data['nickname'] = $t_info['nickname'];

		$data['fh_rate'] = C('fenhong');
		$data['addtime'] = time();

		$data['starttime'] = strtotime($data['start']);
		$data['endtime'] = strtotime($data['end']);

		$data['status'] = 1;


		$fh_res = M('user')->where("id = {$data['uid']}")->setInc("yong",$data['fh_money']);
		$res_log = M('fh')->add($data);

		if($fh_res){
			$this->success('分红成功!',U('Admin/Member/agent_fhlog'),1);
		}else{
			$this->error('分红失败');
		}
	}

	public function agent_fhlog(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

		$map_member = array();
		$username = I('username');
		if ($username) {
			$map_member['username'] = $username;
		}

		$fh = M('fh');
		$count = $fh->where($map_member)->count();
		$page = new \Think\Page($count,10);
		$show = $page->show();
		$list = $fh->where($map_member)->limit($page->firstRow.','.$page->listRows)->order("id ASC")->select();

		$this->assign('show',$show);
		$this->assign('username',$username);
		$this->assign('list',$list);
		$this->display();
	}
}
?>