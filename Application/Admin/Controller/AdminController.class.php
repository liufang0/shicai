<?php

namespace Admin\Controller;
use Think\Controller;

class AdminController extends BaseController{
	
	public function index(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

		$admin = M('admin');
		$count = $admin->count();
		$page = new \Think\Page($count,10);
		$show = $page->show();
		$list = $admin->limit($page->firstRow.','.$page->listRows)->order("id ASC")->select();
		
		$this->assign('show',$show);
		$this->assign('list',$list);
		$this->display();
	}
	
	public function disable(){
		$id = I('id');
		$res = M('admin')->where("id = $id")->setField('status',0);
		if($res){
			$this->success('禁用成功！');
		}else{
			$this->error('禁用失败！');
		}
	}
	
	
	public function endisable(){
		$id = I('id');
		$res = M('admin')->where("id = $id")->setField('status',1);
		if($res){
			$this->success('启用成功！');
		}else{
			$this->error('启用失败！');
		}
	}
	
	public function del(){
		$id = I('id');
		$res = M('admin')->where("id = $id")->delete();
		if($res){
			$this->success('删除成功！');
		}else{
			$this->error('删除失败！');
		}
	}
	
	

	public function edit(){
		if(IS_POST){
			if(!IS_AJAX){
				$this->error('提交方式不正确！');
			}else{
				$id = I('id');
				$password = I('password');
				$username = I('username');
				$data = array(
					'username' => $username,
				);
				if ($password) {
					$data['password'] = md5($password);
				}
				$res = M('admin')->where("id=$id")->save($data);
				if ($res) {
					$this->success('编辑成功,跳转中~',U('Admin/Admin/index'),1);
				} else {
					$this->error("编辑失败");
				}
			}
		}else{
			$id = I('id');
			$userinfo = M('admin')->where("id = $id")->find();
			
			$this->assign('userinfo',$userinfo);
			$this->display();
		}
	}

	public function add(){
		if(IS_POST){
			if(!IS_AJAX){
				$this->error('提交方式不正确！');
			}else{
				$password = I('password');
				$username = I('username');

				$data = array(
					'username' => $username,
					'role' => I('role'),
					'status' => 1
				);
				if (!$password) {
					$this->error('请输入密码');
				}
				$data['password'] = md5($password);
				$res = M('admin')->add($data);
				if ($res) {
					$this->success('新增成功,跳转中~',U('Admin/Admin/index'),1);
				} else {
					$this->error("新增失败");
				}
			}
		}else{
			$this->display();
		}
	}


	public function log(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

    	$map = array();

    	$type = I('type');
    	if ($type > 0) {
    		$map['type'] = $type;
    	}

    	$username = I('username');
    	if ($username) {
    		$admin = M('admin_log');
			$count = $admin->where("username like '%{$username}%'")->where($map)->count();
			$page = new \Think\Page($count,10);
			$show = $page->show();
			$list = $admin->where("username like '%{$username}%'")->where($map)->limit($page->firstRow.','.$page->listRows)->order("id desc")->select();
    	} else{
    		$admin = M('admin_log');
			$count = $admin->where($map)->count();
			$page = new \Think\Page($count,20);
			$show = $page->show();
			$list = $admin->where($map)->limit($page->firstRow.','.$page->listRows)->order("id desc")->select();
    	}

		
		$this->assign('username',$username);
		$this->assign('show',$show);
		$this->assign('type',$type);
		$this->assign('list',$list);
		$this->display();
	}

	public function yushe(){
		
	}
	
}

?>