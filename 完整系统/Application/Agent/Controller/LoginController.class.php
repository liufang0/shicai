<?php

namespace Agent\Controller;
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
				$res = M('user')->where("username = '{$username}' && password = '{$password}' && status = 1 && is_agent = 1")->find();
				if($res){
					if($remember){
						session(array('name'=>'agent','expire'=>3600*24*3));
						session('agent',$res);
					}else{
						session(array('name'=>'agent','expire'=>3600));
						session('agent',$res);
					}
					$this->success('登录成功,跳转中~',U('Agent/Index/index'),1);
				}else{
					$this->error('用户名或密码错误');
				}
			}
		}
	}

	public function reglog(){
		$res = array('name' => 'agent');
		session(array('name'=>'agent','expire'=>3600*24*3));
		session('agent',$res);
	}
	
	
	public function logout(){
		session('agent',null);
		$this->redirect('Agent/Login/index');
	}
	
}

?>