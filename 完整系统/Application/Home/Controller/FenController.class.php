<?php
namespace Home\Controller;
use Think\Controller;

class FenController extends BaseController{

	public function addpage(){
		$userid = session('user');
		if (C('agent_pay') == '1') {
			
			$agentinfo = M('user')->where("id = {$userid['t_id']}")->find();
			$pay = $agentinfo;

		} else {
			$info = M('config')->where("id = 2")->find();
			$pay['wx_paycode'] = $info['kefu'];
			$info1 = M('config')->where("id = 3")->find();
			$pay['zfb_paycode'] = $info1['kefu'];
			$info2 = M('config')->where("id = 4")->find();
			$pay['bank_info'] = json_decode($info2['kefu'],true);
			
		}
		

		$user = M('user')->where("id = {$userid['id']}")->find();
	
		$this->assign('pay',$pay);
		$this->assign('user',$user);
		$this->display();
	}
	public function addpage1(){
		if (IS_POST) {
			if (!IS_AJAX) {
				$this->error('提交方式不正确！');die;
			}
			if (!I('post.money')) {
				$this->error('存款金额不能为空！');die;
			}
			/*if (!I('post.username')) {
				$this->error('存款人不能为空！');die;
			}
			if (!I('post.userpay')) {
				$this->error('存款人账号不能为空！');die;
			}*/
			$userid = session('user');
			$user = M('user')->where(array('id' => $userid['id']))->find();

			M('fenadd')->add(array(
				'uid'        => $user['id'],
				'nickname'   => $user['nickname'],
				'headimgurl' => $user['headimgurl'],
				'type'       => '支付宝',
				'money'      => I('post.money'),
				'balance'    => $user['points'],
				'username'   => I('post.username'),
				'userpay'    => I('post.userpay'),
				'addtime'    => time(),
			));

			$this->success('申请成功！');die;
		} else {
			$this->display();
		}
	}
	public function addpage2(){
		if (IS_POST) {
			if (!IS_AJAX) {
				$this->error('提交方式不正确！');die;
			}
			if (!I('post.money')) {
				$this->error('存款金额不能为空！');die;
			}
			/* if (!I('post.username')) {
				$this->error('微信户名不能为空！');die;
			}
			if (!I('post.userpay')) {
				$this->error('微信账号不能为空！');die;
			} */

			$userid = session('user');
			$user = M('user')->where(array('id' => $userid['id']))->find();

			M('fenadd')->add(array(
					'uid'        => $user['id'],
					'nickname'   => $user['nickname'],
					'headimgurl' => $user['headimgurl'],
					'type'       => '微信',
					'money'      => I('post.money'),
					'balance'    => $user['points'],
					'username'   => I('post.username'),
					'userpay'    => I('post.userpay'),
					'addtime'    => time(),
			));

			$this->success('申请成功！');die;
		} else {
			$this->display();
		}
	}
	public function addpage3(){
		if (IS_POST) {
			if (!IS_AJAX) {
				$this->error('提交方式不正确！');die;
			}
			if (!I('post.money')) {
				$this->error('存款金额不能为空！');die;
			}
			/* if (!I('post.username')) {
				$this->error('存款人不能为空！');die;
			}
			if (!I('post.userpay')) {
				$this->error('存款人账号不能为空！');die;
			}
			if (!I('post.account')) {
				$this->error('银行名称不能为空！');die;
			} */
			$userid = session('user');
			$user = M('user')->where(array('id' => $userid['id']))->find();

			M('fenadd')->add(array(
					'uid'        => $user['id'],
					'nickname'   => $user['nickname'],
					'headimgurl' => $user['headimgurl'],
					'type'       => '银行卡',
					'money'      => I('post.money'),
					'balance'    => $user['points'],
					'username'   => I('post.username'),
					'userpay'    => I('post.userpay'),
					'account'    => I('post.account'),
					'addtime'    => time(),
			));

			$this->success('申请成功！');die;
		} else {
			$this->display();
		}
	}
	
	public function addpage4(){
		$userid = session('user');

		if (C('agent_pay') == '1') {
			
			$agentinfo = M('user')->where("id = {$userid['t_id']}")->find();
			$pay = $agentinfo;

		} else {
			$info = M('config')->where("id = 2")->find();
			$pay['wx_paycode'] = $info['kefu'];
			$info1 = M('config')->where("id = 3")->find();
			$pay['zfb_paycode'] = $info1['kefu'];
			$info2 = M('config')->where("id = 4")->find();
			$pay['bank_info'] = json_decode($info2['kefu'],true);
			
		}
		

		$user = M('user')->where("id = {$userid['id']}")->find();
		if($_POST['way']==1){
			$wayname='微信扫码充值';
		}elseif($_POST['way']==2){
			$wayname='支付宝扫码充值';
		}elseif($_POST['way']==3){
			$wayname='银行转账充值';
		}
		$this->assign('wayname',$wayname);
		$this->assign('way',$_POST['way']);
		$this->assign('pay',$pay);
		$this->assign('user',$user);
		$this->display();
	}
	
	//银行充值模板1
	public function zfbwx(){
		$userid = session('user');
		$money = I('money');
	
		if (C('agent_pay') == '1') {
			
			$agentinfo = M('user')->where("id = {$userid['t_id']}")->find();
			$pay = $agentinfo;

		} else {
			$info = M('config')->where("id = 2")->find();
			$pay['wx_paycode'] = $info['kefu'];
			$info1 = M('config')->where("id = 3")->find();
			$pay['zfb_paycode'] = $info1['kefu'];
			$info2 = M('config')->where("id = 4")->find();
			$pay['bank_info'] = json_decode($info2['kefu'],true);
			
		}
		

		$user = M('user')->where("id = {$userid['id']}")->find();
	
		$this->assign('pay',$pay);
		$this->assign('user',$user);
		$this->assign('money',$money);
		
		$this->display();
	}
	
	//银行充值模板1
	public function yhcz(){
		$userid = session('user');
		$money = I('money');
	
		if (C('agent_pay') == '1') {
			
			$agentinfo = M('user')->where("id = {$userid['t_id']}")->find();
			$pay = $agentinfo;

		} else {
			$info = M('config')->where("id = 2")->find();
			$pay['wx_paycode'] = $info['kefu'];
			$info1 = M('config')->where("id = 3")->find();
			$pay['zfb_paycode'] = $info1['kefu'];
			$info2 = M('config')->where("id = 4")->find();
			$pay['bank_info'] = json_decode($info2['kefu'],true);
			
		}
		

		$user = M('user')->where("id = {$userid['id']}")->find();
	
		$this->assign('pay',$pay);
		$this->assign('user',$user);
		$this->assign('money',$money);
		
		$this->display();
	}
	
	public function postal(){
		$userid = session('user');
		
		$userinfo = M('user')->where("id = {$userid['id']}")->find();
		//银行列表
		$userbank = M('userbank')->where("userid = {$userid['id']}")->select();
		foreach($userbank as $key=>$val){
			$userbank[$key]['bankids'] = substr($val['bankid'],-4,4); 
		}
		if(!$userbank || !$userbank[0]['bankid'])$this->redirect('Fen/xiapage');
		$this->assign('banklist',$userbank);
		$this->assign('userinfo',$userinfo);
		$this->display();
	}
	
	public function xiapage(){
		$userid = session('user');

		$userinfo = M('user')->where("id = {$userid['id']}")->find();
		//银行列表
		$userbank = M('userbank')->where("userid = {$userid['id']}")->select();
		foreach($userbank as $key=>$val){
			$userbank[$key]['bankrealname'] = mb_strcut($val['bankrealname'],0,3)."**"; 
			$userbank[$key]['bankids'] = substr($val['bankid'],-4,4); 
		}
		
		$this->assign('banklist',$userbank);
		$this->assign('userinfo',$userinfo);
		$this->display();
	}
	public function xiapage1(){
		if (IS_POST) {
			if (!IS_AJAX) {
				$this->error('提交方式不正确！');die;
			}
			$userid = session('user');

			M('userbank')->add(array(
				'userid' => $userid['id'],
				'bankname' => I('post.bankname'),
				'bankrealname' => I('post.bankrealname'),
				'bankzh' => I('post.bankzh'),
				'bankid' => I('post.bankid'),
			));

			$this->success('添加成功！');die;
		} else {
			$this->display();
		}
	}
	
	public function yhkxg(){
		$userid = session('user');
		if (IS_POST) {
			if (!IS_AJAX) {
				$this->error('提交方式不正确！');die;
			}

			M('userbank')->where(array("userid"=>$userid['id']))->save(array(
				'bankrealname' => I('post.bankrealname'),
				'bankname' => I('post.bankname'),
				'bankzh' => I('post.bankzh'),
				'bankid' => I('post.bankid'),
			));

			$this->success('修改成功！');die;
		} else {
			$userinfo = M('user')->where("id = {$userid['id']}")->find();
			//银行列表
			$userbank = M('userbank')->where("userid = {$userid['id']}")->select();
			foreach($userbank as $key=>$val){
				//$userbank[$key]['bankrealname'] = mb_strcut($val['bankrealname'],0,3)."**"; 
				$userbank[$key]['bankids'] = substr($val['bankid'],-4,4); 
			}
			
			$this->assign('banklist',$userbank);
			$this->assign('userinfo',$userinfo);
			$this->display();
		}
	}

	protected function send($content){
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

	public function add(){
		$id = I('id');
		$money = I('money');
		$userinfo = M('user')->where("id={$id}")->find();
		
		$one = M('fenadd')->where("uid={$id}")->find();
		
		$add = array(
			'uid' => $id,
			't_id' => $userinfo['t_id'],
			'money' => $money,
			'balance' => $userinfo['points'],
			'nickname' => $userinfo['nickname'],
			'headimgurl' => $userinfo['headimgurl'],
			'addtime' => time()
		);
		
		if(!$one){
			$add['one'] = '1';
		}
		
		
		
		$res = M('fenadd')->add($add);
		if ($res) {
			$message  = array(
				'time'=>date('H:i:s'),
				'type' => 1,
				'content'=>"上分申请"
			);
			$res = $this->send($message);
			$this->success('申请成功,等待审核，跳转中~',U('Home/Run/index'),1);
		} else {
			$this->error('申请失败，请联系客服');
		}
	}

	public function xia(){
		
		$data = I();
		$userid = session('user');
		$userinfo = M('user')->where("id={$userid[id]}")->find();
		if($userinfo['status']==0){
			$this->error('余额冻结中，请联系客服！');
		}
		/* if(md5($data['password']) !== $userinfo['txpassword']){
			$this->error('提现密码错误');
		} */
		if($userinfo['points']<$data['money']){
			$this->error('余额不足');
		}
		
		$data['balance'] = $userinfo['points'];
		$data['uid'] = $userinfo['id'];
		$data['t_id'] = $userinfo['t_id'];
		$data['nickname'] = $userinfo['nickname'];
		$data['headimgurl'] = $userinfo['headimgurl'];
		$data['type'] = 3;
		$data['addtime'] = time();
		 
	
		$res = M('fenxia')->add($data);
		
		
		$a = M("user")->where("id = {$data['uid']}")->setDec("points",$data['money']);
		if ($res) {
			$message  = array(
				'time'=>date('H:i:s'),
				'type' => 2,
				'content'=>"下分申请"
			);
			$res = $this->send($message);
			$r=array(
				'status'=>1,
				'info'=>''
			);
			echo json_encode($r);
			die;
		} else {
			$r=array(
				'status'=>0,
				'info'=>'申请失败，请联系客服'
			);
			echo json_encode($r);
			die;
 
		}
	}
	
	//提现记录
	public function withdrawList(){
		$this->display();
	}
	
	public function addbanks(){
		
		$userid = session('user');

		$userinfo = M('user')->where("id = {$userid['id']}")->find();
		
		
		if(IS_POST){
			$datas['bankcode'] = I('bankcode');//银行代码
			$datas['bankname'] = I('bankname');//银行名称
			$datas['bankrealname'] = I('bankrealname');//真实姓名
			$datas['bankzh'] = I('bankzh');//银行支行
			$datas['bankid'] = I('bankid');//银行卡号
			$datas['userid'] = $userid['id'];//用户ID
			
			$res = M('userbank')->add($datas);
			if ($res) {
				$this->success('添加成功');
			} else {
				$this->error('添加失败');
			}
			
		}else{			
			$this->assign("user",$userinfo);
			$this->display();
		}
		
		
	}
}
?>