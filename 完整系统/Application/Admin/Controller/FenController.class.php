<?php

namespace Admin\Controller;
use Think\Controller;

class FenController extends BaseController{
	
	public function addlist(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

		$nickname = I('nickname');
		$uid = I('uid');
		if($uid){
			$member = M('fenadd');
			$count = $member->where("uid = '{$uid}'")->count();
			$page = new \Think\Page($count,10);
			$show = $page->show();
			$list = $member->where("uid = '{$uid}'")->limit($page->firstRow.','.$page->listRows)->order("id desc")->select();
		}else{
			$member = M('fenadd');
			$count = $member->count();
			$page = new \Think\Page($count,10);
			$show = $page->show();
			$list = $member->limit($page->firstRow.','.$page->listRows)->order("id desc")->select();
		}

		$this->assign('show',$show);
		$this->assign('uid',$uid);
		$this->assign('list',$list);
		$this->display();
	}

	public function xialist(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

		$nickname = I('nickname');
		$uid = I('uid');
		if($uid){
			if (is_numeric($uid)) {
				$map['uid'] = $uid;
			} else{
				$map['uid'] = $uid;
			}
		}

		if($uid){
			$member = M('fenxia');
			$count = $member->where($map)->count();
			$page = new \Think\Page($count,10);
			$show = $page->show();
			$list = $member->where($map)->limit($page->firstRow.','.$page->listRows)->order("id desc")->select();
		}else{
			$member = M('fenxia');
			$count = $member->count();
			$page = new \Think\Page($count,10);
			$show = $page->show();
			$list = $member->limit($page->firstRow.','.$page->listRows)->order("id desc")->select();
		}

		$this->assign('list',$list);
		$this->assign('show',$show);
		$this->assign('uid',$uid);
		$this->display();
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


	public function check(){
		$id = I('id');
		$res = M('fenadd')->where("id = $id")->setField('check',1);
		if($res){
			$message  = array(
				'time'=>date('H:i:s'),
				'type' => 3,
				'content'=>"上分审核成功"
			);
			$res = $this->send($message);
			$this->success('审核成功！');
		}else{
			$this->error('审核失败！');
		}
	}

	public function ignore(){
		$id = I('id');
		$res = M('fenadd')->where("id = $id")->setField('check',2);
		if($res){
			$message  = array(
				'time'=>date('H:i:s'),
				'type' => 3,
				'content'=>"上分信息处理"
			);
			$res = $this->send($message);
			$this->success('已忽略！');
		}else{
			$this->error('忽略失败！');
		}
	}
	
	public function ignoredel(){
		$id = I('id');
		$res = M('fenadd')->where("id = $id")->delete();
		if($res){
			$message  = array(
				'time'=>date('H:i:s'),
				'type' => 3,
				'content'=>"上分信息删除"
			);
			$res = $this->send($message);
			$this->success('已删除！');
		}else{
			$this->error('删除失败！');
		}
	}
	
	public function under(){
		if(IS_POST){
			if(!IS_AJAX){
				$this->error('提交方式不正确！');
			}else{
				$userid = I('userid');
				$points = I('points');
				$type = I('type');
				
				$r = M('fenxia')->where(array("uid"=>$userid,"status"=>0))->find();
				
				if(!$r){
					$this->error('此订单已处理，请勿重复处理！');
					die;
				}
				if(!preg_match('/^[1-9]\d*$/',$points)){
					$this->error('兑换点数为正整数');
				}
				 $info = M('user')->where("id = $userid")->find();
				/*if($info['points']<$points){
					$this->error('点数不足');
				}
				$res2 = M('user')->where("id = $userid")->setDec('points',$points); */
				//$res2 = M('user')->where("id = $userid")->find();
				//$res2 = true;
				if($info){
					
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
						//$res = $this->send_fen($message);

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
	
	public function ignorexia(){
		$id = I('id');
		$r = M('fenxia')->where(array("id"=>$id,"status"=>0))->find();
		
		if(!$r){
			$this->error('此订单已处理，请勿重复处理！');
			die;
		}
		$res = M('fenxia')->where("id = $id")->setField('status',2);
		$info = M('fenxia')->where("id = $id")->find();
		$res2 = M('user')->where("id = {$info['uid']}")->setInc('points',$info['money']);
		if($res){
			$message  = array(
				'time'=>date('H:i:s'),
				'type' => 4,
				'content'=>"下分信息处理"
			);
			$res = $this->send($message);
			$this->success('已忽略！');
		}else{
			$this->error('忽略失败！');
		}
	}

	public function setwx(){
			$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

		$info = M('config')->where("id = 2")->find();
		$this->assign('info',$info);
		$this->display();
	}

	public function setzfb(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}
		$info = M('config')->where("id = 3")->find();
		$this->assign('info',$info);

		$this->display();
	}
  public function setbank(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}
		$info = M('config')->where("id = 4")->find();
		$info['kefu'] = json_decode($info['kefu'],true);
		$this->assign('info',$info);

		$this->display();
	}


	public function set1(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

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
                        $img_url = '/carousel/kefu/' . $info[file0][savepath] . $info[file0][savename];//如果上传成功则完成路径拼接
                    } else {
                        $this->error($upload->getError());//否则就是上传错误，显示错误原因
                    }
                }
				if ($checkpic != $oldcheckpic) {
                    $data['kefu'] = $img_url;
					$data['id'] = 2;
                }

                $log = array(
					'username' => session('admin')['username'],
					'type' => 5,
					'addtime' => time(),
					'content' => "修改微信收款二维码"
				);
				M('admin_log')->add($log);

				if($data['kefu']){
					$result = M('config')->where("id=2")->save($data);
				}
				if($result){
					$this->success('修改成功');
				}else{
					$this->error('修改失败');
				}
			}
		}else{
			$info = M('config')->where("id = 2")->find();
			$this->assign('info',$info);
			$this->display();
		}
	}

	public function set2(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

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
                        $img_url = '/carousel/kefu/' . $info[file0][savepath] . $info[file0][savename];//如果上传成功则完成路径拼接
                    } else {
                        $this->error($upload->getError());//否则就是上传错误，显示错误原因
                    }
                }
                  $log = array(
					'username' => session('admin')['username'],
					'type' => 6,
					'addtime' => time(),
					'content' => "修改支付宝收款二维码"
				);
				M('admin_log')->add($log);

				if ($checkpic != $oldcheckpic) {
                    $data['kefu'] = $img_url;
					$data['id'] = 3;
                }
				if($data['kefu']){
					$result = M('config')->where("id=3")->save($data);
				}
				if($result){
					$this->success('修改成功');
				}else{
					$this->error('修改失败');
				}
			}
		}else{
			$info = M('config')->where("id = 3")->find();
			$this->assign('info',$info);
			$this->display();
		}
	}
	
	public function set4(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

		if(IS_POST){
			if(!IS_AJAX){
				$this->error('提交方式不正确');
			}else{
				$data['bankname'] = $_POST['bankname'];
				$data['bankuser'] = $_POST['bankuser'];
				$data['banknum'] = $_POST['banknum'];
				$data['bankaddress'] = $_POST['bankaddress'];
				
                  $log = array(
					'username' => session('admin')['username'],
					'type' => 6,
					'addtime' => time(),
					'content' => "修改银行卡转帐收款信息"
				);
				M('admin_log')->add($log);

				$str['kefu'] = json_encode($data);
			
				if($str != ''){
					$result = M('config')->where("id=4")->save($str);
				}
				if($result){
					$this->success('修改成功');
				}else{
					$this->error('修改失败');
				}
			}
		}else{
			$info = M('config')->where("id = 4")->find();
			$info['kefu'] = json_decode($info['kefu'],true);
			$this->assign('info',$info);
			$this->display();
		}
	}

}

?>