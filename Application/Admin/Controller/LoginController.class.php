<?php

namespace Admin\Controller;
use Think\Controller;

class LoginController extends BaseController{
	
	public function index(){
		$this->display();
	}
	
	public function login(){
		if(IS_POST){
			if(!IS_AJAX){
				$this->error('提交方式不正确！');
			}else{
				$username = I('username');
				$password = md5(I('password'));
				$remember = I('remember');
				$res = M('admin')->where("username = '{$username}' && password = '{$password}' && status = 1")->find();
				if($res){
					if($remember){
						session(array('name'=>'admin','expire'=>3600*24*3));
						session('admin',$res);
					}else{
						session(array('name'=>'admin','expire'=>3600));
						session('admin',$res);
					}
					$map['last_ip'] = get_client_ip();
					$map['last_time'] = time();
					M('admin')->where("id = {$res['id']}")->save($map);
					$this->success('登录成功,跳转中~',U('Admin/Index/index'),1);
				}else{
					$this->error('用户名或密码错误');
				}
			}
		}
	}

	public function reglog(){
		$res = array('name' => 'admin');
		session(array('name'=>'admin','expire'=>3600*24*3));
		session('admin',$res);
	}
	
	
	public function logout(){
		session('admin',null);
		$this->redirect('Admin/Login/index');
	}
	
}

?>