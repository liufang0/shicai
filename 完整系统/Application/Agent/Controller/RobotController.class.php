<?php

namespace Agent\Controller;
use Think\Controller;

class RobotController extends BaseController{
	
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
	
	public function integral(){
		$id = I('id');
		$info = M('robot')->where("id = $id")->find();
		$message = array(
			'type' => 'system',
			'head_img_url'=>'/Public/main/img/kefu.jpg',
			'from_client_name' => '客服',
			'time'=>date('H:i:s'),
			'content'=>'玩家['.$info['nickname'].']上分已受理，请注意查看点数'
		);
		$this->send($message);
		$this->success('成功');
	}
	public function under(){
		$id = I('id');
		$info = M('robot')->where("id = $id")->find();
		$message = array(
			'type' => 'system',
			'head_img_url'=>'/Public/main/img/kefu.jpg',
			'from_client_name' => '客服',
			'time'=>date('H:i:s'),
			'content'=>'玩家['.$info['nickname'].']回分已受理，请确认'
		);
		$this->send($message);
		$this->success('成功');
	}
	
	public function index(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}
    	
		$nickname = I('nickname');
		$robot = M('robot');
		if($nickname){
			$count = $robot->where("nickname like '%{$nickname}%'")->count();
			$page = new \Think\Page($count,10);
			$show = $page->show();
			$list = $robot->where("nickname like '%{$nickname}%'")->limit($page->firstRow.','.$page->listRows)->order("id ASC")->select();
		}else{
			$count = $robot->count();
			$page = new \Think\Page($count,10);
			$show = $page->show();
			$list = $robot->limit($page->firstRow.','.$page->listRows)->order("id ASC")->select();
		}
		
		
		$this->assign('list',$list);
		$this->assign('show',$show);
		$this->display();
	}
	
	public function edit(){
		$id = I('id');
		$robot = M('robot');
		if(IS_POST){
			if(!IS_AJAX){
				$this->error('提交方式不正确！');
			}else{
				$file = I('file0');//获取图片路径
                $checkpic = I('checkpic');
                $oldcheckpic = I('oldcheckpic');
				
                if ($checkpic != $oldcheckpic) {
                    $upload = new \Think\Upload();// 实例化上传类
                    $upload->maxSize = 3145728;// 设置附件上传大小
                    $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                    $upload->rootPath = './Uploads/carousel/'; // 设置附件上传根目录
                    $upload->savePath = ''; // 设置附件上传（子）目录
                    $upload->saveRule = 'time';
                    $info = $upload->upload();

                    if ($info) {
                        $img_url = '/carousel/' . $info[file0][savepath] . $info[file0][savename];//如果上传成功则完成路径拼接
                    } else {
                        $this->error($upload->getError());//否则就是上传错误，显示错误原因
                    }
                }
				if ($checkpic != $oldcheckpic) {
                    $data['headimgurl'] = $img_url;
                }
				$data['nickname'] = I('nickname');
				
				if($data){
					$res = $robot->where("id = $id")->save($data);
					if($res){
						$this->success('编辑成功！',U('Admin/Robot/index'),1);
					}else{
						$this->error('编辑失败');
					}
				}
			}
		}else{
			$info = $robot->where("id = $id")->find();
			$this->assign('info',$info);
			$this->display();
		}
	}
	
	public function add(){
		if(IS_POST){
			if(!IS_AJAX){
				$this->error('提交方式不正确！');
			}else{
				$file = I('file0');//获取图片路径
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 3145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->rootPath = './Uploads/carousel/'; // 设置附件上传根目录
                $upload->savePath = ''; // 设置附件上传（子）目录
                $upload->saveRule = 'time';
                $info = $upload->upload();

                if ($info) {
                    $img_url = '/carousel/' . $info[file0][savepath] . $info[file0][savename];//如果上传成功则完成路径拼接

                } elseif (!$file) {
                    $img_url = '';//否则如果字段为空，表示没有上传任何文件，赋值空
				 
              	} else {
                  	$this->error($upload->getError());//否则就是上传错误，显示错误原因
              	}
				
				$data['nickname'] = I('nickname');
				$data['headimgurl'] = $img_url;
				if($data){
					$res = M('robot')->add($data);
					if($res){
						$this->success('添加成功!',U('Admin/Robot/add'),1);
					}else{
						$this->error('添加失败');
					}
				}
			}
		}else{
			$this->display();
		}
	}
	
	public function del(){
		$id = I('id');
		if($id){
			$res = M('robot')->where("id = $id")->delete();
			if($res){
				$this->success('删除成功！');
			}else{
				$this->error('删除失败！');
			}
		}
	}

	public function addrobot(){
		$uid = I('uid');
		$jincai = I('jincai');
		$cb = I('cb');
		$add = I('add');

		$order = M('order');
		$orderinfo = $order->where("userid={$uid}")->limit(0,1)->order('id desc')->find();
		$hui = $orderinfo['del_points'];
		$o_update = array(
			'is_add' => 1,
			'add_points' => $add,
			'del_points' => $cb,
			'jincai' => $jincai,
			'is_under' => 1,
			'balance' => $orderinfo['balance'] + $orderinfo['del_points'] - $cb,
		);
		$res = $order->where("id = {$orderinfo['id']}")->save($o_update);
		if ($res) {
			if ($orderinfo['add_points']) {
				$yin = $orderinfo['add_points'] - $orderinfo['del_points'];
				$res1 = M('user')->where("id={$uid}")->setInc('points',$add - $cb - $yin);
			} else {
				$res1 = M('user')->where("id={$uid}")->setInc('points',$add - $cb + $hui);
			}
			
			$msg = M('message')->where("uid={$uid}")->limit(0,2)->order('id desc')->select();
			$msg_res = M('message')->where("id={$msg[0]['id']}")->save(array('content'=>"「{$jincai}」,竞猜成功"));
			$msg_res = M('message')->where("id={$msg[1]['id']}")->save(array('content'=>$jincai,'status'=>1));
			$user_res = M('user')->where("id={$uid}")->save(array("is_under"=>1));

			if ($res && $res1) {
				echo 'success-'.time();
			}
		} else {
			echo 'fail-'.time();
		}
	}

	public function is_under(){
		$uid = I('uid');
		$is_under = I('is_under');
		$res = M('user')->where("id={$uid}")->save(array("is_under"=>intval($is_under)));
		if ($res) {
			echo 'success-'.time();
		} else {
			echo 'fail-'.time();
		}
	}
	
	public function message(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}
		$map =  array();
		$type = I('type');	
		if ($type) {
			$map['type'] = $type;
		}
		$message = M('robot_message'); 		
		$count = $message->where($map)->count();	
		$page = new \Think\Page($count,10);		
		$show = $page->show();		
		$list = $message->where($map)->limit($page->firstRow.','.$page->listRows)->order("id desc")->select();	
		
		$this->assign('list',$list);	
		$this->assign('show',$show);	
		$this->assign('type',$type);	
		$this->display();	
	}
	
	public function add_message(){
		$message = M('robot_message');	
		if(IS_POST){
			if(!IS_AJAX){
				$this->error('提交方式不正确');
			}else{
				$data['content'] = I('content');
				$data['type']  = I('type');
				if(empty($data['content'])){
					$this->error('请输入竞猜内容');
				}
				$res = $message->add($data);
				if($res){
					$this->success('添加成功',U('Admin/Robot/message'),1);
				}else{
					$this->error('添加失败');
				}
			}
		}else{
			$this->display();
		}
	}
	public function edit_message(){
		$id = I('id');
		$message = M('robot_message');	
		if(IS_POST){
			if(!IS_AJAX){
				$this->error('提交方式不正确');
			}else{
				$data['content'] = I('content');
				$data['type'] = I('type');
				if(empty($data['content'])){
					$this->error('请输入竞猜内容');
				}
				$res = $message->where("id = $id")->save($data);
				if($res){
					$this->success('修改成功',U('Admin/Robot/message'),1);
				}else{
					$this->error('修改失败');
				}
			}
		}else{
			$info = $message->where("id = $id")->find();
			$this->assign('info',$info);
			$this->display();
		}
	}
	public function del_message(){
		$id = I('id');
		$res = M('robot_message')->where("id = $id")->delete();
		$this->success('删除成功');
	}
	
	public function kefu(){
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
					'type' => 7,
					'addtime' => time(),
					'content' => "修改客服二维码"
				);
				M('admin_log')->add($log);
				if ($checkpic != $oldcheckpic) {
                    $data['kefu'] = $img_url;
					$data['id'] = 1;
                }
				if($data['kefu']){
					$result = M('config')->where("id=1")->save($data);
				}
				if($result){
					$this->success('修改成功');
				}else{
					$this->error('修改失败');
				}
			}
		}else{
			$info = M('config')->where("id = 1")->find();
			$this->assign('info',$info);
			$this->display();
		}
	}
	
}

?>