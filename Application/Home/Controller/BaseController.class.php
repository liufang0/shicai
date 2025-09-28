<?php
namespace Home\Controller;
use Think\Controller;

class BaseController extends Controller{
	
	public function _initialize(){
	    getConfigs();
	   // die( C("CAIJI_KEY"));
		//检测登录状态
		$userid = session('user');
		if (C('is_weixin') == '1' && is_weixin()) {
			if(CONTROLLER_NAME!='Index'){
				if(empty($userid['id'])){
					$this->redirect('Index/wxlogin');
				}
			}
		} else {
			if(CONTROLLER_NAME!='Index' && CONTROLLER_NAME!='Run'){
				if(empty($userid['id'])){
					$this->redirect('Home/Index/login');
				}
			}
		}
		
		if (isset($userid['id'])) {
			$userinfo = M('user')->where("id = {$userid['id']}")->find();
			if (!$userinfo) {
				session(null);
				$this->redirect('Home/Index/index');
			}
		} else {
			$userinfo = array();
		}
		$this->assign('userinfo',$userinfo);
		$this->assign('version',VERSION);
		
		
	}
}
?>