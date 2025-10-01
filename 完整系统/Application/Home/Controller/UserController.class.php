<?php
namespace Home\Controller;
use Think\Controller;

class UserController extends BaseController{


	//会员中心首页
	public function index(){
		$userid = session('user');
		$order = M('order');
		$map['state'] = 1;
        $map['userid'] = $userid['id'];
		$points_tj = $order->field("count(id) as count,sum(add_points) as sum_add,sum(del_points) as sum_del")
            ->where($map)
            ->find();
		$points_tj['sum_add'] = $points_tj['sum_add'] ? $points_tj['sum_add'] : 0;
		$points_tj['sum_del'] = $points_tj['sum_del'] ? $points_tj['sum_del'] : 0;
		$points_tj['ying'] = $points_tj['sum_add'] - $points_tj['sum_del'];
		$this->assign('points_tj', $points_tj);
		
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

		

		$userinfo = M('user')->where("id = {$userid['id']}")->find();

		//下线总数
		$userinfo['t_account'] = M('user')->where("t_id = {$userid['id']}")->count() ? : 0;
		
		//今日盈亏
		$map = array();
		$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		$endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
	
		$map['time'] = array(array('egt',$beginToday),array('elt',$endToday),'and');
		$map['state'] = 1;
		$map['userid'] = $userid['id'];

		$yinkui = M('order')->field("sum(add_points) as sum_add,sum(del_points) as sum_del")->where($map)->find();
		
		$userinfo['ying'] = $yinkui['sum_add'] - $yinkui['sum_del'];
		$userinfo['sum_add'] = $yinkui['sum_add'];

		//今日推广佣金
		$map_y['time'] = array(array('egt',$beginToday),array('elt',$endToday),'and');
		$map_y['t_uid'] =  $userid['id'];
		$yong = M('push_money')->where($map_y)->field("sum(money) as money")->find();

		$userinfo['yong_today'] = $yong['money'] ? : 0;
		$kefu = M('config')->where("id = 1")->find();
		$this->assign('kefu',$kefu);

		$is_weixin = is_weixin();
		$this->assign('is_weixin',$is_weixin);

		$this->assign("user",$userinfo);
		if (C('index_page') == '1') {
            $this->display("index_1");
        } else {
            $this->display();
        }
	}

	//佣金转钱包
	public function yzqpage(){
		$this->display();
	}

	//佣金转钱包
	public function fzqpage(){
		$this->display();
	}
	
	//打开额度转换页面
	public function edzhpage(){
		$username=$_SESSION['user']['username'];
		Vendor('ApiBi.Biapi', '' ,'.class.php');
		$api=new \Biapi();
		$res=$api->balances('AG',$username);
		
		$this->assign("balance",$res);
		$this->display();
	}
	
	//进行额度转换
	public function edzh(){
		$zztype = isset($_POST['zz_type']) ? $_POST['zz_type'] : '';
		$yy = isset($_POST['zz_money']) ? trim($_POST['zz_money']) : '';
		//echo $zztype.$yy."dd";exit;
		
		$userid = session('user');
		$user = M('user')->where("id = {$userid['id']}")->find();
		$username=$user['username'];

		$conver = doubleval($yy);
		if (($zztype=='12' || $zztype=='11') && ($conver > $user["points"]))
		{
			$this->error('转账金额不能大于账户余额，请重新输入。');
			exit;
		}


		$gametype='';
		if($zztype=='12' || $zztype=='22'){
			$gametype='AG';	
			if($zztype=='12'){
				$about="主账户->AG娱乐";
			}else if($zztype=='22'){
				$about="AG娱乐->主账户";
			}
		}else if($zztype=='11' || $zztype=='21'){
			$gametype='BB';	
			if($zztype=='11'){
				$about="主账户->BB娱乐";
			}else if($zztype=='21'){
				$about="BB娱乐->主账户";
			}

		}else if($zztype=='13' || $zztype=='23'){
			$gametype='IBC';	

			if($zztype=='13'){
				$about="主账户->沙巴体育";
			}else if($zztype=='23'){
				$about="沙巴体育->主账户";
			}
		}

		Vendor('ApiBi.Biapi', '' ,'.class.php');
		$mon=$user['points'];
		if($zztype == '11' || $zztype == '12' || $zztype == '13' ){
			
			if($yy>$mon){
				$this->error('主钱包余额不足');
				exit;
			}
			$res=new \BiApi();
			
			$result=$res->zzmoney($gametype,$username,'IN',$yy);
			//var_dump($result);die;
			if($result){//充值成功
				$res_money=$mon-$yy;
				$billNO = $username.date('YmdHis',time());
				//$about="转入".$gametype;
				$sql1		=	"insert into money_log(`user_id`,`order_num`,`about`,`update_time`,`type`,`order_value`,`assets`,`balance`) values('".$row["user_id"]."','$billNO','$about','".date("Y-m-d H:i:s")."','真人转账','".abs($yy)."','".$row["money"]."',".($row["money"]-abs($yy)).")";
				//$mysqli->query($sql1);
				$sql2		=	"insert into zz_info(uid,username,old_money,status,amount,new_money,type,info,actionTime,result,billNO) values(".$row["user_id"].",'$username',$mon,1,$yy,$res_money,".$_REQUEST["zz_type"].",'转入沙巴', ".time().",'转帐成功','$billNO')";
				//$mysqli->query($sql2);	

				M('user')->where("id = {$userid['id']}")->setField('points',$res_money);
				
				$this->success('转入成功，跳转中~',U('Home/User/edzhpage'),1);
				exit;
			}else{
				$this->error('转入失败');
				exit;
			}
				
				
		}else if($zztype == '21' || $zztype == '22' || $zztype == '23' ){
			$res=new \BiApi();
			
			$result=$res->zzmoney($gametype,$username,'OUT',$yy);
			if($result){//提现成功
				$res_money=$mon+$yy;
				//echo $mon.'#'.$yy;die;
				$billNO = $username.date('YmdHis',time());
				//$about=$gametype."提现";
				$sql1		=	"insert into money_log(`user_id`,`order_num`,`about`,`update_time`,`type`,`order_value`,`assets`,`balance`) values('".$row["user_id"]."','$billNO','$about','".date("Y-m-d H:i:s")."','真人转账','".abs($yy)."','".$row["money"]."',".($row["money"]-abs($yy)).")";
				//$mysqli->query($sql1);
				$sql2		=	"insert into zz_info(uid,username,old_money,status,amount,new_money,type,info,actionTime,result,billNO) values(".$row["user_id"].",'$username',$mon,1,$yy,$res_money,".$_REQUEST["zz_type"].",'PT提现', ".time().",'转帐成功','$billNO')";
				//$mysqli->query($sql2);	

				M('user')->where("id = {$userid['id']}")->setField('points',$res_money);

				$this->success('转出成功，跳转中~',U('Home/User/edzhpage'),1);
				exit;
			}else{
				$this->error('转出失败');
				exit;
			}
		}
	}

	public function uppwd(){
		$this->display();
	}

	

	//修改密码
	public function dopwd(){
		$old_pwd = I('old_pwd');
		$pwd = I('pwd');
		$user = session('user');
		$userinfo = M('user')->where("id = {$user['id']}")->find();
		if ($userinfo['password'] != md5($old_pwd)) {
			$this->error("旧密码不正确");
		}

		$res = M('user')->where("id = {$user['id']}")->save(array('password'=>md5($pwd)));

		if ($res) {
			$this->success('修改密码成功，跳转中~',U('Home/User/index'),1);
		} else {
			$this->error('修改密码失败');
		}

	}

	//佣金转钱包
	public function yzq(){
		$money = I('money');
		$uid = session('user');
		$user = M('user')->where("id = {$uid['id']}")->find();

		if (intval($user['yong']) < intval($money)) {
			$this->error("佣金不足，请确认");
		}

		$User = M('user');
		$User->startTrans();
		$res1 =  $User->where("id = {$uid['id']}")->setDec('yong',$money);
		$res2 =  $User->where("id = {$uid['id']}")->setInc('points',$money);

		if ($res1 && $res2) {
			$User->commit(); 
			$this->success('转入成功，跳转中~',U('Home/User/index'),1);
		} else {
			$User->rollback(); 
			$this->error('转入失败，请重试');
		}
	}

	//佣金转钱包
	public function fzq(){
		$money = I('money');
		$uid = session('user');
		$user = M('user')->where("id = {$uid['id']}")->find();

		if (intval($user['fanshui']) < intval($money)) {
			$this->error("返水金额不足，请确认");
		}

		$User = M('user');
		$User->startTrans();
		$res1 =  $User->where("id = {$uid['id']}")->setDec('fanshui',$money);
		$res2 =  $User->where("id = {$uid['id']}")->setInc('points',$money);

		if ($res1 && $res2) {
			$User->commit(); 
			$this->success('转入成功，跳转中~',U('Home/User/index'),1);
		} else {
			$User->rollback(); 
			$this->error('转入失败，请重试');
		}
	}

	//我的下线
	public function offline(){
		$id = I('id');
		// $userinfo = session('user');
		$member = M('user');
		$count = $member->where("t_id = {$id}")->count();
		$page = new \Think\Page($count,10);
		$show = $page->show();
		$list = $member->where("t_id = {$id}")->limit($page->firstRow.','.$page->listRows)->order("id ASC")->select();
		
		
		foreach ($list as $key => $value) {
			$yong = M('push_money')->where("id={$value['id']}")->field("SUM(money) as money")->find();
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
			$list[$key]['ying'] = number_format($user_order['sum_add'] - $user_order['sum_del'],2);
			
			$money = M('user')->field("points")->where("id = {$value['id']}")->find();
			$list[$key]['money'] = $money['points'];
			
			$user_yong = M('push_money')->field("sum(money) as sum_yong")->where("uid = {$value['id']}")->where($map)->find();
			$list[$key]['yong2'] = $user_yong['sum_yong'];
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
		$this->assign('total_money',$total_money);
		$this->assign('start',$start ?  date('Y-m-d H:i:s',$start) : '');
		$this->assign('end',$end ? date('Y-m-d H:i:s',$end) : '');
		if (C('index_page') == '1') {
            $this->display("offline_1");
        } else {
            $this->display();
        }
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

	//我的下线下注记录
	public function offxz(){
		$time = I('time');
		$map = array();

		$map['state'] = 1;
		$time = I('time');
		$start = strtotime(I('starttime'));
		$end = strtotime(I('endtime'));
		
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

		if ($start && $end) {
			$map['time'] = array(array('egt',$start),array('elt',$end),'and');
		}

		$id = I('id');
		$map['is_under'] = 1;
		$map['userid'] = $id;
		$order = M('order');
		// $integral = M('integral');
		$count = $order->where($map)->count();
		$page = new \Think\Page($count,10);
		$show = $page->show();
		$list = array();
		$tj = $order->field("sum(add_points) as sum_add,sum(del_points) as sum_del")->where($map)->find();
		$list = $order->where($map)->limit($page->firstRow.','.$page->listRows)->order("id DESC")->select();

		$this->assign('list',$list);
		$this->assign('time',$time);
		$this->assign('id',$id);
		$this->assign('show',$show);
		$this->assign('tj',$tj);
		$this->assign('start',$start ?  date('Y-m-d',$start) : '');
		$this->assign('end',$end ? date('Y-m-d',$end) : '');
		$this->display();
	}

	//我的下线充值记录
	public function offcz(){
		$id = I('id');
		$map['uid'] = $id;
		
		$integral = M('integral');
		$time = I('time');
		$map = array();
		$map['uid'] = $id;
		$start = strtotime(I('starttime'));
		$end = strtotime(I('endtime'));
		
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

		if ($start && $end) {
			$map['time'] = array(array('egt',$start),array('elt',$end),'and');
		}

		
		$sum_data = array(
			'add' => 0,
			'xia' => 0,
		);
	

		$count = $integral->where($map)->count();
		$page = new \Think\Page($count,10);
		$show = $page->show();
		$sum_add = $integral->field("sum(points) as sum_add")->where("type = 1")->where($map)->find();
		$sum_data['add'] = $sum_add['sum_add'];
		$sum_xia = $integral->field("sum(points) as sum_add")->where("type = 0")->where($map)->find();
		$sum_data['xia'] = $sum_xia['sum_add'];
		$list = $integral->where($map)->limit($page->firstRow.','.$page->listRows)->order("id DESC")->select();
	
		for($i=0;$i<count($list);$i++){
			$list[$i]['user'] = M('user')->where("id = {$list[$i]['uid']}")->find();
		}

		$startline =  $start ? date('Y-m-d',$start) : '';
		$endline =  $start ? date('Y-m-d',$end) : '';

		$this->assign('nickname',$nickname);
		$this->assign('start',$startline);
		$this->assign('end',$endline);
		$this->assign('list',$list);
		$this->assign('id',$id);
		$this->assign('show',$show);
		$this->assign('sum_data',$sum_data);
		$this->assign('time',$time);
		$this->display();
	}

	//大头支付
	public function datoupay(){
		$uid = session('user')['id'];
		$member = M('integral');
		$count = $member->field("sum(points) as sum_points,count(id) as count")->where("type = 1  and uid = {$uid}")->find();

		$page = new \Think\Page($count['count'],10);
		$show = $page->show();
		$list = $member->where("type = 1 and uid = {$uid}")->limit($page->firstRow.','.$page->listRows)->order("id desc")->select();

		$this->assign('show',$show);
		$this->assign('count',$count);
		$this->assign('list',$list);
		$this->display();
	}

	
	public function datoupay2(){
		$userid = session('user');
		$userinfo = M('user')->where("id = {$userid['id']}")->find();

		
		$this->assign("user",$userinfo);
		$this->display();
	}

	//充值记录
	public function fenadd(){
		$uid = session('user')['id'];
		$member = M('integral');
		$count = $member->field("sum(points) as sum_points,count(id) as count")->where("type = 1  and uid = {$uid}")->find();

		$page = new \Think\Page($count['count'],10);
		$show = $page->show();
		$list = $member->where("type = 1 and uid = {$uid}")->limit($page->firstRow.','.$page->listRows)->order("id desc")->select();

		$this->assign('show',$show);
		$this->assign('count',$count);
		$this->assign('list',$list);
		$this->display();
	}

	//提现记录
	public function fenxia(){
		$uid = session('user')['id'];
		$member = M('fenxia');
		$count = $member->field("sum(points) as sum_points,count(id) as count")->where("type = 3  and uid = {$uid}")->find();
		
		$page = new \Think\Page($count['count'],10);
		$show = $page->show();
		$list = $member->where("type = 3 and uid = {$uid}")->limit($page->firstRow.','.$page->listRows)->order("id desc")->select();

		$this->assign('show',$show);
		$this->assign('list',$list);
		$this->assign('count',$count);
		
		$this->display();
	}

	public function xiazhu(){
			$t = I('t');
		$pkdata = F('幸运飞艇data');
		$map = array();
		if($t == 1){
			$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
			$endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
		}
		if($t == 2){
			$beginToday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
			$endToday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
		}
		if($t == 3){
			$beginToday=mktime(0,0,0,date('m'),date('d')-2,date('Y'));
			$endToday=mktime(0,0,0,date('m'),date('d')-1,date('Y'))-1;
		}
		if($t == 4){
			$beginToday=0;
			$endToday=0;
		}
		if ($beginToday and $endToday) {
			$map['time'] = array(array('egt',$beginToday),array('elt',$endToday),'and');
		}
		$userinfo = session('user');
		$map['state'] = 1;
		$map['userid'] = $userinfo['id'];
		
		
		$order = M('order');
		$count = $order->where($map)->count();
		$points_tj = $order->field("count(id) as count,sum(add_points) as sum_add,sum(del_points) as sum_del")->where($map)->find();
		$points_tj['ying'] = $points_tj['sum_add'] - $points_tj['sum_del'];
		$page = new \Think\Page($points_tj['count'],6);
		
		$show = $page->show();
		$list = $order->field("*")->where($map)->limit($page->firstRow.','.$page->listRows)->order("id DESC")->select();

		$number = array();
		for($i=0;$i<count($list);$i++){

			if(!in_array($list[$i]['number'], $number)){
				$number[] = $list[$i]['number'];
			}
			for($a=0;$a<count($number);$a++){
				if($list[$i]['number']==$number[$a]){
					$list1[$a]['number'] = $number[$a];
					$list1[$a]['order'][] = $list[$i];
				}
			}
		}
		
		//print_r($list1);
		$this->assign('list1',$list1);
		$this->assign('state',F('state'));
		$this->assign('number',$pkdata['next']['periodNumber']);
		$this->assign('list',$list);
		$this->assign('points_tj',$points_tj);
		$this->assign('show',$show);
		$this->assign('today',mktime(0,0,0,date('m'),date('d'),date('Y')));
		$this->assign('t',$t);
		$this->display();
	}
	
	//设置页面
	public function setting(){
		$this->display();
	}
	
	//头像设置
	public function txsetting(){
		
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

		$userid = session('user');

		$userinfo = M('user')->where("id = {$userid['id']}")->find();

		$is_weixin = is_weixin();
		
		if(IS_POST){

			$datas['headimgurl'] = I('path');
			

			$userinfos = M('user')->where("id = {$userid['id']}")->save($datas);
				
		
			if($userinfos){
				$this->success('修改成功', U('/Home/User/txsetting'), 1);
			}
			
		}else{
			
			$this->assign('is_weixin',$is_weixin);

			$this->assign("user",$userinfo);
			
			$this->display();
		}
		
		
	}
	
	//真实姓名设置
	public function xmsetting(){
		$userid = session('user');
		if(IS_POST){
			
			$data['realname'] = I('zxxm');
			
			$userinfo = M('user')->where("id = {$userid['id']}")->save($data);
			if($userinfo){
				$this->success('填写成功', U('/Home/User/xmsetting'), 1);
			}
			
		}else{
			
			$userinfo = M('user')->where("id = {$userid['id']}")->find();
			$this->assign("user",$userinfo);
			$this->display();
		}
		
	}
	
	//昵称设置
	public function ncsetting(){
		$userid = session('user');
		if(IS_POST){
			
			$data['nickname'] = I('nc');
			
			$userinfo = M('user')->where("id = {$userid['id']}")->save($data);
			if($userinfo){
				$this->success('填写成功', U('/Home/User/ncsetting'), 1);
			}
			
		}else{
			
			$userinfo = M('user')->where("id = {$userid['id']}")->find();
			$this->assign("user",$userinfo);
			$this->display();
		}
	}
	
	//密码修改
	public function mmsetting(){
		$userid = session('user');
		
		$userinfo = M('user')->where("id = {$userid['id']}")->find();
		
		if(IS_POST){
			
			$data['ymm'] = I('ymm');
			$datas['password'] = MD5(I('xmm'));
			
			$ymm = MD5($data['ymm']);
			
			if($ymm != $userinfo['password']){
				$this->error('原密码错误，请重新输入');
			}else{
				$userinfos = M('user')->where("id = {$userid['id']}")->save($datas);
				
			}
		
			if($userinfos){
				$this->success('修改成功', U('/Home/User/mmsetting'), 1);
			}
			
		}else{
			
			$this->display();
		}
	}
	
	//提现密码修改
	public function txmmsetting(){
		$userid = session('user');
		
		$userinfo = M('user')->where("id = {$userid['id']}")->find();
		if(IS_POST){
			$data['ymm'] = I('ymm');
			$datas['txpassword'] = MD5(I('xmm'));
			$ymm = MD5($data['ymm']);
			//提现密码为空
			if(empty($userinfo['txpassword'])){
				if($ymm != $userinfo['password']){
					$this->error('原密码错误，请重新输入');
				}else{
					$userinfos = M('user')->where("id = {$userid['id']}")->save($datas);
				}
			}else{
				if($ymm != $userinfo['txpassword']){
					$this->error('原密码错误，请重新输入');
				}else{
					$userinfos = M('user')->where("id = {$userid['id']}")->save($datas);
				}
				
				
			}
			
			if($userinfos){
				$this->success('修改成功', U('/Home/User/mmsetting'), 1);
			}
			
		}
		
		$this->display();
	}
	
	//手机绑定
	public function sjsetting(){
		$userid = session('user');
		$userinfo = M('user')->where("id = {$userid['id']}")->find();
		
		$this->assign('user',$userinfo);
		
		$this->display();
	}
	
	//联系客服
	public function kfsetting(){
				
		
		$this->display();
	}
	
	//关于我们
	public function gysetting(){
				
		
		$this->display();
	}
	
	//优惠活动
	public function youhui(){
		$this->display();
	}
	
	//公告
	public function gonggao(){
		$message = M('xxmessage')->select();
		foreach($message as $key=>$val){
			$message[$key]['substrsa'] = substr($val['content'],0,10);
			
		}
		$this->assign('list',$message);
		
		$this->display();
	}
	
	//客户端下载
	public function appkhd(){
		$this->display();
	}
	
	//发送手机验证码
	public function smscode(){
		$userid = session('user');
		$userinfo = M('user')->where("id = {$userid['id']}")->find();
	
		if(IS_POST){
			$phone = I('phone');
			
			//查询此号码有没给绑定
			$resphone = M('user')->where("phone = {$phone}")->find();
			
			if($resphone){
				$this->error('此手机已被绑定');
			}
			
			//生成六位随机数字
			$code = rand(100000,999999);
			
			//写入数据
			
			$data = [
				'username' => $userinfo['username'],
				'phone' => $phone,
				'code' => $code,
				'add_time' => time(),
				'status' => 0
			];
			
			$res = M('smscode')->add($data);
			

			$statusStr = array(
			"0" => "短信发送成功",
			"-1" => "参数不全",
			"-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
			"30" => "密码错误",
			"40" => "账号不存在",
			"41" => "余额不足",
			"42" => "帐户已过期",
			"43" => "IP地址限制",
			"50" => "内容含有敏感词"
			);
			$smsapi = "http://api.smsbao.com/";
			$user = "29290149"; //短信平台帐号
			$pass = md5("qwer789"); //短信平台密码
			$content="【富美验证】您的验证码是：".$code.",请在3分钟内绑定！";//要发送的短信内容
			$phones = $phone;//要发送短信的手机号码
			$sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phones."&c=".urlencode($content);
			
			$result =file_get_contents($sendurl) ;
			
			if($result == 0){
				$this->success($statusStr[$result]);
			}else{
				$this->error($statusStr[$result]);
			}
			
		}	
	}
	
	public function banding(){
		$userid = session('user');
		$userinfo = M('user')->where("id = {$userid['id']}")->find();
		
		if(IS_POST){
			$phone = I('phone');
			$code = I('code');
			
			//查询是否有验证码信息
			$ressms = M('smscode')->where(array('username'=>$userinfo['username'],'phone'=>$phone,'code'=>$code))->find();
		
		
			if($ressms){
				if($ressms['status'] == 0){
					//更新用户数据
					$datas['phone'] = $phone;
					$res = M('user')->where("id = {$userinfo['id']}")->save($datas);
					
					//更新验证码状态
					$datab['status'] = 1;
					$resa = M('smscode')->where(array('username'=>$userinfo['username'],'phone'=>$phone,'code'=>$code))->save($datab);
					
					if($res){
						$this->success('绑定成功');
					}else{
						$this->error('绑定失败，请联系客服');
					}
				}else{
					$this->error('验证码已过期，请重新获取');
				}
				
			}
			
		}
		
	}
	
	public function get_number(){
		$userid = session('user');
		$data = M('user')->where("id = {$userid['id']}")->field("points")->find();
 
		$list = M('number')->where("game='".$_GET['type']."'")
            ->order("id DESC")
            ->find();
			
		$number = M('number')->where("game='".$_GET['type']."'")->limit(0,1)->order("id desc")->find();
		$n_awardTime = strtotime($number["awardtime"]) + 300;
		$time = ($n_awardTime - time());
		$list['over'] = $time;
		$list['points'] = $data['points'];
		$this->success($list);
	}
	
	public function get_money(){
		$userid = session('user');
		$data = M('user')->where("id = {$userid['id']}")->find();
		
		$this->success($data);
	}
	
}
?>